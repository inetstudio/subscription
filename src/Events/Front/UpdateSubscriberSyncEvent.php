<?php

namespace InetStudio\Subscription\Events\Front;

use Illuminate\Queue\SerializesModels;
use InetStudio\Subscription\Contracts\Models\SubscriptionModelContract;
use InetStudio\Subscription\Contracts\Events\Front\UpdateSubscriberSyncEventContract;

/**
 * Class UpdateSubscriberSync.
 */
class UpdateSubscriberSyncEvent implements UpdateSubscriberSyncEventContract
{
    use SerializesModels;

    /**
     * @var SubscriptionModelContract
     */
    public $object;

    /**
     * UpdateSubscriberSync constructor.
     * 
     * @param SubscriptionModelContract $object
     */
    public function __construct(SubscriptionModelContract $object)
    {
        $this->object = $object;
    }
}
