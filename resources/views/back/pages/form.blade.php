@extends('admin::back.layouts.app')

@php
    $title = ($item->id) ? 'Просмотр подписчика' : '';
@endphp

@section('title', $title)

@section('content')

    @push('breadcrumbs')
        @include('admin.module.subscription::back.partials.breadcrumbs')
        <li>
            <a href="{{ route('back.subscription.index') }}">Подписчики</a>
        </li>
    @endpush

    <div class="row m-sm">
        <a class="btn btn-white" href="{{ route('back.subscription.index') }}">
            <i class="fa fa-arrow-left"></i> Вернуться назад
        </a>
    </div>

    <div class="wrapper wrapper-content form-horizontal">
        <div class="row">
            <div class="col-lg-12">
                <div class="panel-group float-e-margins" id="mainAccordion">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h5 class="panel-title">
                                <a data-toggle="collapse" data-parent="#mainAccordion" href="#collapseMain" aria-expanded="true">Основная информация</a>
                            </h5>
                        </div>
                        <div id="collapseMain" class="panel-collapse collapse in" aria-expanded="true">
                            <div class="panel-body">

                                {!! Form::string('email', $item->email, [
                                    'label' => [
                                        'title' => 'Email',
                                    ],
                                    'field' => [
                                        'class' => 'form-control',
                                        'disabled' => true,
                                    ],
                                ]) !!}

                                @if (count($item->additional_info) > 0)
                                    <div class="form-group ">
                                        <label for="message" class="col-sm-2 control-label">Дополнительная информация</label>

                                        <div class="col-sm-10">
                                            <pre class="json-data">@json($item->additional_info)</pre>
                                        </div>
                                    </div>
                                    <div class="hr-line-dashed"></div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
