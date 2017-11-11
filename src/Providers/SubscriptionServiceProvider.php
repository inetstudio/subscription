<?php

namespace InetStudio\Subscription\Providers;

use Illuminate\Support\Str;
use Illuminate\Support\ServiceProvider;
use InetStudio\Subscription\Models\SubscriptionModel;
use InetStudio\Subscription\Console\Commands\SetupCommand;
use InetStudio\Subscription\Observers\SubscriptionObserver;
use InetStudio\Subscription\Contracts\SubscriptionServiceContact;

class SubscriptionServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->registerConsoleCommands();
        $this->registerPublishes();
        $this->registerRoutes();
        $this->registerViews();
        $this->registerObservers();
        $this->registerBindings();
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Register Subscription's console commands.
     *
     * @return void
     */
    protected function registerConsoleCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                SetupCommand::class,
            ]);
        }
    }

    /**
     * Setup the resource publishing groups for Subscription.
     *
     * @return void
     */
    protected function registerPublishes(): void
    {
        $this->publishes([
            __DIR__.'/../../config/subscription.php' => config_path('subscription.php'),
        ], 'config');

        if ($this->app->runningInConsole()) {
            if (! class_exists('CreateSubscriptionTables')) {
                $timestamp = date('Y_m_d_His', time());
                $this->publishes([
                    __DIR__.'/../../database/migrations/create_subscription_tables.php.stub' => database_path('migrations/'.$timestamp.'_create_subscription_tables.php'),
                ], 'migrations');
            }
        }
    }

    /**
     * Register Subscription's routes.
     *
     * @return void
     */
    protected function registerRoutes(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../../routes/web.php');
    }

    /**
     * Register Subscription's views.
     *
     * @return void
     */
    protected function registerViews(): void
    {
        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'admin.module.subscription');
    }

    /**
     * Register Subscription's observers.
     *
     * @return void
     */
    public function registerObservers(): void
    {
        SubscriptionModel::observe(SubscriptionObserver::class);
    }

    /**
     * Register Subscription's services bindings.
     *
     * @return void
     */
    public function registerBindings()
    {
        $availableDrivers = ['mailchimp', 'mailgun', 'mindbox', 'local'];
        $configDriver = config('subscription.driver');

        $driver = (in_array($configDriver, $availableDrivers)) ? $configDriver : 'local';

        $this->app->bind(
            SubscriptionServiceContact::class,
            'InetStudio\Subscription\Services\\'.Str::ucfirst($driver).'Service'
        );
    }
}
