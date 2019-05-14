<?php

namespace InetStudio\Subscription\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use DrewM\MailChimp\Webhook;
use DrewM\MailChimp\MailChimp;
use InetStudio\Subscription\Contracts\Models\SubscriptionModelContract;
use InetStudio\Subscription\Contracts\Services\SubscriptionServices\SubscriptionServiceContract;

/**
 * Class MailchimpService.
 */
class MailchimpService implements SubscriptionServiceContract
{
    protected $service;
    
    /**
     * @var string
     */
    protected $subscribersList;

    /**
     * @var array
     */
    protected $interests;

    /**
     * @var array 
     */
    protected $userData = [
        'personal' => 'merge_fields',
        'groups' => 'interests',
    ];

    /**
     * MailchimpService constructor.
     *
     * @param array $config
     *
     * @throws \Exception
     */
    public function __construct(array $config)
    {
        $this->service = new MailChimp($config['api_key']);
        $this->subscribersList = $config['subscribers_list'];
        $this->interests = Arr::changeKeysCase($config['interests'], CASE_LOWER);
    }

    /**
     * Подписываем пользователя на рассылку.
     *
     * @param SubscriptionModelContract $item
     * 
     * @return bool
     */
    public function subscribe(SubscriptionModelContract $item): bool
    {
        $exist = $this->checkUser($item['email']);

        $this->caseAction($item, $exist);

        return true;
    }

    /**
     * Обновляем информацию подписчика.
     *
     * @param SubscriptionModelContract $item
     * 
     * @return bool
     */
    public function update(SubscriptionModelContract $item): bool
    {
        $prevEmail = $item->getOriginal('email', $item['email']);

        $exist = $this->checkUser($prevEmail);

        $this->caseAction($item, $exist);

        return true;
    }

    /**
     * Отписываем пользователя от рассылки.
     *
     * @param SubscriptionModelContract $item
     * 
     * @return bool
     */
    public function unsubscribe(SubscriptionModelContract $item): bool
    {
        $subscriberHash = $this->service->subscriberHash($item['email']);

        $exist = $this->checkUser($item['email']);

        if ($exist) {
            $this->service->patch('lists/'.$this->subscribersList.'/'.$subscriberHash, [
                'status' => 'unsubscribed'
            ]);
        }

        event(app()->makeWith('InetStudio\Subscription\Contracts\Events\Front\EmailUnsubscribedEventContract', [
            'object' => $item,
        ]));

        return true;
    }

    /**
     * Удаляем пользователя из листа рассылки.
     *
     * @param SubscriptionModelContract $item
     * 
     * @return bool
     */
    public function delete(SubscriptionModelContract $item): bool
    {
        $subscriberHash = $this->service->subscriberHash($item['email']);

        $exist = $this->checkUser($item['email']);

        if ($exist) {
            $this->service->delete('lists/'.$this->subscribersList.'/members/'.$subscriberHash, []);
        }

        event(app()->makeWith('InetStudio\Subscription\Contracts\Events\Front\EmailDeletedEventContract', [
            'object' => $item,
        ]));

        return true;
    }

    /**
     * Проверяем пользователя на нахождение в листе.
     *
     * @param string $email
     * 
     * @return bool
     */
    protected function checkUser(string $email): bool
    {
        $user = $this->getUser($email);

        return isset($user['id']);
    }

    /**
     * Выбираем, что делать с пользователем.
     *
     * @param SubscriptionModelContract $item
     * @param bool $exist
     */
    protected function caseAction(SubscriptionModelContract $item, bool $exist): void
    {
        if (! $exist) {
            $this->addUser($item);
        } else {
            $this->updateUser($item);
        }
    }

    /**
     * Добавляем пользователя в лист.
     *
     * @param SubscriptionModelContract $item
     * 
     * @return bool
     */
    protected function addUser(SubscriptionModelContract $item): bool
    {
        $additionalData = $this->getAdditionalInfo($item);

        $options = array_merge([
            'email_address' => $item['email'],
            'status' => config('subscription.default_status', 'pending'),
        ], $additionalData);

        $this->service->post('lists/'.$this->subscribersList.'/members', $options);

        event(app()->makeWith('InetStudio\Subscription\Contracts\Events\Front\EmailPendingEventContract', [
            'object' => $item,
        ]));

        return true;
    }

