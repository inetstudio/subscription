<?php

namespace InetStudio\Subscription\Services;

use InetStudio\Subscription\Models\SubscriptionModel;
use InetStudio\Subscription\Contracts\SubscriptionServiceContact;

class LocalService implements SubscriptionServiceContact
{
    /**
     * Подписываем пользователя на рассылку.
     *
     * @param SubscriptionModel $subscription
     * @return bool
     */
    public function subscribe(SubscriptionModel $subscription)
    {
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
        return true;
    }
}
