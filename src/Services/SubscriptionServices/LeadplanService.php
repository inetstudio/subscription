<?php

namespace InetStudio\Subscription\Services\SubscriptionServices;

use Illuminate\Http\Request;
use InetStudio\Subscription\Contracts\Models\SubscriptionModelContract;
use InetStudio\Subscription\Contracts\Services\SubscriptionServices\SubscriptionServiceContract;

/**
 * Class LeadplanService.
 */
class LeadplanService implements SubscriptionServiceContract
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

        if (isset($requestData['additional_info'])) {
            sleep(3);
        }

        if (isset($requestData['email'])) {
            $email = $requestData['email'];

            $users = $usersService->getModel()->where([
                ['email', '=', $email],
            ])->get();

            $item = $subscriptionService->getModel()::withTrashed()
                ->where([
                    ['email', '=', $email],
                ])
                ->first();

            $subscriptionService->getModel()::flushEventListeners();

            if ($item && $item->trashed()) {
                $item->restore();
            }

            $itemId = $item['id'] ?? 0;
            $data = [
                'email' => $email,
                'user_id' => ($users->count() > 0) ? $users->first()->id : 0,
            ];

            if (isset($requestData['status'])) {
                $data['status'] = $requestData['status'];
            }

            if (isset($requestData['additional_info'])) {
                $currentData = (isset($item)) ? $item->additional_info : [];
                $data['additional_info'] = array_replace_recursive($currentData, $requestData['additional_info']);
            }

            $item = $subscriptionService->saveModel($data, $itemId);

            if ($itemId == 0) {
                event(app()->makeWith('InetStudio\Subscription\Contracts\Events\Front\NewSubscriberSyncEventContract', [
                    'object' => $item,
                ]));
            } else {
                event(app()->makeWith('InetStudio\Subscription\Contracts\Events\Front\UpdateSubscriberSyncEventContract', [
                    'object' => $item,
                ]));
            }
        }

        return true;
    }
}
