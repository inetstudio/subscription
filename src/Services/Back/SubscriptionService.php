<?php

namespace InetStudio\Subscription\Services\Back;

use League\Fractal\Manager;
use Illuminate\Support\Facades\DB;
use League\Fractal\Serializer\DataArraySerializer;
use InetStudio\AdminPanel\Services\Back\BaseService;
use InetStudio\Subscription\Contracts\Services\Back\SubscriptionServiceContract;

/**
 * Class SubscriptionService.
 */
class SubscriptionService extends BaseService implements SubscriptionServiceContract
{
    protected $colors = [
        'pending' => 'warning',
        'subscribed' => 'primary',
        'unsubscribed' => 'danger',
        'cleaned' => 'default',
    ];

    protected $titles = [
        'pending' => 'Ожидают подтверждения',
        'subscribed' => 'Активные',
        'unsubscribed' => 'Неактивные',
        'cleaned' => 'Некорректные адреса',
    ];

    /**
     * SubscriptionService constructor.
     */
    public function __construct()
    {
        parent::__construct(app()->make('InetStudio\Subscription\Contracts\Repositories\SubscriptionRepositoryContract'));
    }

    /**
     * Получаем подсказки.
     *
     * @param string $search
     * @param $type
     *
     * @return array
     */
    public function getSuggestions(string $search, $type): array
    {
        $items = $this->repository->searchItems([['email', 'LIKE', '%'.$search.'%']]);

        $resource = (app()->makeWith('InetStudio\Subscription\Contracts\Transformers\Back\SuggestionTransformerContract', [
            'type' => $type,
        ]))->transformCollection($items);

        $manager = new Manager();
        $manager->setSerializer(new DataArraySerializer());

        $transformation = $manager->createData($resource)->toArray();

        if ($type && $type == 'autocomplete') {
            $data['suggestions'] = $transformation['data'];
        } else {
            $data['items'] = $transformation['data'];
        }

        return $data;
    }

    /**
     * Возвращаем статистику подписок по статусу.
     *
     * @return mixed
     */
    public function getSubscriptionStatisticByStatus()
    {
        $subscriptions = $this->repository->getItemsQuery()
            ->select(['status', DB::raw('count(*) as total')])
            ->groupBy('status')
            ->get();

        return $subscriptions;
    }

    /**
     * Возвращаем цвета статусов подписок.
     *
     * @return array
     */
    public function getStatusesColors(): array
    {
        return $this->colors;
    }

    /**
     * Возвращаем заголовки статусов подписок.
     *
     * @return array
     */
    public function getStatusesTitles(): array
    {
        return $this->titles;
    }
}
