<?php

namespace InetStudio\Subscription\Observers;

use InetStudio\Subscription\Models\SubscriptionModel;
use InetStudio\Subscription\Contracts\SubscriptionServiceContact;

class SubscriptionObserver
{
    protected $subscriptionService;

    /**
     * SubscriptionObserver constructor.
     * @param SubscriptionServiceContact $subscriptionService
     */
    public function __construct(SubscriptionServiceContact $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
    }

    /**
     * Событие "объект подписки создан".
     *
     * @param SubscriptionModel $subscription
     */
    public function created(SubscriptionModel $subscription): void
    {
        $this->subscriptionService->subscribe($subscription);
    }

    /**
     * Событие "объект подписки обновляется".
     *
     * @param SubscriptionModel $subscription
     */
    public function updating(SubscriptionModel $subscription): void
    {
        $this->subscriptionService->update($subscription);
    }

    /**
     * Событие "объект подписки удаляется".
     *
     * @param SubscriptionModel $subscription
     */
    public function deleting(SubscriptionModel $subscription): void
    {
        $this->subscriptionService->unsubscribe($subscription);
    }
}
