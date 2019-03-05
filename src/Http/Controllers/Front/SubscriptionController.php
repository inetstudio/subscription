<?php

namespace InetStudio\Subscription\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use InetStudio\Subscription\Contracts\Services\Front\SubscriptionServiceContract;
use InetStudio\Subscription\Contracts\Http\Requests\Front\SubscribeRequestContract;
use InetStudio\Subscription\Contracts\Http\Responses\Front\SubscribeResponseContract;

/**
 * Class SubscriptionController.
 */
class SubscriptionController extends Controller
{
    /**
     * Подписка пользователя.
     *
     * @param SubscriptionServiceContract $subscriptionService
     * @param SubscribeRequestContract $request
     *
     * @return SubscribeResponseContract
     */
    public function subscribe(SubscriptionServiceContract $subscriptionService, SubscribeRequestContract $request): SubscribeResponseContract
    {
        $result = $subscriptionService->subscribeByRequest($request);

        return app()->makeWith(SubscribeResponseContract::class, compact('result'));
    }
}
