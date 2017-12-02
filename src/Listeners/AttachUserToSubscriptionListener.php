<?php

namespace InetStudio\Subscription\Listeners;

use InetStudio\AdminPanel\Events\Auth\ActivatedEvent;
use InetStudio\Subscription\Models\SubscriptionModel;

class AttachUserToSubscriptionListener
{
    /**
     * AttachUserToSubscriptionListener constructor.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param ActivatedEvent $event
     * @return void
     */
    public function handle(ActivatedEvent $event): void
    {
        $user = $event->user;

        $subscriptions = SubscriptionModel::where('user_id', 0)->where('email', $user->email)->get();

        foreach ($subscriptions as $subscription) {
            $subscription->user_id = $user->id;
            $subscription->setAdditionalInfo('personal.FNAME', $user->name);
            $subscription->save();
        }
    }
}
