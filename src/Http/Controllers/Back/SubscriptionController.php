<?php

namespace InetStudio\Subscription\Http\Controllers\Back;

use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;
use InetStudio\AdminPanel\Traits\DatatablesTrait;
use InetStudio\Subscription\Models\SubscriptionModel;
use InetStudio\Subscription\Transformers\SubscriptionTransformer;

/**
 * Контроллер для управления подписками.
 *
 * Class ContestByTagStatusesController
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
    public function index(DataTables $dataTable)
    {
        $table = $this->generateTable($dataTable, 'subscription', 'index');

        return view('admin.module.subscription::pages.index', compact('table'));
    }

    /**
     * Datatables serverside.
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
    public function edit($id = null)
    {
        if (! is_null($id) && $id > 0 && $item = SubscriptionModel::find($id)) {
            return view('admin.module.subscription::pages.form', [
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
    public function destroy($id = null)
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
