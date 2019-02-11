<?php

namespace InetStudio\Subscription\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use InetStudio\Subscription\Contracts\Http\Controllers\Back\SubscriptionDataControllerContract;

/**
 * Class SubscriptionDataController.
 */
class SubscriptionDataController extends Controller implements SubscriptionDataControllerContract
{
    /**
     * Используемые сервисы.
     *
     * @var array
     */
    protected $services;

    /**
     * SubscriptionController constructor.
     */
    public function __construct()
    {
        $this->services['dataTables'] = app()->make('InetStudio\Subscription\Contracts\Services\Back\SubscriptionDataTableServiceContract');
    }

    /**
     * Получаем данные для отображения в таблице.
     *
     * @return mixed
     */
    public function data()
    {
        return $this->services['dataTables']->ajax();
    }
}
