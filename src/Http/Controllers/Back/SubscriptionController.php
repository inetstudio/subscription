<?php

namespace InetStudio\Subscription\Http\Controllers\Back;

use Illuminate\View\View;
use Yajra\DataTables\DataTables;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use InetStudio\Subscription\Models\SubscriptionModel;
use InetStudio\Subscription\Transformers\SubscriptionTransformer;
use InetStudio\AdminPanel\Http\Controllers\Back\Traits\DatatablesTrait;

/**
 * Контроллер для управления подписками (Back).
 *
 * Class SubscriptionController
 */
class SubscriptionController extends Controller
{
    use DatatablesTrait;

    /**
     * Список подписок.
     *
     * @param DataTables $dataTable
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(DataTables $dataTable): View
    {
        $table = $this->generateTable($dataTable, 'subscription', 'index');

        return view('admin.module.subscription::back.pages.index', compact('table'));
    }

    /**
     * DataTables ServerSide.
     *
     * @return mixed
     */
    public function data()
    {
        $items = SubscriptionModel::query();

        return DataTables::of($items)
            ->setTransformer(new SubscriptionTransformer)
            ->rawColumns(['status', 'actions'])
            ->make();
    }
    
    /**
     * Редактирование подписки.
     *
     * @param null $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id = null): View
    {
        if (! is_null($id) && $id > 0 && $item = SubscriptionModel::find($id)) {
            return view('admin.module.subscription::back.pages.form', [
                'item' => $item,
            ]);
        } else {
            abort(404);
        }
    }

    /**
     * Удаление подписки.
     *
     * @param null $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id = null): JsonResponse
    {
        if (! is_null($id) && $id > 0 && $item = SubscriptionModel::find($id)) {
            $item->delete();

            return response()->json([
                'success' => true,
            ]);
        } else {
            return response()->json([
                'success' => false,
            ]);
        }
    }
}
