<?php

namespace InetStudio\Subscription\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

/**
 * Class SubscriptionServiceProvider.
 */
class SubscriptionServiceProvider extends ServiceProvider
{
    /**
     * Загрузка сервиса.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->registerConsoleCommands();
        $this->registerPublishes();
        $this->registerRoutes();
        $this->registerViews();
        $this->registerTranslations();
        $this->registerEvents();
        $this->registerObservers();
    }

    /**
     * Регистрация привязки в контейнере.
     *
     * @return void
     */
    public function register(): void
    {
        $this->registerBindings();
    }

    /**
     * Регистрация команд.
     *
     * @return void
     */
    protected function registerConsoleCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                'InetStudio\Subscription\Console\Commands\SetupCommand',
            ]);
        }
    }

    /**
     * Регистрация ресурсов.
     *
     * @return void
     */
    protected function registerPublishes(): void
    {
        $this->publishes([
            __DIR__.'/../../config/subscription.php' => config_path('subscription.php'),
        ], 'config');

        if ($this->app->runningInConsole()) {
            if (! Schema::hasTable('subscription')) {
                $timestamp = date('Y_m_d_His', time());
                $this->publishes([
                    __DIR__.'/../../database/migrations/create_subscription_tables.php.stub' => database_path('migrations/'.$timestamp.'_create_subscription_tables.php'),
                ], 'migrations');
            }
        }
    }

    /**
     * Регистрация путей.
     *
     * @return void
     */
    protected function registerRoutes(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../../routes/web.php');
        $this->loadRoutesFrom(__DIR__.'/../../routes/api.php');
    }

    /**
     * Регистрация представлений.
     *
     * @return void
     */
    protected function registerViews(): void
    {
        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'admin.module.subscription');
    }

    /**
     * Регистрация переводов.
     *
     * @return void
     */
    protected function registerTranslations(): void
    {
        $this->loadTranslationsFrom(__DIR__.'/../../resources/lang', 'subscription');
    }

    /**
     * Регистрация событий.
     *
     * @return void
     */
    protected function registerEvents(): void
    {
        Event::listen(
            'InetStudio\ACL\Activations\Contracts\Events\Front\ActivatedEventContract',
            'InetStudio\Subscription\Contracts\Listeners\Front\AttachUserToSubscriptionListenerContract'
        );

        Event::listen(
            'InetStudio\ACL\Users\Contracts\Events\Front\SocialRegisteredEventContract',
            'InetStudio\Subscription\Contracts\Listeners\Front\AttachUserToSubscriptionListenerContract'
        );
    }

    /**
     * Регистрация наблюдателей.
     *
     * @return void
     */
    public function registerObservers(): void
    {
        $this->app->make('InetStudio\Subscription\Contracts\Models\SubscriptionModelContract')::observe($this->app->make('InetStudio\Subscription\Contracts\Observers\SubscriptionObserverContract'));
    }

    /**
     * Регистрация привязок, алиасов и сторонних провайдеров сервисов.
     *
     * @return void
     */
    public function registerBindings(): void
    {
        $driver = config('subscription.driver');

        $this->app->singleton('InetStudio\Subscription\Contracts\Services\SubscriptionServices\SubscriptionServiceContract', function ($app) use ($driver) {
            return app()->makeWith('InetStudio\Subscription\Contracts\Managers\SubscriptionManagerContract', compact('app'))
                ->with($driver);
        });
    }
}
