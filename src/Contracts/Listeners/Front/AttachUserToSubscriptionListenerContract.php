<?php

namespace InetStudio\Subscription\Contracts\Listeners\Front;

/**
 * Interface AttachUserToSubscriptionListenerContract.
 */
interface AttachUserToSubscriptionListenerContract
{
    public function handle($event): void;
}
