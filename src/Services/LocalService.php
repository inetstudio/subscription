<?php

namespace InetStudio\Subscription\Services;

use Illuminate\Http\Request;
use InetStudio\Subscription\Models\SubscriptionModel;
use InetStudio\Subscription\Contracts\SubscriptionServiceContract;

class LocalService implements SubscriptionServiceContract
{
    /**
     * Подписываем пользователя на рассылку.
     *
     * @param SubscriptionModel $subscription
     * @return bool
     */
    public function subscribe(SubscriptionModel $subscription): bool
    {
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
        return true;
    }
}
