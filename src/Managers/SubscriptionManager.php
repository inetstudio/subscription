<?php

namespace InetStudio\Subscription\Managers;

use Illuminate\Support\Manager;
use InetStudio\Subscription\Services\SubscriptionServices\LocalService;
use InetStudio\Subscription\Services\SubscriptionServices\MindboxService;
use InetStudio\Subscription\Services\SubscriptionServices\LeadplanService;
use InetStudio\Subscription\Services\SubscriptionServices\MailchimpService;
use InetStudio\Subscription\Contracts\Managers\SubscriptionManagerContract;

/**
 * Class SubscriptionManager.
 */
class SubscriptionManager extends Manager implements SubscriptionManagerContract
{
    /**
     * Возвращаем сервис подписки.
     *
     * @param string $driver
     *
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
     *
     * @throws \Exception
     */
    protected function createMailchimpDriver(): MailchimpService
    {
        $config = $this->app['config']['subscription.mailchimp'];

        return new MailchimpService($config);
    }

    /**
     * Возвращаем сервис для интеграции с Mindbox.
     *
     * @return MindboxService
     */
    protected function createMindboxDriver(): MindboxService
    {
        return new MindboxService();
    }

    /**
     * Возвращаем сервис для интеграции с Leadplan.
     *
     * @return LeadplanService
     */
    protected function createLeadplanDriver(): LeadplanService
    {
        return new LeadplanService();
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
