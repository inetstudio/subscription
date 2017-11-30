<?php

namespace InetStudio\Subscription\Services;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use DrewM\MailChimp\Webhook;
use DrewM\MailChimp\MailChimp;
use InetStudio\Subscription\Events\EmailDeletedEvent;
use InetStudio\Subscription\Events\EmailCleanedEvent;
use InetStudio\Subscription\Events\EmailPendingEvent;
use InetStudio\Subscription\Models\SubscriptionModel;
use InetStudio\Subscription\Events\EmailUnsubscribedEvent;
use InetStudio\Subscription\Contracts\SubscriptionServiceContract;

class MailchimpService implements SubscriptionServiceContract
{
    private $service;
    private $subscriptionList;
    private $interests;
    private $userData = [
        'personal' => 'merge_fields',
        'groups' => 'interests',
    ];

    /**
     * MailchimpService constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->service = new MailChimp($config['api_key']);
        $this->subscriptionList = $config['subscribers_list'];
        $this->interests = $this->array_change_key_case_unicode($config['interests'], CASE_LOWER);
    }

    /**
     * Подписываем пользователя на рассылку.
     *
     * @param SubscriptionModel $subscription
     * @return bool
     */
    public function subscribe(SubscriptionModel $subscription): bool
    {
        $exist = $this->checkUser($subscription->email);

        $this->caseAction($subscription, $exist);

        return true;
    }

    /**
     * Обновляем информацию подписчика.
     *
     * @param SubscriptionModel $subscription
     * @return bool
     */
    public function update(SubscriptionModel $subscription): bool
    {
        $prevEmail = $subscription->getOriginal('email', $subscription->email);

        $exist = $this->checkUser($prevEmail);

        $this->caseAction($subscription, $exist);

        return true;
    }

    /**
     * Отписываем пользователя от рассылки.
     *
     * @param SubscriptionModel $subscription
     * @return bool
     */
    public function unsubscribe(SubscriptionModel $subscription): bool
    {
        $subscriberHash = $this->service->subscriberHash($subscription->email);

        $exist = $this->checkUser($subscription->email);

        if ($exist) {
            $this->service->patch('lists/'.$this->subscriptionList.'/'.$subscriberHash, [
                'status' => 'unsubscribed'
            ]);
        }

        event(new EmailUnsubscribedEvent($subscription));

        return true;
    }

    /**
     * Удаляем пользователя из листа рассылки.
     *
     * @param SubscriptionModel $subscription
     * @return bool
     */
    public function delete(SubscriptionModel $subscription): bool
    {
        $subscriberHash = $this->service->subscriberHash($subscription->email);

        $exist = $this->checkUser($subscription->email);

        if ($exist) {
            $this->service->delete('lists/'.$this->subscriptionList.'/members/'.$subscriberHash, []);
        }

        event(new EmailDeletedEvent($subscription));

        return true;
    }

    /**
     * Проверяем пользователя на нахождение в листе.
     *
     * @param string $email
     * @return bool
     */
    private function checkUser(string $email): bool
    {
        $user = $this->getUser($email);

        return (isset($user['id'])) ? true : false;
    }

    /**
     * Выбираем, что делать с пользователем.
     *
     * @param SubscriptionModel $subscription
     * @param $exist
     */
    private function caseAction(SubscriptionModel $subscription, $exist): void
    {
        if (! $exist) {
            $this->addUser($subscription);
        } else {
            $this->updateUser($subscription);
        }
    }

    /**
     * Добавляем пользователя в лист.
     *
     * @param SubscriptionModel $subscription
     * @return bool
     */
    private function addUser(SubscriptionModel $subscription): bool
    {
        $additionalData = $this->getAdditionalInfo($subscription);

        $options = array_merge([
            'email_address' => $subscription->email,
            'status' => 'pending',
        ], $additionalData);

        $this->service->post('lists/'.$this->subscriptionList.'/members', $options);

        event(new EmailPendingEvent($subscription));

        return true;
    }

    /**
     * Обновляем данные пользователя.
     *
     * @param SubscriptionModel $subscription
     * @return bool
     */
    private function updateUser(SubscriptionModel $subscription): bool
    {
        $prevEmail = $subscription->getOriginal('email');
        $prevStatus = $subscription->getOriginal('status');

        $additionalData = $this->getAdditionalInfo($subscription);

        $subscriberHash = $this->service->subscriberHash($prevEmail ?: $subscription->email);

        $options = array_merge([
            'email_address' => $subscription->email,
        ], $additionalData);

        if ($prevEmail != $subscription->email) {
            $options['status'] = 'pending';

            event(new EmailPendingEvent($subscription));
        }

        if ($prevStatus != $subscription->status) {
            $options['status'] = $subscription->status;

            $event = '\InetStudio\Subscription\Events\Email'.Str::ucfirst($subscription->status).'Event';

            event(new $event($subscription));
        }

        $this->service->patch('lists/'.$this->subscriptionList.'/members/'.$subscriberHash, $options);

        return true;
    }

