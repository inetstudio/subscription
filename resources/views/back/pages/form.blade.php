@extends('admin::back.layouts.app')

@php
    $title = ($item->id) ? 'Просмотр подписчика' : '';
@endphp

@section('title', $title)

@section('content')

    @push('breadcrumbs')
        @include('admin.module.subscription::back.partials.breadcrumbs.form')
    @endpush

    <div class="wrapper wrapper-content">
        <div class="ibox">
            <div class="ibox-title">
                <a class="btn btn-sm btn-white" href="{{ route('back.subscription.index') }}">
                    <i class="fa fa-arrow-left"></i> Вернуться назад
                </a>
            </div>
        </div>

        {!! Form::info() !!}

        {!! Form::open(['url' => '#', 'id' => 'mainForm', 'enctype' => 'multipart/form-data']) !!}

            @if ($item->id)
                {{ method_field('PUT') }}
            @endif
    
            {!! Form::hidden('subscription_id', (! $item->id) ? '' : $item->id, ['id' => 'object-id']) !!}
    
            {!! Form::hidden('subscription_type', get_class($item), ['id' => 'object-type']) !!}

            <div class="ibox">
                <div class="ibox-title">
                    {!! Form::buttons('', '', ['back' => 'back.subscription.index']) !!}
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="panel-group float-e-margins" id="mainAccordion">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h5 class="panel-title">
                                            <a data-toggle="collapse" data-parent="#mainAccordion" href="#collapseMain" aria-expanded="true">Основная информация</a>
                                        </h5>
                                    </div>
                                    <div id="collapseMain" class="collapse show" aria-expanded="true">
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
                                                <div class="form-group row">
                                                    <label for="message" class="col-sm-2 col-form-label font-bold">Дополнительная информация</label>

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
                <div class="ibox-footer">
                    {!! Form::buttons('', '', ['back' => 'back.subscription.index']) !!}
                </div>
            </div>

        {!! Form::close()!!}
    </div>
@endsection
