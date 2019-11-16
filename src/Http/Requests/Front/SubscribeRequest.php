<?php

namespace InetStudio\Subscription\Http\Requests\Front;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use InetStudio\Subscription\Contracts\Http\Requests\Front\SubscribeRequestContract;

/**
 * Class SubscribeRequest.
 */
class SubscribeRequest extends FormRequest implements SubscribeRequestContract
{
    /**
     * Определить, авторизован ли пользователь для этого запроса.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Сообщения об ошибках.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'email.required' => 'Поле «E-mail» обязательно для заполнения',
            'email.max' => 'Поле «E-mail» не должно превышать 255 символов',
            'email.email' => 'Поле «E-mail» содержит значение в некорректном формате',
            'email.unique' => 'Такой email уже существует',

            'policy-agree.required' => 'Обязательно для заполнения',
            'subscribe-agree.required' => 'Обязательно для заполнения',
        ];
    }

    /**
     * Правила проверки запроса.
     *
     * @return array
     */
    public function rules(): array
    {
        $usersService = app()->make('InetStudio\ACL\Users\Contracts\Services\Front\ItemsServiceContract');

        $uniqueRule = Rule::unique('subscription', 'email')->where(function ($query) {
            return $query->whereNull('deleted_at');
        });

        if ($user = $usersService->getUser()) {
            $uniqueRule->ignore($user->id, 'user_id');
        }

        return [
            'email' => [
                'required',
                'max:255',
                'email',
                $uniqueRule,
            ],
            'policy-agree' => 'required',
            'subscribe-agree' => 'required',
        ];
    }
}
