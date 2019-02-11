<?php

namespace InetStudio\Subscription\Events\Front;

use Illuminate\Queue\SerializesModels;
use InetStudio\Subscription\Contracts\Models\SubscriptionModelContract;
use InetStudio\Subscription\Contracts\Events\Front\EmailSubscribedEventContract;

/**
 * Class EmailSubscribedEvent.
 */
class EmailSubscribedEvent implements EmailSubscribedEventContract
{
    use SerializesModels;

    /**
     * @var SubscriptionModelContract
     */
    public $object;

    /**
     * EmailSubscribedEvent constructor.
     * 
     * @param SubscriptionModelContract $object
     */
    public function __construct(SubscriptionModelContract $object)
    {
        $this->object = $object;
    }
}
