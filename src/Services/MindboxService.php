<?php

namespace InetStudio\Subscription\Services;

use GuzzleHttp\Client;
use InetStudio\Subscription\Models\SubscriptionModel;
use InetStudio\Subscription\Contracts\SubscriptionServiceContact;

class MindboxService implements SubscriptionServiceContact
{
    private $secret;
    private $url;
    private $brand;
    private $point;
    private $userIP;

    public function __construct()
    {
        $request = request();
        $deviceUUID = $_COOKIE['mindboxDeviceUUID'];

        $this->secret = config('subscription.mindbox.secret');
        $this->brand = config('subscription.mindbox.brand');
        $this->point = config('subscription.mindbox.point');
        $this->url = config('subscription.mindbox.url').$deviceUUID;
        $this->userIP = $request->ip();
    }

    /**
     * Подписываем пользователя на рассылку.
     *
     * @param SubscriptionModel $subscription
     * @return bool
     */
    public function subscribe(SubscriptionModel $subscription)
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
    public function update(SubscriptionModel $subscription)
    {
        return true;
    }

    /**
     * Отписываем пользователя от рассылки.
     *
     * @param SubscriptionModel $subscription
     * @return bool
     */
    public function unsubscribe(SubscriptionModel $subscription)
    {
        return true;
    }
}
