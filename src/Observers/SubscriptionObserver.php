<?php

namespace InetStudio\Subscription\Observers;

use InetStudio\Subscription\Models\SubscriptionModel;
use InetStudio\Subscription\Contracts\SubscriptionServiceContact;

class SubscriptionObserver
{
    /**
     * Событие "объект подписки создан".
     *
     * @param SubscriptionServiceContact $subscriptionService
     * @param SubscriptionModel $subscription
     */
    public function created(SubscriptionServiceContact $subscriptionService, SubscriptionModel $subscription): void
    {
        $subscriptionService->subscribe($subscription);
    }

    /**
     * Событие "объект подписки обновляется".
     *
     * @param SubscriptionServiceContact $subscriptionService
     * @param SubscriptionModel $subscription
     */
    public function updating(SubscriptionServiceContact $subscriptionService, SubscriptionModel $subscription): void
    {
        $subscriptionService->update($subscription);
    }

    /**
     * Событие "объект подписки удаляется".
     *
     * @param SubscriptionServiceContact $subscriptionService
     * @param SubscriptionModel $subscription
     */
    public function deleting(SubscriptionServiceContact $subscriptionService, SubscriptionModel $subscription): void
    {
        $subscriptionService->unsubscribe($subscription);
    }
}
