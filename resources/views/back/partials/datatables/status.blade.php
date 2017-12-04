@php
    $colors = [
        'pending' => 'warning',
        'subscribed' => 'primary',
        'unsubscribed' => 'danger',
        'cleaned' => 'default',
    ];
@endphp
<span class="label label-{{ (isset($colors[$status])) ? $colors[$status] : 'info' }}">{{ $status }}</span>
