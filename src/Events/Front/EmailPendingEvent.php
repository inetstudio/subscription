<?php

namespace InetStudio\Subscription\Events\Front;

use Illuminate\Queue\SerializesModels;
use InetStudio\Subscription\Contracts\Models\SubscriptionModelContract;
use InetStudio\Subscription\Contracts\Events\Front\EmailPendingEventContract;

/**
 * Class EmailPendingEvent.
 */
class EmailPendingEvent implements EmailPendingEventContract
{
    use SerializesModels;

    /**
     * @var SubscriptionModelContract
     */
    public $object;

    /**
     * EmailPendingEvent constructor.
     *
     * @param SubscriptionModelContract $object
     */
    public function __construct(SubscriptionModelContract $object)
    {
        $this->object = $object;
    }
}
