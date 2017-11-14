<?php

namespace InetStudio\Subscription\Managers;

use Illuminate\Support\Manager;
use InetStudio\Subscription\Services\LocalService;
use InetStudio\Subscription\Services\MailgunService;
use InetStudio\Subscription\Services\MindboxService;
use InetStudio\Subscription\Services\MailchimpService;

class SubscriptionManager extends Manager
{
    /**
     * Возвращаем сервис подписки.
     *
     * @param string $driver
     * @return mixed
     */
    public function with($driver)
    {
        return $this->driver($driver);
    }

    /**
     * Возвращаем сервис для локальной подписки.
     *
     * @return LocalService
     */
    protected function createLocalDriver(): LocalService
    {
        return new LocalService();
    }

    /**
     * Возвращаем сервис для интеграции с Mailchimp.
     *
     * @return MailchimpService
     */
    protected function createMailchimpDriver(): MailchimpService
    {
        $config = $this->app['config']['subscription.mailchimp'];

        return new MailchimpService($config);
    }

    /**
     * Возвращаем сервис для интеграции с Mailgun.
     *
     * @return MailgunService
     */
    protected function createMailgunDriver(): MailgunService
    {
        $config = $this->app['config']['subscription.mailgun'];

        return new MailgunService($config);
    }

    /**
     * Возвращаем сервис для интеграции с Mindbox.
     *
     * @return MindboxService
     */
    protected function createMindboxDriver(): MindboxService
    {
        $config = $this->app['config']['subscription.mindbox'];

        return new MindboxService($config);
    }

    /**
     * Возвращаем имя драйвера по умолчанию.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        return 'local';
    }
}
