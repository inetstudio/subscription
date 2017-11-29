<?php

namespace InetStudio\Subscription\Events;

use Illuminate\Queue\SerializesModels;
use InetStudio\Subscription\Models\SubscriptionModel;

class UserDeleteEvent
{
    use SerializesModels;

    /**
     * @var SubscriptionModel
     */
    public $subscription;

    /**
     * UserDeleteEvent constructor.
     * @param SubscriptionModel $subscription
     */
    public function __construct(SubscriptionModel $subscription)
    {
        $this->subscription = $subscription;
    }
}
