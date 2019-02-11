<?php

Route::group([
    'namespace' => 'InetStudio\Subscription\Contracts\Http\Controllers\Back',
    'middleware' => ['web', 'back.auth'],
    'prefix' => 'back',
], function () {
    Route::any('subscription/data', 'SubscriptionDataControllerContract@data')->name('back.subscription.data.index');
    Route::post('subscription/slug', 'SubscriptionUtilityControllerContract@getSlug')->name('back.subscription.getSlug');
    Route::post('subscription/suggestions', 'SubscriptionUtilityControllerContract@getSuggestions')->name('back.subscription.getSuggestions');

    Route::resource('subscription', 'SubscriptionControllerContract', ['only' => [
        'index', 'edit', 'destroy',
    ], 'as' => 'back']);
});

Route::group(['namespace' => 'InetStudio\Subscription\Http\Controllers\Front'], function () {
    Route::group(['middleware' => 'web'], function () {
        Route::post('subscribe', 'SubscriptionController@subscribe')->name('front.subscription.subscribe');
    });
});
