<?php

namespace InetStudio\Subscription\Listeners\Front;

use Illuminate\Support\Arr;
use InetStudio\Subscription\Contracts\Listeners\Front\AttachUserToSubscriptionListenerContract;

/**
 * Class AttachUserToSubscriptionListener.
 */
class AttachUserToSubscriptionListener implements AttachUserToSubscriptionListenerContract
{
    /**
     * Handle the event.
     *
     * @param $event
     *
     * @return void
     */
    public function handle($event): void
    {
        $subscriptionService = app()->make('InetStudio\Subscription\Contracts\Services\Front\SubscriptionServiceContract');

        $user = $event->user;

        $items = $subscriptionService->getModel()::withTrashed()
            ->where([
                ['email', '=', $user->email],
                ['user_id', '=', 0],
            ])
            ->get();

        foreach ($items as $item) {
            $additionalInfo = $item['additional_info'];
            $additionalInfo = Arr::set($additionalInfo, 'personal.FNAME', $user->name);

            $itemId = $item['id'] ?? 0;
            $data = [
                'user_id' => $user->id,
                'additional_info' => $additionalInfo,
            ];

            $subscriptionService->saveModel($data, $itemId);
        }
    }
}
