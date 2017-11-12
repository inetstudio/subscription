<?php

namespace InetStudio\Subscription\Observers;

use InetStudio\Subscription\Models\SubscriptionModel;
use InetStudio\Subscription\Contracts\SubscriptionServiceContract;

class SubscriptionObserver
{
    protected $subscriptionService;

    /**
     * SubscriptionObserver constructor.
     * @param SubscriptionServiceContract $subscriptionService
     */
    public function __construct(SubscriptionServiceContract $subscriptionService)
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
