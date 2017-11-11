<?php

namespace InetStudio\Subscription\Services;

use Mailgun\Mailgun;
use InetStudio\Subscription\Models\SubscriptionModel;
use InetStudio\Subscription\Contracts\SubscriptionServiceContact;

class MailgunService implements SubscriptionServiceContact
{
    private $service;
    private $subscriptionList;

    /**
     * MailchimpService constructor.
     */
    public function __construct()
    {
        $this->service = new Mailgun('subscription.mailgun.api_key');
        $this->subscriptionList = config('subscription.mailgun.subscribers_list');
    }

    /**
     * Подписываем пользователя на рассылку.
     *
     * @param SubscriptionModel $subscription
     * @return bool
     */
    public function subscribe(SubscriptionModel $subscription)
    {
        $status = ($subscription->is_subscribed == 0) ? 'unsubscribed' : 'subscribed';

        $this->service->post('lists/'.$this->subscriptionList.'/members', [
            'subscribed' => $status,
            'vars' => $this->getAdditionalInfo($subscription)
        ]);

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
        $status = ($subscription->is_subscribed == 0) ? 'unsubscribed' : 'subscribed';

        $this->service->put('lists/'.$this->subscriptionList.'/members/'.$original, [
            'subscribed' => $status,
            'vars' => $this->getAdditionalInfo($subscription)
        ]);

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
        $this->service->put('lists/'.$this->subscriptionList.'/members/'.$subscription->email, [
            'subscribed' => false,
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
        return json_encode(array_change_key_case($subscription->additional_info, CASE_UPPER));
    }
}
