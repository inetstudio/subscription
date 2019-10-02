<?php

namespace InetStudio\Subscription\Observers;

use InetStudio\Subscription\Contracts\Models\SubscriptionModelContract;
use InetStudio\Subscription\Contracts\Observers\SubscriptionObserverContract;

/**
 * Class SubscriptionObserver.
 */
class SubscriptionObserver implements SubscriptionObserverContract
{
    /**
     * Сервис подписок.
     *
     * @var
     */
    protected $subscriptionService;

    /**
     * SubscriptionObserver constructor.
     */
    public function __construct()
    {
        $this->subscriptionService = app()->make('InetStudio\Subscription\Contracts\Services\SubscriptionServices\SubscriptionServiceContract');
    }

    /**
     * Событие "объект подписки создан".
     *
     * @param SubscriptionModelContract $item
     */
    public function created(SubscriptionModelContract $item): void
    {
        if (! $item->trashed()) {
            $this->subscriptionService->subscribe($item);
        }
    }

    /**
     * Событие "объект подписки обновляется".
     *
     * @param SubscriptionModelContract $item
     */
    public function updating(SubscriptionModelContract $item): void
    {
        if (! $item->trashed()) {
            $this->subscriptionService->update($item);
        }
    }

    /**
     * Событие "объект подписки удаляется".
     *
     * @param SubscriptionModelContract $item
     */
    public function deleting(SubscriptionModelContract $item): void
    {
        $this->subscriptionService->delete($item);
    }

    /**
     * Событие "объект подписки удален".
     *
     * @param SubscriptionModelContract $item
     */
    public function deleted(SubscriptionModelContract $item): void
    {
        $item->status = 'deleted';
        $item->save();
    }
}
