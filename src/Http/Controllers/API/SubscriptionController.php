<?php

namespace InetStudio\Subscription\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

/**
 * Class SubscriptionController.
 */
class SubscriptionController extends Controller
{
    /**
     * Синхронизируем локальные данные с сервисом подписок.
     *
     * @param Request $request
     * @param string $service
     *
     * @return JsonResponse
     */
    public function sync(Request $request, string $service): JsonResponse
    {
        $subscriptionService = app()->makeWith('InetStudio\Subscription\Contracts\Managers\SubscriptionManagerContract', [
            'app' => app(),
        ])->with($service);

        return response()->json([
            'success' => $subscriptionService->sync($request),
        ]);
    }
}
