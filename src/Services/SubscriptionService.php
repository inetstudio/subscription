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
     * @return bool
     */
    public function subscribe($request): bool
    {
        $usersService = app()->make('UsersService');

        $email = $usersService->getUserEmail($request);

        $additional_info = ($request->filled('data')) ? Arr::changeKeysCase($request->get('data')) : [];

        $subscriber = SubscriptionModel::withTrashed()->where('email', $email)->first();

        $subscriptionData = [
            'user_id' => $usersService->getUserId($email),
            'additional_info' => $additional_info,
        ];

        if ($subscriber) {
            if ($subscriber->trashed()) {
                $dispatcher = SubscriptionModel::getEventDispatcher();
                SubscriptionModel::unsetEventDispatcher();

                $subscriber->restore();

                SubscriptionModel::setEventDispatcher($dispatcher);
            }

            if ($subscriber->status != 'subscribed') {
                $subscriptionData['status'] = 'pending';
            }
        } else {
            $subscriptionData['status'] = 'pending';
        }

        SubscriptionModel::updateOrCreate([
            'email' => $email,
        ], $subscriptionData);

        return true;
    }
}
