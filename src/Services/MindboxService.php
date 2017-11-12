<?php

namespace InetStudio\Subscription\Services;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use InetStudio\Subscription\Models\SubscriptionModel;
use InetStudio\Subscription\Contracts\SubscriptionServiceContract;

class MindboxService implements SubscriptionServiceContract
{
    private $secret;
    private $url;
    private $brand;
    private $point;
    private $userIP;

    /**
     * MindboxService constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        $request = request();
        $deviceUUID = $_COOKIE['mindboxDeviceUUID'];

        $this->secret = $config['secret'];
        $this->brand = $config['brand'];
        $this->point = $config['point'];
        $this->url = $config['url'].$deviceUUID;
        $this->userIP = $request->ip();
    }

    /**
     * Подписываем пользователя на рассылку.
     *
     * @param SubscriptionModel $subscription
     * @return bool
     */
    public function subscribe(SubscriptionModel $subscription): bool
    {
        $client = new Client();

        $response = $client->post($this->url, [
            'body' => '
            <operation>
              <customer>
                 <email>'.$subscription->email.'</email>
                 <subscriptions>
                  <subscription>
                    <brand>'.$this->brand.'</brand>
                    <isSubscribed>true</isSubscribed>
                    <valueByDefault>false</valueByDefault>
                  </subscription>
                </subscriptions>
              </customer>
              <pointOfContact>'.$this->point.'</pointOfContact>
            </operation>',
            'headers' => [
                'Accept' => 'application/xml',
                'Content-Type' => 'application/xml',
                'Authorization' => 'Mindbox secretKey="'.$this->secret.'"',
                'X-Customer-IP' => $this->userIP,
            ],
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
        return true;
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
