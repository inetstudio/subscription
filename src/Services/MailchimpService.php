<?php

namespace InetStudio\Subscription\Services;

use Illuminate\Http\Request;
use DrewM\MailChimp\MailChimp;
use InetStudio\Subscription\Models\SubscriptionModel;
use InetStudio\Subscription\Contracts\SubscriptionServiceContract;

class MailchimpService implements SubscriptionServiceContract
{
    private $service;
    private $subscriptionList;

    /**
     * MailchimpService constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->service = new MailChimp($config['api_key']);
        $this->subscriptionList = $config['subscribers_list'];
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
        $original = $subscription->getOriginal();

        $exist = $this->checkUser($original['email']);

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
        $subscriberHash = $this->service->subscriberHash($email);
        $checkUserExist = $this->service->get('lists/'.$this->subscriptionList.'/members/'.$subscriberHash, []);

        return (isset($checkUserExist['id'])) ? true : false;
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

        $options = [
            'email_address' => $subscription->email,
            'status' => 'pending',
        ];

        if (count($additionalData) > 0) {
            $options = array_merge($options, [
                'merge_fields' => $additionalData,
            ]);
        }

        $this->service->post('lists/'.$this->subscriptionList.'/members', $options);

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
        $additionalData = $this->getAdditionalInfo($subscription);
        $status = ($subscription->is_subscribed == 0) ? 'unsubscribed' : 'subscribed';

        $subscriberHash = $this->service->subscriberHash($subscription->email);

        $options = [
            'email_address' => $subscription->email,
            'status' => $status,
        ];

        if (count($additionalData) > 0) {
            $options = array_merge($options, [
                'merge_fields' => $additionalData,
            ]);
        }

        $this->service->patch('lists/'.$this->subscriptionList.'/members/'.$subscriberHash, $options);

        return true;
    }

    /**
     * Получаем дополнительные данные пользователя.
     *
     * @param SubscriptionModel $subscription
     * @return array
     */
    private function getAdditionalInfo(SubscriptionModel $subscription): array
    {
        return array_change_key_case($subscription->additional_info, CASE_UPPER);
    }

    /**
     * Синхронизируем локальные данные с сервисом подписок.
     *
     * @param Request $request
     * @return bool
     */
    public function sync(Request $request): bool
    {
        return true;
    }
}
