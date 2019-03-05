<?php

namespace InetStudio\Subscription\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use InetStudio\Subscription\Contracts\Services\Back\SubscriptionServiceContract;
use InetStudio\Subscription\Contracts\Http\Responses\Back\Resource\FormResponseContract;
use InetStudio\Subscription\Contracts\Http\Responses\Back\Resource\IndexResponseContract;
use InetStudio\Subscription\Contracts\Services\Back\SubscriptionDataTableServiceContract;
use InetStudio\Subscription\Contracts\Http\Controllers\Back\SubscriptionControllerContract;
use InetStudio\Subscription\Contracts\Http\Responses\Back\Resource\DestroyResponseContract;

/**
 * Class SubscriptionController.
 */
class SubscriptionController extends Controller implements SubscriptionControllerContract
{
    /**
     * Список объектов.
     *
     * @param SubscriptionDataTableServiceContract $dataTableService
     * 
     * @return IndexResponseContract
     */
    public function index(SubscriptionDataTableServiceContract $dataTableService): IndexResponseContract
    {
        $table = $dataTableService->html();

        return app()->makeWith(IndexResponseContract::class, [
            'data' => compact('table'),
        ]);
    }

    /**
     * Добавление объекта.
     *
     * @param SubscriptionServiceContract $subscriptionService
     *
     * @return FormResponseContract
     */
    public function create(SubscriptionServiceContract $subscriptionService): FormResponseContract
    {
        $item = $subscriptionService->getItemById();

        return app()->makeWith(FormResponseContract::class, [
            'data' => compact('item'),
        ]);
    }

    /**
     * Редактирование объекта.
     *
     * @param SubscriptionServiceContract $subscriptionService
     * @param int $id
     *
     * @return FormResponseContract
     */
    public function edit(SubscriptionServiceContract $subscriptionService, int $id = 0): FormResponseContract
    {
        $item = $subscriptionService->getItemById($id);

        return app()->makeWith(FormResponseContract::class, [
            'data' => compact('item'),
        ]);
    }

    /**
     * Удаление объекта.
     *
     * @param SubscriptionServiceContract $subscriptionService
     * @param int $id
     *
     * @return DestroyResponseContract
     */
    public function destroy(SubscriptionServiceContract $subscriptionService, int $id = 0): DestroyResponseContract
    {
        $result = $subscriptionService->destroy($id);

        return app()->makeWith(DestroyResponseContract::class, [
            'result' => ($result === null) ? false : $result,
        ]);
    }
}
