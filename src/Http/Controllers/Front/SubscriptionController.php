<?php

namespace InetStudio\Subscription\Http\Controllers\Front;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use InetStudio\Subscription\Managers\SubscriptionManager;

/**
 * Контроллер для управления подписками.
 *
 * Class ContestByTagStatusesController
 */
class SubscriptionController extends Controller
{
    /**
     * Синхронизируем локальные данные с сервисом подписок.
     *
     * @param Request $request
     * @param SubscriptionManager $subscriptionManager
     * @param string $service
     */
    public function sync(Request $request, SubscriptionManager $subscriptionManager, string $service): void
    {
        $subscriptionService = $subscriptionManager->with($service);
        $subscriptionService->sync($request);
    }
}
