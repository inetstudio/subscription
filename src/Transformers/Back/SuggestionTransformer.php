<?php

namespace InetStudio\Subscription\Transformers\Back;

use League\Fractal\TransformerAbstract;
use League\Fractal\Resource\Collection as FractalCollection;
use InetStudio\Subscription\Contracts\Models\SubscriptionModelContract;
use InetStudio\Subscription\Contracts\Transformers\Back\SuggestionTransformerContract;

/**
 * Class SuggestionTransformer.
 */
class SuggestionTransformer extends TransformerAbstract implements SuggestionTransformerContract
{
    /**
     * @var string
     */
    protected $type;

    /**
     * SuggestionTransformer constructor.
     *
     * @param $type
     */
    public function __construct($type)
    {
        $this->type = $type;
    }

    /**
     * Подготовка данных для отображения в выпадающих списках.
     *
     * @param SubscriptionModelContract $item
     *
     * @return array
     *
     * @throws \Throwable
     */
    public function transform(SubscriptionModelContract $item): array
    {
        if ($this->type && $this->type == 'autocomplete') {

            return [
                'value' => $item->getAttribute('email'),
                'data' => [
                    'id' => $item->getAttribute('id'),
                    'name' => $item->getAttribute('email'),
                ],
            ];
        } else {
            return [
                'id' => $item->getAttribute('id'),
                'name' => $item->getAttribute('email'),
            ];
        }
    }

    /**
     * Обработка коллекции объектов.
     *
     * @param $items
     *
     * @return FractalCollection
     */
    public function transformCollection($items): FractalCollection
    {
        return new FractalCollection($items, $this);
    }
}
