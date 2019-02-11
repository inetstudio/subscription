<?php

namespace InetStudio\Subscription\Http\Responses\Back\Subscription;

use Illuminate\View\View;
use Illuminate\Contracts\Support\Responsable;
use InetStudio\Subscription\Contracts\Http\Responses\Back\Subscription\FormResponseContract;

/**
 * Class FormResponse.
 */
class FormResponse implements FormResponseContract, Responsable
{
    /**
     * @var array
     */
    protected $data;

    /**
     * FormResponse constructor.
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Возвращаем ответ при открытии формы объекта.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return View
     */
    public function toResponse($request): View
    {
        return view('admin.module.subscription::back.pages.form', $this->data);
    }
}
