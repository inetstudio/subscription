<?php

namespace InetStudio\Subscription\Http\Controllers\Back;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use InetStudio\Subscription\Contracts\Services\Back\SubscriptionServiceContract;
use InetStudio\Subscription\Contracts\Http\Responses\Back\Utility\SuggestionsResponseContract;
use InetStudio\Subscription\Contracts\Http\Controllers\Back\SubscriptionUtilityControllerContract;

/**
 * Class SubscriptionUtilityController.
 */
class SubscriptionUtilityController extends Controller implements SubscriptionUtilityControllerContract
{
    /**
     * Возвращаем статьи для поля.
     *
     * @param SubscriptionServiceContract $subscriptionService
     * @param Request $request
     *
     * @return SuggestionsResponseContract
     */
    public function getSuggestions(SubscriptionServiceContract $subscriptionService, Request $request): SuggestionsResponseContract
    {
        $search = $request->get('q');
        $type = $request->get('type') ?? '';

        $suggestions = $subscriptionService->getSuggestions($search);

        return app()->makeWith(
            SuggestionsResponseContract::class,
            compact('suggestions', 'type')
        );
    }
}
