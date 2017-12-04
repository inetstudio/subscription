<?php

namespace InetStudio\Subscription\Services;

use Illuminate\Support\Arr;
use InetStudio\Subscription\Models\SubscriptionModel;

class SubscriptionService
{
    /**
     * Сохраняем подписчика.
     *
     * @param $request
     * @return array
     */
    public function subscribeByRequest($request): array
    {
        $subscriptionData = $this->getRequestSubscriptionData($request);

        return $this->subscribeByData($subscriptionData);
    }

    /**
     * Сохраняем подписчика.
     *
     * @param array $subscriptionData
     * @return array
     */
    public function subscribeByData(array $subscriptionData): array
    {
        $subscriber = $this->getSubscriber($subscriptionData['email']);
        $subscriber = $this->restoreSubscriber($subscriber);

        if (! $subscriber || $subscriber->status != 'subscribed') {
            $subscriptionData['status'] = 'pending';

            $message = trans('subscription::messages.pending');
        } else {
            $message = trans('subscription::messages.update');
        }

        $subscriber = SubscriptionModel::updateOrCreate([
            'email' => $subscriptionData['email'],
        ], $subscriptionData);

        return [
            'message' => $message,
            'subscription' => $subscriber,
        ];
    }

    /**
     * Отписываем пользователя.
     *
     * @param $request
     * @return array
     */
    public function unsubscribeByRequest($request): array
    {
        $subscriptionData = $this->getRequestSubscriptionData($request);

        return $this->unsubscribeByData($subscriptionData);
    }

    /**
     * Отписываем пользователя.
     *
     * @param array $subscriptionData
     * @return array
     */
    public function unsubscribeByData(array $subscriptionData): array
    {
        $subscriber = $this->getSubscriber($subscriptionData['email']);
        $subscriber = $this->restoreSubscriber($subscriber);

        if ($subscriber) {
            $subscriptionData['status'] = 'unsubscribed';

            $subscriber->update($subscriptionData);

            return [
                'message' => trans('subscription::messages.unsubscribed'),
                'subscription' => $subscriber,
            ];
        } else {
            return [
                'message' => trans('subscription::messages.not_found'),
                'subscription' => $subscriber,
            ];
        }
    }

    /**
     * Получаем информацию по подписке из запроса.
     *
     * @param $request
     * @return array
     */
    private function getRequestSubscriptionData($request)
    {
        $usersService = app()->make('UsersService');

        $email = $usersService->getUserEmail($request);
        $additional_info = ($request->filled('subscriptionData')) ? Arr::changeKeysCase($request->get('subscriptionData')) : [];

        $subscriptionData = [
            'user_id' => $usersService->getUserId($email),
            'email' => $email,
            'additional_info' => $additional_info,
        ];

        return $subscriptionData;
    }

    /**
     * Получаем подписчика.
     *
     * @param string $email
     * @return SubscriptionModel|null
     */
    private function getSubscriber($email): ?SubscriptionModel
    {
        $subscriber = SubscriptionModel::withTrashed()->where('email', $email)->first();

        return $subscriber;
    }

    /**
     * Восстанавливаем подписчика.
     *
     * @param $subscriber
     * @return SubscriptionModel|null
     */
    private function restoreSubscriber($subscriber): ?SubscriptionModel
    {
        if ($subscriber && $subscriber->trashed()) {
            $dispatcher = SubscriptionModel::getEventDispatcher();
            SubscriptionModel::unsetEventDispatcher();

            $subscriber->restore();

            SubscriptionModel::setEventDispatcher($dispatcher);
        }

        return $subscriber;
    }
}
