<?php

Route::group(['namespace' => 'InetStudio\Subscription\Http\Controllers\Back'], function () {
    Route::group(['middleware' => 'web', 'prefix' => 'back'], function () {
        Route::group(['middleware' => 'back.auth'], function () {
            Route::any('subscription/data', 'SubscriptionController@data')->name('back.subscription.data');
            Route::resource('subscription', 'SubscriptionController', ['only' => [
                'index', 'edit', 'destroy',
            ], 'as' => 'back']);
        });
    });
});
