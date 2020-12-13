<?php

namespace InetStudio\Subscription\Contracts\Listeners\Front;

/**
 * Interface SubscribeFromRequestListenerContract.
 */
interface SubscribeFromRequestListenerContract
{
    /**
     * Handle the event.
     *
     * @param $event
     */
    public function handle($event): void;
}
