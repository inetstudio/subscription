<?php

namespace InetStudio\Subscription\Listeners\Front;

use InetStudio\Subscription\Contracts\Listeners\Front\SubscribeFromRequestListenerContract;

/**
 * Class SubscribeFromRequestListener.
 */
class SubscribeFromRequestListener implements SubscribeFromRequestListenerContract
{
    /**
     * Используемые сервисы.
     *
     * @var array
     */
    protected $services;

    /**
     * SubscribeFromRequestListener constructor.
     */
    public function __construct()
    {
        $this->services['subscription'] = app()->make('InetStudio\Subscription\Contracts\Services\Front\SubscriptionServiceContract');
    }

    /**
     * Handle the event.
     *
     * @param $event
     *
     * @return void
     */
    public function handle($event): void
    {
        $request = request();

        if ($request->filled('subscribe-agree')) {
            $this->services['subscription']->subscribeByRequest($request);
        }
    }
}
