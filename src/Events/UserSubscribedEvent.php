<?php

namespace InetStudio\Subscription\Events;

use Illuminate\Queue\SerializesModels;
use InetStudio\Subscription\Models\SubscriptionModel;

class UserSubscribedEvent
{
    use SerializesModels;

    /**
     * @var SubscriptionModel
     */
    public $subscription;

    /**
     * UserSubscribedEvent constructor.
     * @param SubscriptionModel $subscription
     */
    public function __construct(SubscriptionModel $subscription)
    {
        $this->subscription = $subscription;
    }
}
