<?php

namespace InetStudio\Subscription\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use InetStudio\Subscription\Contracts\Http\Controllers\Back\SubscriptionControllerContract;
use InetStudio\Subscription\Contracts\Http\Responses\Back\Subscription\FormResponseContract;
use InetStudio\Subscription\Contracts\Http\Responses\Back\Subscription\IndexResponseContract;
use InetStudio\Subscription\Contracts\Http\Responses\Back\Subscription\DestroyResponseContract;

/**
 * Class SubscriptionController.
 */
class SubscriptionController extends Controller implements SubscriptionControllerContract
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
        $this->services['subscription'] = app()->make('InetStudio\Subscription\Contracts\Services\Back\SubscriptionServiceContract');
        $this->services['dataTables'] = app()->make('InetStudio\Subscription\Contracts\Services\Back\SubscriptionDataTableServiceContract');
    }

    /**
     * Список объектов.
     *
     * @return IndexResponseContract
     */
    public function index(): IndexResponseContract
    {
        $table = $this->services['dataTables']->html();

        return app()->makeWith(IndexResponseContract::class, [
            'data' => compact('table'),
        ]);
    }

    /**
     * Добавление объекта.
     *
     * @return FormResponseContract
     */
    public function create(): FormResponseContract
    {
        $item = $this->services['subscription']->getItemByID();

        return app()->makeWith(FormResponseContract::class, [
            'data' => compact('item'),
        ]);
    }

    /**
     * Редактирование объекта.
     *
     * @param int $id
     *
     * @return FormResponseContract
     */
    public function edit($id = 0): FormResponseContract
    {
        $item = $this->services['subscription']->getItemByID($id);

        return app()->makeWith(FormResponseContract::class, [
            'data' => compact('item'),
        ]);
    }

    /**
     * Удаление объекта.
     *
     * @param int $id
     *
     * @return DestroyResponseContract
     */
    public function destroy(int $id = 0): DestroyResponseContract
    {
        $result = $this->services['subscription']->destroy($id);

        return app()->makeWith(DestroyResponseContract::class, [
            'result' => ($result === null) ? false : $result,
        ]);
    }
}
