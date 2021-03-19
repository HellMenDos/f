@extends('layouts.admin.app')

@section('heading', $heading)
@section('title', $title)

@section('breadcrumbs')
    @breadcrumbs([
    'breadcrumbs' => [
    'admin.dashboard.index' => 'Главная',
    'admin.users.index'     => 'Пользователи',
    'admin.users.show'      => $title,
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
                <a href="{{ route('admin.services.request.accept',  $serviceRequest->id) }}" class="btn btn-info btn-xs">
                    <span class="hidden-xs">Принять заявку</span>
                </a>
                <a href="" class="btn btn-default btn-xs">
                    <span class="hidden-xs">Удалить заявку</span>
                </a>
                <a href="{{ route('admin.services.request.edit',  $serviceRequest->id) }}" class="btn btn-warning btn-xs">
                    <span class="hidden-xs">Редактировать заявку</span>
                </a>
                <a href="" class="btn btn-success btn-xs">
                    <span class="hidden-xs">Заявка выполнена</span>
                </a>
                @endauth
            </div>
            <br>

            <header class="block_header">
                <h2><i class="fa fa-edit"></i> Плановые данные</h2>
            </header>

            <div class="block_body">

                <table class="table table-striped">
                    <tr>
                        <th>Наименование услуги</th>
                        <td>{{ $serviceRequest->title ?? str_limit(strip_tags(html_decode($serviceRequest->description )), 35)  }}</td>
                    </tr>
                    <tr>
                        <th>Количество</th>
                        <td>{{ $serviceRequest->quantity }}</td>
                    </tr>
                    <tr>
                        <th>Цена</th>
                        <td>{{ $serviceRequest->price ?? 0 }}</td>
                    </tr>
                    <tr>
                        <th>Итого</th>
                        <td><b>{{ $serviceRequest->price ?? 0 }}</b></td>
                    </tr>
                    <tr>
                        <th>Дата и время исполнения</th>
                        <td>{{ $serviceRequest->execution_date ?? 'не указано'}}</td>
                    </tr>
                    <tr>
                        <th>Комментарий</th>
                        <td>{{ strip_tags(html_decode($serviceRequest->description )) }}</td>
                    </tr>
                    {{--      <tr>
                              <th>Адрес (а)</th>
                              <td>
                                  <ul>
                                      @foreach($addresses as $address)
                                          <li>
                                              <a href="{{ route('admin.areas.show', $address->area_id) }}">{{ $address->title }}</a>
                                              @if ($user->main == 'on')
                                              <span class="badge badge-warning ml-20" title="Ответственное лицо"><i class="fa fa-address-card"></i></span>
                                              @endif
                                          </li>
                                      @endforeach
                                  </ul>
                              </td>
                          </tr>--}}
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
                        <td>{{ $serviceRequest->title ?? str_limit(strip_tags(html_decode($serviceRequest->description )), 35)  }}</td>
                    </tr>
                    <tr>
                        <th>Количество</th>
                        <td>{{ $serviceRequest->quantity }}</td>
                    </tr>
                    <tr>
                        <th>Цена</th>
                        <td>{{ $serviceRequest->price ?? 0 }}</td>
                    </tr>
                    <tr>
                        <th>Итого</th>
                        <td><b>{{ $serviceRequest->price ?? 0 }}</b></td>
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
