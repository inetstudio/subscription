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
     * @var bool
     */
    protected $result;

    /**
     * SubscribeResponse constructor.
     *
     * @param bool $result
     */
    public function __construct(bool $result)
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
        return response()->json([
            'success' => $this->result,
            'message' => ($this->result)
                ? trans('subscription::messages.pending')
                : trans('subscription::messages.send_fail'),
        ]);
    }
}
