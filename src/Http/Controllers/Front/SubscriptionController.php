<?php

namespace InetStudio\Subscription\Http\Controllers\Front;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use InetStudio\Subscription\Http\Requests\Front\SubscribeRequest;

/**
 * Контроллер для управления подписками (Front).
 *
 * Class SubscriptionController
 */
class SubscriptionController extends Controller
{
    /**
     * Подписка пользователя.
     *
     * @param SubscribeRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function subscribe(SubscribeRequest $request): JsonResponse
    {
        $subscriptionService = app()->make('InetStudio\Subscription\Contracts\Services\Front\SubscriptionServiceContract');

        return response()->json([
            'success' => $subscriptionService->subscribeByRequest($request),
            'message' => trans('subscription::messages.pending'),
        ]);
    }
}
