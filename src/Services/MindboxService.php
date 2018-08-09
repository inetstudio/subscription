<?php

namespace InetStudio\Subscription\Services;

use Illuminate\Http\Request;
use InetStudio\Subscription\Models\SubscriptionModel;
use InetStudio\Subscription\Contracts\SubscriptionServiceContract;

/**
 * Class MindboxService.
 */
class MindboxService implements SubscriptionServiceContract
{
    /**
     * Подписываем пользователя на рассылку.
     *
     * @param SubscriptionModel $subscription
     *
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
     *
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
     *
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
     *
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
     *
     * @return bool
     *
     * @throws \Exception
     */
    public function sync(Request $request): bool
    {
        $requestData = $request->all();

        if (isset($requestData['email'])) {
            $email = $requestData['email'];

            $user = $this->getUser($email);
            $subscribers = SubscriptionModel::withTrashed()->where('email', $email)->get();
            SubscriptionModel::flushEventListeners();

            if ($subscribers->count() > 0) {
                $subscriber = $subscribers->first();

                if ($subscriber->trashed()) {
                    $subscriber->restore();
                }
            } else {
                $subscriber = new SubscriptionModel();
            }

            $subscriber->email = $email;
            $subscriber->status = $requestData['status'];
            $subscriber->status_id = ($user) ? $user->id : 0;
            $subscriber->additional_info = [];
            $subscriber->save();
        }

        return true;
    }
}
