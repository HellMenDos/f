@extends('layouts.admin.app')

@section('heading', $heading)
@section('title', $title)

@section('breadcrumbs')
    @breadcrumbs([
        'breadcrumbs' => [
            'admin.dashboard.index'        => 'Главная',
            'admin.services.request.index' => 'Заявки',
            'admin.services.request.show'  => $title,
        ]
    ])
    @endbreadcrumbs
@endsection

@section('after-scripts')

@endsection

@section('content')
    <div class="col-md-10 col-md-offset-1">
        <section class="block">

            <div class="action">
                @haspermission('create_services')
                    @if ($serviceRequest->status < 3)
                        @if ($serviceRequest->status != 1)
                            <a href="{{ route('admin.services.request.accept', $serviceRequest->id) }}" class="btn btn-info btn-xs">
                                <span class="hidden-xs">Принять заявку</span>
                            </a>
                        @endif
                        <a href="{{ route('admin.services.request.delete', $serviceRequest->id) }}" class="btn btn-default btn-xs">
                            <span class="hidden-xs">Удалить заявку</span>
                        </a>
                        <a href="{{ route('admin.services.request.edit', $serviceRequest->id) }}" class="btn btn-warning btn-xs">
                            <span class="hidden-xs">Редактировать заявку</span>
                        </a>
                        <a href="{{ route('admin.services.request.complete', $serviceRequest->id) }}" class="btn btn-success btn-xs">
                            <span class="hidden-xs">Заявка выполнена</span>
                        </a>
                    @endif
                @endauth
            </div>
            <br>

            <header class="block_header">
                <h2><i class="fa fa-edit"></i> Плановые данные</h2>
            </header>

            <div class="block_body">

                <table class="table table-striped">
                    @if ($planningServiceRequest['service_id'])
                    <tr>
                        <th>Наименование услуги</th>
                        <td>{{ $planningServiceRequest['title'] ?? Str::limit(strip_tags(html_decode($serviceRequest['description'] )), 35)  }}</td>
                    </tr>
                    <tr>
                        <th>Количество</th>
                        <td>{{ $planningServiceRequest['quantity'] }}</td>
                    </tr>
                    <tr>
                        <th>Цена, руб.</th>
                        <td>{{ $planningServiceRequest['price'] ?? 0 }}</td>
                    </tr>
                    <tr>
                        <th>Итого, руб.</th>
                        <td><b>{{ $planningServiceRequest['price'] * $planningServiceRequest['quantity'] }}</b></td>
                    </tr>
                    <tr>
                        <th>Дата и время исполнения</th>
                        <td>{{ $planningServiceRequest['execution_date'] ?? 'не указано'}}</td>
                    </tr>
                    <tr>
                        <th>Комментарий</th>
                        <td>{{ strip_tags(html_decode($planningServiceRequest['description'] )) }}</td>
                    </tr>
                    @else
                        <tr>
                            <th>Текст заявки</th>
                            <td>{{ $planningServiceRequest['text_free'] }}</td>
                        </tr>
                    @endif
                </table>

            </div>
        </section>
    </div>

    <div class="col-md-10 col-md-offset-1">
        <section class="block">
            <header class="block_header">
                <h2><i class="fa fa-edit"></i> Фактические данные</h2>
            </header>
            <div class="block_body">

                <table class="table table-striped">
                    <tr>
                        <th>Наименование услуги</th>
                        <td>{{ $serviceRequest->title ?? 'не указано' }}</td>
                    </tr>
                    <tr>
                        <th>Количество</th>
                        <td>{{ $serviceRequest->quantity ?? 'не указано' }}</td>
                    </tr>
                    <tr>
                        <th>Цена, руб.</th>
                        <td>{{ $serviceRequest->price ?? 'не указано' }}</td>
                    </tr>
                    <tr>
                        <th>Итого, руб.</th>
                        <td><b>{{ $serviceRequest->price * $serviceRequest->quantity ?? 0 }}</b></td>
                    </tr>
                    <tr>
                        <th>Дата и время исполнения</th>
                        <td>{{ $serviceRequest->execution_date ?? 'не указано'}}</td>
                    </tr>

                </table>

                <div class="form-actions">
                    {{--
                                    <a class="btn btn-primary" href="{{ route('admin.users.edit', $user->user_id) }}">Редактировать</a>
                    --}}
                    <a class="btn btn-default" href="{{ route('admin.services.request.index') }}">Назад</a>
                </div>
            </div>
        </section>
    </div>
@endsection