    /**
     * Обновляем данные пользователя.
     *
     * @param SubscriptionModelContract $item
     *
     * @return bool
     */
    protected function updateUser(SubscriptionModelContract $item): bool
    {
        $prevEmail = $item->getOriginal('email');
        $prevStatus = $item->getOriginal('status');

        $additionalData = $this->getAdditionalInfo($item);

        $subscriberHash = $this->service->subscriberHash($prevEmail ?: $item['email']);

        $options = array_merge([
            'email_address' => $item['email'],
        ], $additionalData);

        if ($prevStatus != $item['status']) {
            $options['status'] = $item['status'];

            event(app()->makeWith('InetStudio\Subscription\Contracts\Events\Front\Email'.Str::ucfirst($item['status']).'EventContract', [
                'object' => $item,
            ]));
        }

        $this->service->patch('lists/'.$this->subscribersList.'/members/'.$subscriberHash, $options);

        return true;
    }

    /**
     * Синхронизируем локальные данные с сервисом подписок.
     *
     * @param Request $request
     *
     * @return bool
     *
     * @throws \Exception
     */
    public function sync(Request $request): bool
    {
        $subscriptionService = app()->make('InetStudio\Subscription\Contracts\Services\Front\SubscriptionServiceContract');

        $requestData = Webhook::receive($request->instance()->getContent());

        if (isset($requestData['data']['email'])) {
            $email = $requestData['data']['email'];

            $user = $this->getUser($email);
            $items = $subscriptionService->getModel()::withTrashed()
                ->where([
                    ['email', '=', $email],
                ])
                ->get();

            $subscriptionService->getModel()::flushEventListeners();

            if ($items->count() > 0) {
                $item = $items->first();

                if ($item->trashed()) {
                    $item->restore();
                }
            }

            $itemId = $item['id'] ?? 0;

            if (isset($user['id'])) {
                if ($requestData['type'] == 'cleaned') {

                    $item = $subscriptionService->saveModel([
                        'status' => 'cleaned',
                    ], $itemId);

                    event(app()->makeWith('InetStudio\Subscription\Contracts\Events\Front\EmailCleanedEventContract', [
                        'object' => $item,
                    ]));
                } else {

                    $item = $subscriptionService->saveModel([
                        'email' => $email,
                        'status' => (isset($user['status'])) ? $user['status'] : 'unsubscribed',
                        'additional_info' => $this->formatAdditionalInfo($user),
                    ], $itemId);

                    event(app()->makeWith('InetStudio\Subscription\Contracts\Events\Front\Email'.Str::ucfirst($item->status).'EventContract', [
                        'object' => $item,
                    ]));
                }
            } else {
                if (isset($requestData['data']['action']) && $requestData['data']['action'] == 'delete') {

                    $item = $subscriptionService->saveModel([
                        'status' => 'deleted',
                    ], $itemId);

                    event(app()->makeWith('InetStudio\Subscription\Contracts\Events\Front\EmailDeletedEventContract', [
                        'object' => $item,
                    ]));

                    $item->delete();
                }
            }
        }

        return true;
    }

    /**
     * Возвращаем пользователя из Mailchimp.
     *
     * @param string $email
     *
     * @return array
     */
    protected function getUser(string $email): array
    {
        $subscriberHash = $this->service->subscriberHash($email);

        return $this->service->get('lists/'.$this->subscribersList.'/members/'.$subscriberHash, []);
    }

    /**
     * Получаем дополнительные данные пользователя.
     *
     * @param SubscriptionModelContract $item
     *
     * @return array
     */
    protected function getAdditionalInfo(SubscriptionModelContract $item): array
    {
        $additionalData = $item['additional_info'];

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
     *
     * @return array
     */
    protected function preparePersonalInfo(array $data): array
    {
        return $data;
    }

    /**
     * Подготавливаем данные по группам.
     *
     * @param array $data
     *
     * @return array
     */
    protected function prepareGroupsInfo(array $data): array
    {
        $data = Arr::changeKeysCase($data, CASE_LOWER);

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
     * 
     * @return array
     */
    protected function formatAdditionalInfo(array $user): array
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
     *
     * @return array
     */
    protected function formatPersonalInfo(array $data): array
    {
        return $data;
    }

    /**
     * Подготавливаем данные по группам.
     *
     * @param array $data
     *
     * @return array
     */
    protected function formatGroupsInfo(array $data): array
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
}
