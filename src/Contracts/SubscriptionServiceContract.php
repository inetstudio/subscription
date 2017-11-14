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
     * @return bool
     */
    public function subscribe(SubscriptionModel $subscription): bool;

    /**
     * Обновляем информацию подписчика.
     *
     * @param SubscriptionModel $subscription
     * @return bool
     */
    public function update(SubscriptionModel $subscription): bool;

    /**
     * Отписываем пользователя от рассылки.
     *
     * @param SubscriptionModel $subscription
     * @return bool
     */
    public function unsubscribe(SubscriptionModel $subscription): bool;

    /**
     * Удаляем пользователя из листа рассылки.
     *
     * @param SubscriptionModel $subscription
     * @return bool
     */
    public function delete(SubscriptionModel $subscription): bool;

    /**
     * Синхронизируем локальные данные с сервисом подписок.
     *
     * @param Request $request
     * @return bool
     */
    public function sync(Request $request): bool;
}
