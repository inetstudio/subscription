<?php

namespace InetStudio\Subscription\Repositories;

use InetStudio\AdminPanel\Repositories\BaseRepository;
use InetStudio\Subscription\Contracts\Models\SubscriptionModelContract;
use InetStudio\Subscription\Contracts\Repositories\SubscriptionRepositoryContract;

/**
 * Class SubscriptionRepository.
 */
class SubscriptionRepository extends BaseRepository implements SubscriptionRepositoryContract
{
    /**
     * SubscriptionRepository constructor.
     *
     * @param SubscriptionModelContract $model
     */
    public function __construct(SubscriptionModelContract $model)
    {
        $this->model = $model;

        $this->defaultColumns = ['id', 'email', 'status', 'user_id', 'additional_info'];
    }
}
