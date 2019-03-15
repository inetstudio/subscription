<?php

namespace InetStudio\Subscription\Http\Responses\Front;

use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Support\Responsable;
use InetStudio\Subscription\Contracts\Http\Responses\Front\SubscribeResponseContract;

/**
 * Class SubscribeResponse.
 */
class SubscribeResponse implements SubscribeResponseContract, Responsable
{
    /**
     * @var array
     */
    protected $result;

    /**
     * SubscribeResponse constructor.
     *
     * @param array $result
     */
    public function __construct(array $result)
    {
        $this->result = $result;
    }

    /**
     * Возвращаем ответ при удалении объекта.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function toResponse($request): JsonResponse
    {
        return response()->json($this->result);
    }
}
