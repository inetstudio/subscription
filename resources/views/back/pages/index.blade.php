@extends('admin::back.layouts.app')

@php
    $title = 'Подписчики';
@endphp

@section('title', $title)

@section('content')

    @push('breadcrumbs')
        @include('admin.module.subscription::back.partials.breadcrumbs')
    @endpush

    <div class="wrapper wrapper-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-content">
                        <div class="table-responsive">
                            {{ $table->table(['class' => 'table table-striped table-bordered table-hover dataTable']) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@pushonce('scripts:datatables_subscription_index')
    {!! $table->scripts() !!}
@endpushonce
