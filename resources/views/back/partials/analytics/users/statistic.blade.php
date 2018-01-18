@php
    $colors = [
        'pending' => 'warning',
        'subscribed' => 'primary',
        'unsubscribed' => 'danger',
        'cleaned' => 'default',
    ];

    $titles = [
        'pending' => 'Ожидают подтверждения',
        'subscribed' => 'Активные',
        'unsubscribed' => 'Неактивные',
        'cleaned' => 'Некорректные адреса',
    ];
@endphp

<div class="ibox float-e-margins">
    <div class="ibox-content">
        <h2>Подписки</h2>
        <ul class="todo-list m-t">
            @foreach ($subscriptions as $subscription)
                <li>
                    <small class="label label-{{ (isset($colors[$subscription->status])) ? $colors[$subscription->status] : 'info' }}">{{ $subscription->total }}</small>
                    <span class="m-l-xs">{{ (isset($titles[$subscription->status])) ? $titles[$subscription->status] : 'Не удалось определить статус' }}</span>
                </li>
            @endforeach
            <hr>
            <li>
                <small class="label label-default">{{ $subscriptions->sum('total') }}</small>
                <span class="m-l-xs">Всего</span>
            </li>
        </ul>
    </div>
</div>
