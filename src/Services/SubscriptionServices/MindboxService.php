<?php

namespace InetStudio\Subscription\Services;

use Illuminate\Http\Request;
use InetStudio\Subscription\Contracts\Models\SubscriptionModelContract;
use InetStudio\Subscription\Contracts\Services\SubscriptionServices\SubscriptionServiceContract;

/**
 * Class MindboxService.
 */
class MindboxService implements SubscriptionServiceContract
{
    /**
     * Подписываем пользователя на рассылку.
     *
     * @param SubscriptionModelContract $item
     *
     * @return bool
     */
    public function subscribe(SubscriptionModelContract $item): bool
    {
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
        $usersService = app()->make('InetStudio\ACL\Users\Contracts\Services\Front\ItemsServiceContract');

        $requestData = $request->all();

        if (isset($requestData['email'])) {
            $email = $requestData['email'];

            $users = $usersService->getModel()->where([
                ['email', '=', $email],
            ])->get();

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
            $data = [
                'email' => $email,
                'status' => $requestData['status'],
                'user_id' => ($users->count() > 0) ? $users->first()->id : 0,
            ];

            if (! $itemId) {
                $data['additional_info'] = [];
            }

            $subscriptionService->saveModel($data, $itemId);
        }

        return true;
    }
}
