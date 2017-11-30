<?php

Route::group(['namespace' => 'InetStudio\Subscription\Http\Controllers\API'], function () {
    Route::group(['prefix' => 'module/subscription'], function () {
        Route::any('sync/{service}', 'SubscriptionController@sync')->name('api.subscription.sync');
    });
});
