<?php

namespace InetStudio\Subscription\Contracts\Services\SubscriptionServices;

use Illuminate\Http\Request;
use InetStudio\Subscription\Contracts\Models\SubscriptionModelContract;

/**
 * Interface SubscriptionServiceContract.
 */
interface SubscriptionServiceContract
{
    /**
     * Подписываем пользователя на рассылку.
     *
     * @param SubscriptionModelContract $item
     *
     * @return bool
     */
    public function subscribe(SubscriptionModelContract $item): bool;

    /**
     * Обновляем информацию подписчика.
     *
     * @param SubscriptionModelContract $item
     *
     * @return bool
     */
    public function update(SubscriptionModelContract $item): bool;

    /**
     * Отписываем пользователя от рассылки.
     *
     * @param SubscriptionModelContract $item
     *
     * @return bool
     */
    public function unsubscribe(SubscriptionModelContract $item): bool;

    /**
     * Удаляем пользователя из листа рассылки.
     *
     * @param SubscriptionModelContract $item
     *
     * @return bool
     */
    public function delete(SubscriptionModelContract $item): bool;

    /**
     * Синхронизируем локальные данные с сервисом подписок.
     *
     * @param Request $request
     *
     * @return bool
     */
    public function sync(Request $request): bool;
}