    /**
     * Синхронизируем локальные данные с сервисом подписок.
     *
     * @param Request $request
     * @return bool
     */
    public function sync(Request $request): bool
    {
        $requestData = Webhook::receive($request->instance()->getContent());

        if (isset($requestData['data']['email'])) {
            $email = $requestData['data']['email'];

            $user = $this->getUser($email);
            $subscribers = SubscriptionModel::withTrashed()->where('email', $email)->get();
            SubscriptionModel::flushEventListeners();

            if ($subscribers->count() > 0) {
                $subscriber = $subscribers->first();

                if ($subscriber->trashed()) {
                    $subscriber->restore();
                }
            } else {
                $subscriber = new SubscriptionModel();
            }

            if (isset($user['id'])) {
                if ($requestData['type'] == 'cleaned') {

                    $subscriber->status = 'cleaned';
                    $subscriber->save();

                    event(new EmailCleanedEvent($subscriber));
                } else {
                    $subscriber->email = $email;
                    $subscriber->status = (isset($user['status'])) ? $user['status'] : 'unsubscribed';
                    $subscriber->additional_info = $this->formatAdditionalInfo($user);
                    $subscriber->save();

                    $event = '\InetStudio\Subscription\Events\Email'.Str::ucfirst($subscriber->status).'Event';

                    event(new $event($subscriber));
                }
            } else {
                if (isset($requestData['data']['action']) && $requestData['data']['action'] == 'delete') {

                    $subscriber->status = 'deleted';
                    $subscriber->save();

                    event(new EmailDeletedEvent($subscriber));

                    $subscriber->delete();
                }
            }
        }

        return true;
    }

    /**
     * Возвращаем пользователя из Mailchimp.
     *
     * @param string $email
     * @return array
     */
    private function getUser(string $email): array
    {
        $subscriberHash = $this->service->subscriberHash($email);

        return $this->service->get('lists/'.$this->subscriptionList.'/members/'.$subscriberHash, []);
    }

    /**
     * Получаем дополнительные данные пользователя.
     *
     * @param SubscriptionModel $subscription
     * @return array
     */
    private function getAdditionalInfo(SubscriptionModel $subscription): array
    {
        $additionalData = $subscription->additional_info;

        $data = [];

        foreach ($this->userData as $key => $option) {
            if (isset($additionalData[$key]) && count($additionalData[$key]) > 0) {
                $method = 'prepare'.Str::studly($key).'Info';

                $data = array_merge($data, [
                    $option => $this->$method($additionalData[$key]),
                ]);
            }
        }

        return $data;
    }

    /**
     * Подготавливаем персональные данные.
     *
     * @param array $data
     * @return array
     */
    private function preparePersonalInfo(array $data): array
    {
        return $data;
    }

    /**
     * Подготавливаем данные по группам.
     *
     * @param array $data
     * @return array
     */
    private function prepareGroupsInfo(array $data): array
    {
        $data = $this->array_change_key_case_unicode($data, CASE_LOWER);

        $prepareData = [];

        foreach ($this->interests as $key => $id) {
            $prepareData[$id] = (isset($data[$key])) ? true : false;
        }

        return $prepareData;
    }

    /**
     * Получаем дополнительную информацию по пользователю Mailchimp.
     *
     * @param array $user
     * @return array
     */
    private function formatAdditionalInfo(array $user): array
    {
        $data = [];

        foreach ($this->userData as $key => $option) {
            if (isset($user[$option]) && count($user[$option]) > 0) {
                $method = 'format'.Str::studly($key).'Info';

                $data = array_merge($data, [
                    $key => $this->$method($user[$option]),
                ]);
            }
        }

        return $data;
    }

    /**
     * Подготавливаем персональные данные.
     *
     * @param array $data
     * @return array
     */
    private function formatPersonalInfo(array $data): array
    {
        return $data;
    }

    /**
     * Подготавливаем данные по группам.
     *
     * @param array $data
     * @return array
     */
    private function formatGroupsInfo(array $data): array
    {
        $formatData = [];

        foreach ($data as $key => $value) {
            $groupName = array_search($key, $this->interests);
            if ($groupName && $value) {
                $formatData[$groupName] = $value;
            }
        }

        return $formatData;
    }

    /**
     * Смена регистра ключей в массиве с поддержкой юникода.
     *
     * @param array $arr
     * @param int $case
     * @return array
     */
    private function array_change_key_case_unicode(array $arr, int $case = CASE_LOWER): array
    {
        $case = ($case == CASE_LOWER) ? MB_CASE_LOWER : MB_CASE_UPPER;

        $returnArray = [];

        foreach ($arr as $key => $value) {
            $returnArray[mb_convert_case($key, $case, 'UTF-8')] = $value;
        }

        return $returnArray;
    }
}
