<?php

namespace InetStudio\Subscription\Services\Front;

use Illuminate\Support\Arr;
use InetStudio\AdminPanel\Base\Services\BaseService;
use InetStudio\Subscription\Contracts\Models\SubscriptionModelContract;
use InetStudio\Subscription\Contracts\Services\Front\SubscriptionServiceContract;

/**
 * Class SubscriptionService.
 */
class SubscriptionService extends BaseService implements SubscriptionServiceContract
{
    /**
     * SubscriptionService constructor.
     */
    public function __construct()
    {
        parent::__construct(app()->make('InetStudio\Subscription\Contracts\Models\SubscriptionModelContract'));
    }

    /**
     * Сохраняем подписчика.
     *
     * @param $request
     *
     * @return array
     */
    public function subscribeByRequest($request): array
    {
        $subscriptionData = $this->getRequestSubscriptionData($request);

        return $this->subscribeByData($subscriptionData);
    }

    /**
     * Сохраняем подписчика.
     *
     * @param array $subscriptionData
     *
     * @return array
     */
    public function subscribeByData(array $subscriptionData): array
    {
        $item = $this->getItem($subscriptionData['email']);
        $item = $this->restoreItem($item);

        if (! $item || $item->status != 'subscribed') {
            $subscriptionData['status'] = 'pending';

            $message = trans('subscription::messages.pending');
        } else {
            $message = trans('subscription::messages.update');
        }

        $itemId = $item['id'] ?? 0;
        $this->saveModel($subscriptionData, $itemId);

        return [
            'message' => $message,
            'subscription' => $item,
        ];
    }

    /**
     * Отписываем пользователя.
     *
     * @param $request
     *
     * @return array
     */
    public function unsubscribeByRequest($request): array
    {
        $subscriptionData = $this->getRequestSubscriptionData($request);

        return $this->unsubscribeByData($subscriptionData);
    }

    /**
     * Отписываем пользователя.
     *
     * @param array $subscriptionData
     *
     * @return array
     */
    public function unsubscribeByData(array $subscriptionData): array
    {
        $item = $this->getItem($subscriptionData['email']);
        $item = $this->restoreItem($item);

        if ($item) {
            $subscriptionData['status'] = 'unsubscribed';

            $itemId = $item['id'] ?? 0;
            $this->saveModel($subscriptionData, $itemId);

            return [
                'message' => trans('subscription::messages.unsubscribed'),
                'subscription' => $item,
            ];
        } else {
            return [
                'message' => trans('subscription::messages.not_found'),
                'subscription' => $item,
            ];
        }
    }

    /**
     * Получаем информацию по подписке из запроса.
     *
     * @param $request
     *
     * @return array
     */
    protected function getRequestSubscriptionData($request)
    {
        $usersService = app()->make('InetStudio\ACL\Users\Contracts\Services\Front\UsersServiceContract');

        $email = $usersService->getUserEmail($request);
        $additional_info = ($request->filled('subscriptionData')) ? Arr::changeKeysCase($request->get('subscriptionData')) : [];

        $subscriptionData = [
            'user_id' => $usersService->getUserId($email),
            'email' => $email,
            'additional_info' => $additional_info,
        ];

        return $subscriptionData;
    }

    /**
     * Получаем подписчика.
     *
     * @param string $email
     *
     * @return SubscriptionModelContract|null
     */
    protected function getItem(string $email): ?SubscriptionModelContract
    {
        $items = $this->model::withTrashed()
            ->where([
                ['email', '=', $email],
            ])->get();

        return $items->first();
    }

    /**
     * Восстанавливаем подписчика.
     *
     * @param $item
     *
     * @return SubscriptionModelContract|null
     */
    protected function restoreItem($item): ?SubscriptionModelContract
    {
        if ($item && $item->trashed()) {
            $model = $this->model;

            $dispatcher = $model::getEventDispatcher();
            $model::unsetEventDispatcher();

            $item->restore();

            $model::setEventDispatcher($dispatcher);
        }

        return $item;
    }
}
