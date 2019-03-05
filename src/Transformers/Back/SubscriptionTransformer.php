<?php

namespace InetStudio\Subscription\Transformers\Back;

use League\Fractal\TransformerAbstract;
use InetStudio\Subscription\Contracts\Models\SubscriptionModelContract;
use InetStudio\Subscription\Contracts\Transformers\Back\SubscriptionTransformerContract;

/**
 * Class SubscriptionTransformer.
 */
class SubscriptionTransformer extends TransformerAbstract implements SubscriptionTransformerContract
{
    /**
     * Подготовка данных для отображения в таблице.
     *
     * @param SubscriptionModelContract $item
     *
     * @return array
     *
     * @throws \Throwable
     */
    public function transform(SubscriptionModelContract $item): array
    {
        return [
            'id' => (int) $item->id,
            'email' => $item->email,
            'info' => '',
            'created_at' => (string) $item->created_at,
            'updated_at' => (string) $item->updated_at,
            'status' => view('admin.module.subscription::back.partials.datatables.status', [
                'status' => $item->status,
            ])->render(),
            'actions' => view('admin.module.subscription::back.partials.datatables.actions', [
                'id' => $item->id,
            ])->render(),
        ];
    }
}
