<?php

namespace InetStudio\Subscription\Transformers;

use League\Fractal\TransformerAbstract;
use InetStudio\Subscription\Models\SubscriptionModel;

class SubscriptionTransformer extends TransformerAbstract
{
    /**
     * Подготовка данных для отображения в таблице.
     *
     * @param SubscriptionModel $subscription
     * @return array
     */
    public function transform(SubscriptionModel $subscription): array
    {
        return [
            'id' => (int) $subscription->id,
            'email' => $subscription->email,
            'info' => '',
            'created_at' => (string) $subscription->created_at,
            'updated_at' => (string) $subscription->updated_at,
            'status' => view('admin.module.subscription::partials.datatables.status', [
                'status' => $subscription->is_subscribed,
            ])->render(),
            'actions' => view('admin.module.subscription::partials.datatables.actions', [
                'id' => $subscription->id,
            ])->render(),
        ];
    }
}
