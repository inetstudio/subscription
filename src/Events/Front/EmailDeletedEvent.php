<?php

namespace InetStudio\Subscription\Events\Front;

use Illuminate\Queue\SerializesModels;
use InetStudio\Subscription\Contracts\Models\SubscriptionModelContract;
use InetStudio\Subscription\Contracts\Events\Front\EmailDeletedEventContract;

/**
 * Class EmailDeletedEvent.
 */
class EmailDeletedEvent implements EmailDeletedEventContract
{
    use SerializesModels;

    /**
     * @var SubscriptionModelContract
     */
    public $object;

    /**
     * EmailDeletedEvent constructor.
     *
     * @param SubscriptionModelContract $object
     */
    public function __construct(SubscriptionModelContract $object)
    {
        $this->object = $object;
    }
}
