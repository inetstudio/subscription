<?php

namespace InetStudio\Subscription\Services;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use DrewM\MailChimp\MailChimp;
use InetStudio\Subscription\Models\SubscriptionModel;
use InetStudio\Subscription\Contracts\SubscriptionServiceContract;

class MailchimpService implements SubscriptionServiceContract
{
    private $service;
    private $subscriptionList;
    private $interests;
    private $userData = [
        'personal' => 'merge_fields',
        'groups' => 'interests',
    ];

    /**
     * MailchimpService constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->service = new MailChimp($config['api_key']);
        $this->subscriptionList = $config['subscribers_list'];
        $this->interests = $this->array_change_key_case_unicode($config['interests'], CASE_LOWER);
    }

    /**
     * Подписываем пользователя на рассылку.
     *
     * @param SubscriptionModel $subscription
     * @return bool
     */
    public function subscribe(SubscriptionModel $subscription): bool
    {
        $exist = $this->checkUser($subscription->email);

        $this->caseAction($subscription, $exist);

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

        $exist = $this->checkUser($original['email']);

        $this->caseAction($subscription, $exist);

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
        $subscriberHash = $this->service->subscriberHash($subscription->email);

        $exist = $this->checkUser($subscription->email);

        if ($exist) {
            $this->service->patch('lists/'.$this->subscriptionList.'/'.$subscriberHash, [
                'status' => 'unsubscribed'
            ]);
        }

        return true;
    }

    /**
     * Проверяем пользователя на нахождение в листе.
     *
     * @param string $email
     * @return bool
     */
    private function checkUser(string $email): bool
    {
        $subscriberHash = $this->service->subscriberHash($email);
        $checkUserExist = $this->service->get('lists/'.$this->subscriptionList.'/members/'.$subscriberHash, []);

        return (isset($checkUserExist['id'])) ? true : false;
    }

    /**
     * Выбираем, что делать с пользователем.
     *
     * @param SubscriptionModel $subscription
     * @param $exist
     */
    private function caseAction(SubscriptionModel $subscription, $exist): void
    {
        if (! $exist) {
            $this->addUser($subscription);
        } else {
            $this->updateUser($subscription);
        }
    }

    /**
     * Добавляем пользователя в лист.
     *
     * @param SubscriptionModel $subscription
     * @return bool
     */
    private function addUser(SubscriptionModel $subscription): bool
    {
        $additionalData = $this->getAdditionalInfo($subscription);

        $options = array_merge([
            'email_address' => $subscription->email,
            'status' => 'pending',
        ], $additionalData);

        $this->service->post('lists/'.$this->subscriptionList.'/members', $options);

        return true;
    }

    /**
     * Обновляем данные пользователя.
     *
     * @param SubscriptionModel $subscription
     * @return bool
     */
    private function updateUser(SubscriptionModel $subscription): bool
    {
        $additionalData = $this->getAdditionalInfo($subscription);
        $status = ($subscription->is_subscribed == 0) ? 'unsubscribed' : 'subscribed';

        $subscriberHash = $this->service->subscriberHash($subscription->email);

        $options = array_merge([
            'email_address' => $subscription->email,
            'status' => $status,
        ], $additionalData);

        $this->service->patch('lists/'.$this->subscriptionList.'/members/'.$subscriberHash, $options);

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

    /**
     * Получаем дополнительные данные пользователя.
     *
     * @param SubscriptionModel $subscription
     * @return array
     */
    private function getAdditionalInfo(SubscriptionModel $subscription): array
    {
        $additionalData = $subscription->additional_info;

        $data = [];

        foreach ($this->userData as $key => $option) {
            if (isset($additionalData[$key]) && count($additionalData[$key]) > 0) {
                $method = 'prepare'.Str::studly($key).'Info';

                $data = array_merge($data, [
                    $option => $this->$method($additionalData[$key]),
                ]);
            }
        }

        return $data;
    }

    /**
     * Подготавливаем персональные данные.
     *
     * @param array $data
     * @return array
     */
    private function preparePersonalInfo(array $data): array
    {
        return $data;
    }

    /**
     * Подготавливаем данные по группам.
     *
     * @param array $data
     * @return array
     */
    private function prepareGroupsInfo(array $data): array
    {
        $data = $this->array_change_key_case_unicode($data, CASE_LOWER);

        $prepareData = [];

        foreach ($this->interests as $key => $id) {
            $prepareData[$id] = (isset($data[$key])) ? true : false;
        }

        return $prepareData;
    }

    /**
     * Смена регистра ключей в массиве с поддержкой юникода.
     *
     * @param array $arr
     * @param int $case
     * @return array
     */
    private function array_change_key_case_unicode(array $arr, int $case = CASE_LOWER): array
    {
        $case = ($case == CASE_LOWER) ? MB_CASE_LOWER : MB_CASE_UPPER;

        $returnArray = [];

        foreach ($arr as $key => $value) {
            $returnArray[mb_convert_case($key, $case, 'UTF-8')] = $value;
        }

        return $returnArray;
    }
}
