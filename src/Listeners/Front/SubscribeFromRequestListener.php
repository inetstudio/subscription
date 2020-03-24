<?php

namespace InetStudio\Subscription\Listeners\Front;

use InetStudio\Subscription\Contracts\Listeners\Front\SubscribeFromRequestListenerContract;

/**
 * Class SubscribeFromRequestListener.
 */
class SubscribeFromRequestListener implements SubscribeFromRequestListenerContract
{
    /**
     * Handle the event.
     *
     * @param $event
     */
    public function handle($event): void
    {
        $subscriptionsService = app()->make('InetStudio\Subscription\Contracts\Services\Front\SubscriptionServiceContract');

        $request = request();

        if ($request->filled('subscribe-agree')) {
            $subscriptionsService->subscribeByRequest($request);
        }
    }
}
