<?php

namespace InetStudio\Subscription\Contracts;

use Illuminate\Http\Request;
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

    /**
     * Синхронизируем локальные данные с сервисом подписок.
     *
     * @param Request $request
     * @return mixed
     */
    public function sync(Request $request);
}
