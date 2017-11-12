<?php

namespace InetStudio\Subscription\Contracts;

use InetStudio\Subscription\Models\SubscriptionModel;

interface SubscriptionServiceContract
{
    /**
     * Подписываем пользователя на рассылку.
     *
     * @param SubscriptionModel $subscription
     * @return mixed
     */
    public function subscribe(SubscriptionModel $subscription);

    /**
     * Обновляем информацию подписчика.
     *
     * @param SubscriptionModel $subscription
     * @return mixed
     */
    public function update(SubscriptionModel $subscription);

    /**
     * Отписываем пользователя от рассылки.
     *
     * @param SubscriptionModel $subscription
     * @return mixed
     */
    public function unsubscribe(SubscriptionModel $subscription);
}
