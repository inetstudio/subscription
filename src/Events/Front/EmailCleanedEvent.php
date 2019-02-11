<?php

namespace InetStudio\Subscription\Events\Front;

use Illuminate\Queue\SerializesModels;
use InetStudio\Subscription\Contracts\Models\SubscriptionModelContract;
use InetStudio\Subscription\Contracts\Events\Front\EmailCleanedEventContract;

/**
 * Class EmailCleanedEvent.
 */
class EmailCleanedEvent implements EmailCleanedEventContract
{
    use SerializesModels;

    /**
     * @var SubscriptionModelContract
     */
    public $object;

    /**
     * EmailCleanedEvent constructor.
     *
     * @param SubscriptionModelContract $object
     */
    public function __construct(SubscriptionModelContract $object)
    {
        $this->object = $object;
    }
}
