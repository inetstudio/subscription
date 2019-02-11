<?php

namespace InetStudio\Subscription\Http\Controllers\Back;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use InetStudio\Subscription\Http\Responses\Back\Utility\SuggestionsResponse;
use InetStudio\Subscription\Contracts\Http\Controllers\Back\SubscriptionUtilityControllerContract;

/**
 * Class SubscriptionUtilityController.
 */
class SubscriptionUtilityController extends Controller implements SubscriptionUtilityControllerContract
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
        $this->services['subscription'] = app()->make(
            'InetStudio\Subscription\Contracts\Services\Back\SubscriptionServiceContract'
        );
    }

    /**
     * Возвращаем статьи для поля.
     *
     * @param Request $request
     *
     * @return SuggestionsResponse
     */
    public function getSuggestions(Request $request): SuggestionsResponse
    {
        $search = $request->get('q');
        $type = $request->get('type') ?? '';

        $suggestions = $this->services['subscription']->getSuggestions($search);

        return app()->makeWith(
            'InetStudio\Subscription\Http\Responses\Back\Utility\SuggestionsResponse',
            compact('suggestions', 'type')
        );
    }
}
