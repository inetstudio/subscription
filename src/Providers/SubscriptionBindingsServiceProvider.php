<?php

namespace InetStudio\Subscription\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * Class SubscriptionBindingsServiceProvider.
 */
class SubscriptionBindingsServiceProvider extends ServiceProvider
{
    /**
    * @var  bool
    */
    protected $defer = true;

    /**
    * @var  array
    */
    public $bindings = [
        'InetStudio\Subscription\Contracts\Managers\SubscriptionManagerContract' => 'InetStudio\Subscription\Managers\SubscriptionManager',
        'InetStudio\Subscription\Contracts\Repositories\SubscriptionRepositoryContract' => 'InetStudio\Subscription\Repositories\SubscriptionRepository',
        'InetStudio\Subscription\Contracts\Models\SubscriptionModelContract' => 'InetStudio\Subscription\Models\SubscriptionModel',
        'InetStudio\Subscription\Contracts\Transformers\Back\SubscriptionTransformerContract' => 'InetStudio\Subscription\Transformers\Back\SubscriptionTransformer',
        'InetStudio\Subscription\Contracts\Transformers\Back\SuggestionTransformerContract' => 'InetStudio\Subscription\Transformers\Back\SuggestionTransformer',
        'InetStudio\Subscription\Contracts\Http\Responses\Back\Subscription\DestroyResponseContract' => 'InetStudio\Subscription\Http\Responses\Back\Subscription\DestroyResponse',
        'InetStudio\Subscription\Contracts\Http\Responses\Back\Subscription\IndexResponseContract' => 'InetStudio\Subscription\Http\Responses\Back\Subscription\IndexResponse',
        'InetStudio\Subscription\Contracts\Http\Responses\Back\Subscription\FormResponseContract' => 'InetStudio\Subscription\Http\Responses\Back\Subscription\FormResponse',
        'InetStudio\Subscription\Contracts\Http\Responses\Back\Utility\SuggestionsResponseContract' => 'InetStudio\Subscription\Http\Responses\Back\Utility\SuggestionsResponse',
        'InetStudio\Subscription\Contracts\Http\Requests\Front\SubscribeRequestContract' => 'InetStudio\Subscription\Http\Requests\Front\SubscribeRequest',
        'InetStudio\Subscription\Contracts\Http\Controllers\Back\SubscriptionControllerContract' => 'InetStudio\Subscription\Http\Controllers\Back\SubscriptionController',
        'InetStudio\Subscription\Contracts\Http\Controllers\Back\SubscriptionUtilityControllerContract' => 'InetStudio\Subscription\Http\Controllers\Back\SubscriptionUtilityController',
        'InetStudio\Subscription\Contracts\Http\Controllers\Back\SubscriptionDataControllerContract' => 'InetStudio\Subscription\Http\Controllers\Back\SubscriptionDataController',
        'InetStudio\Subscription\Contracts\Events\Front\EmailCleanedEventContract' => 'InetStudio\Subscription\Events\Front\EmailCleanedEvent',
        'InetStudio\Subscription\Contracts\Events\Front\EmailUnsubscribedEventContract' => 'InetStudio\Subscription\Events\Front\EmailUnsubscribedEvent',
        'InetStudio\Subscription\Contracts\Events\Front\EmailDeletedEventContract' => 'InetStudio\Subscription\Events\Front\EmailDeletedEvent',
        'InetStudio\Subscription\Contracts\Events\Front\EmailPendingEventContract' => 'InetStudio\Subscription\Events\Front\EmailPendingEvent',
        'InetStudio\Subscription\Contracts\Events\Front\EmailSubscribedEventContract' => 'InetStudio\Subscription\Events\Front\EmailSubscribedEvent',
        'InetStudio\Subscription\Contracts\Observers\SubscriptionObserverContract' => 'InetStudio\Subscription\Observers\SubscriptionObserver',
        'InetStudio\Subscription\Contracts\Listeners\Front\SubscribeFromRequestListenerContract' => 'InetStudio\Subscription\Listeners\Front\SubscribeFromRequestListener',
        'InetStudio\Subscription\Contracts\Listeners\Front\AttachUserToSubscriptionListenerContract' => 'InetStudio\Subscription\Listeners\Front\AttachUserToSubscriptionListener',
        'InetStudio\Subscription\Contracts\Services\Back\SubscriptionDataTableServiceContract' => 'InetStudio\Subscription\Services\Back\SubscriptionDataTableService',
        'InetStudio\Subscription\Contracts\Services\Back\SubscriptionServiceContract' => 'InetStudio\Subscription\Services\Back\SubscriptionService',
        'InetStudio\Subscription\Contracts\Services\Front\SubscriptionServiceContract' => 'InetStudio\Subscription\Services\Front\SubscriptionService',
    ];

    /**
     * Получить сервисы от провайдера.
     *
     * @return  array
     */
    public function provides()
    {
        return array_keys($this->bindings);
    }
}
