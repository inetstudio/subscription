<?php

namespace InetStudio\Subscription\Services;

use Mailgun\Mailgun;
use Illuminate\Http\Request;
use InetStudio\Subscription\Models\SubscriptionModel;
use InetStudio\Subscription\Contracts\SubscriptionServiceContract;

class MailgunService implements SubscriptionServiceContract
{
    private $service;
    private $subscriptionList;

    /**
     * MailgunService constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->service = new Mailgun($config['api_key']);
        $this->subscriptionList = $config['subscribers_list'];
    }

    /**
     * Подписываем пользователя на рассылку.
     *
     * @param SubscriptionModel $subscription
     * @return bool
     */
    public function subscribe(SubscriptionModel $subscription): bool
    {
        $status = ($subscription->is_subscribed == 0) ? 'unsubscribed' : 'subscribed';

        $this->service->post('lists/'.$this->subscriptionList.'/members', [
            'subscribed' => $status,
            'vars' => $this->getAdditionalInfo($subscription)
        ]);

        return true;
    }

    /**
     * Обновляем информацию подписчика.
     *
     * @param SubscriptionModel $subscription
     * @return bool
     */
    public function update(SubscriptionModel $subscription): bool
    {
        $original = $subscription->getOriginal();
        $status = ($subscription->is_subscribed == 0) ? 'unsubscribed' : 'subscribed';

        $this->service->put('lists/'.$this->subscriptionList.'/members/'.$original, [
            'subscribed' => $status,
            'vars' => $this->getAdditionalInfo($subscription)
        ]);

        return true;
    }

    /**
     * Отписываем пользователя от рассылки.
     *
     * @param SubscriptionModel $subscription
     * @return bool
     */
    public function unsubscribe(SubscriptionModel $subscription): bool
    {
        $this->service->put('lists/'.$this->subscriptionList.'/members/'.$subscription->email, [
            'subscribed' => false,
        ]);

        return true;
    }

    /**
     * Удаляем пользователя из листа рассылки.
     *
     * @param SubscriptionModel $subscription
     * @return bool
     */
    public function delete(SubscriptionModel $subscription): bool
    {
        return true;
    }

    /**
     * Получаем дополнительные данные пользователя.
     *
     * @param SubscriptionModel $subscription
     * @return array
     */
    private function getAdditionalInfo(SubscriptionModel $subscription): array
    {
        return json_encode(array_change_key_case($subscription->additional_info, CASE_UPPER));
    }

    /**
     * Синхронизируем локальные данные с сервисом подписок.
     *
     * @param Request $request
     * @return bool
     */
    public function sync(Request $request): bool
    {
        return true;
    }
}
