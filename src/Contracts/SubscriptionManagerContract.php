<?php

namespace InetStudio\Subscription\Contracts;

interface SubscriptionManagerContract
{
    /**
     * Получаем реализацию сервиса подписок.
     *
     * @param string $driver
     * @return SubscriptionServiceContract
     */
    public function driver($driver = null);
}
