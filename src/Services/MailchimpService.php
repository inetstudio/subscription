<?php

namespace InetStudio\Subscription\Services;

use DrewM\MailChimp\MailChimp;
use InetStudio\Subscription\Models\SubscriptionModel;
use InetStudio\Subscription\Contracts\SubscriptionServiceContact;

class MailchimpService implements SubscriptionServiceContact
{
    private $service;
    private $subscriptionList;

    /**
     * MailchimpService constructor.
     */
    public function __construct()
    {
        $this->service = new MailChimp(config('subscription.mailchimp.api_key'));
        $this->subscriptionList = config('subscription.mailchimp.subscribers_list');
    }

    /**
     * Подписываем пользователя на рассылку.
     *
     * @param SubscriptionModel $subscription
     * @return bool
     */
    public function subscribe(SubscriptionModel $subscription)
    {
        $exist = $this->checkUser($subscription->email);

        if (! $exist) {
            $this->addUser($subscription);
        } else {
            $this->updateUser($subscription);
        }

        return true;
    }

    /**
     * Обновляем информацию подписчика.
     *
     * @param SubscriptionModel $subscription
     * @return bool
     */
    public function update(SubscriptionModel $subscription)
    {
        $original = $subscription->getOriginal();

        $exist = $this->checkUser($original['email']);

        if (! $exist) {
            $this->addUser($subscription);
        } else {
            $this->updateUser($subscription);
        }

        return true;
    }

    /**
     * Отписываем пользователя от рассылки.
     *
     * @param SubscriptionModel $subscription
     * @return bool
     */
    public function unsubscribe(SubscriptionModel $subscription)
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
    private function checkUser(string $email)
    {
        $subscriberHash = $this->service->subscriberHash($email);
        $checkUserExist = $this->service->get('lists/'.$this->subscriptionList.'/members/'.$subscriberHash, []);

        return (isset($checkUserExist['id'])) ? true : false;
    }

    /**
     * Добавляем пользователя в лист.
     *
     * @param SubscriptionModel $subscription
     * @return bool
     */
    private function addUser(SubscriptionModel $subscription)
    {
        $additionalData = $this->getAdditionalInfo($subscription);

        $this->service->post('lists/'.$this->subscriptionList.'/members', [
            'email_address' => $subscription->email,
            'merge_fields' => $additionalData,
            'status' => 'pending',
        ]);

        return true;
    }

    /**
     * Обновляем данные пользователя.
     *
     * @param SubscriptionModel $subscription
     * @return bool
     */
    private function updateUser(SubscriptionModel $subscription)
    {
        $additionalData = $this->getAdditionalInfo($subscription);
        $status = ($subscription->is_subscribed == 0) ? 'unsubscribed' : 'subscribed';

        $subscriberHash = $this->service->subscriberHash($subscription->email);

        $this->service->patch('lists/'.$this->subscriptionList.'/members/'.$subscriberHash, [
            'email_address' => $subscription->email,
            'merge_fields' => $additionalData,
            'status' => $status
        ]);

        return true;
    }

    /**
     * Получаем дополнительные данные пользователя.
     *
     * @param SubscriptionModel $subscription
     * @return array
     */
    private function getAdditionalInfo(SubscriptionModel $subscription)
    {
        return array_change_key_case($subscription->additional_info, CASE_UPPER);
    }
}
