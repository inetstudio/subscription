<?php

namespace InetStudio\Subscription\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use InetStudio\Subscription\Contracts\Services\Back\SubscriptionDataTableServiceContract;
use InetStudio\Subscription\Contracts\Http\Controllers\Back\SubscriptionDataControllerContract;

/**
 * Class SubscriptionDataController.
 */
class SubscriptionDataController extends Controller implements SubscriptionDataControllerContract
{
    /**
     * Получаем данные для отображения в таблице.
     *
     * @param SubscriptionDataTableServiceContract $dataTableService
     *
     * @return mixed
     */
    public function data(SubscriptionDataTableServiceContract $dataTableService)
    {
        return $dataTableService->ajax();
    }
}
