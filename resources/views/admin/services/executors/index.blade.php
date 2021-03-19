@extends('layouts.admin.app')

@section('heading', $heading)
@section('title', $title)

@section('breadcrumbs')
    @breadcrumbs([
        'breadcrumbs' => [
            'admin.dashboard.index' => 'Главная',
            'admin.services.index' => $heading,
        ]
     ])
    @endbreadcrumbs
@endsection

@section('content')

    @include('admin.services.includes.sidebar_executors')

    <div class="col-md-9">
        <section class="block">
            <header class="block_header">
                <h2>
                    {{ $title }}
                    <span class="label label-success animated bounceIn" title="Количество исполнителей">{{ $countServicesExecutors }}</span>
                </h2>
                <div class="action">
                    @haspermission('create_services')
                        <a href="{{ route('admin.services.executors.create', ['group' => $selectGroup]) }}" class="btn btn-default btn-xs">
                            <span class="hidden-xs">Добавить исполнителя</span> <i class="fa fa-plus"></i>
                        </a>
                    @endauth
                </div>
            </header>
            <div class="block_body">

                <table class="table table-striped table-condensed">
                    <thead>
                        <tr>
                            <th class="hidden-mobi">№</th>
                            <th>Имя</th>
                            <th>Цена</th>
                            <th class="hidden-xs">Статус</th>
                            <th>Действие</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse ($servicesExecutors as $executor)
                        @php $published = ($executor->published)
                            ? '<a href="'. route('admin.services.executors.deactivate', $executor->id) .'"
                                title="Отключить исполнителя"
                                onclick="return confirmMessage(\'Вы подтверждаете отключение? Исполнитель будет недоступен!\')"><span class="label label-success">Вкл.</span></a>'
                            : '<a href="'. route('admin.services.executors.activate', $executor->id) .'"
                                title="Активировать исполнителя"
                                onclick="return confirmMessage(\'Вы подтверждаете включение? Исполнитель будет доступен!\')"><span class="label label-grey">Выкл.</span></a>'
                        @endphp
                        <tr>
                            <td class="hidden-mobi">{{ $executor->id }}</td>
                            <td>
                                <a href="{{ route('admin.services.executors.edit', $executor->id) }}">
                                    {{ \Str::limit(strip_tags($executor->title), 35) }}
                                </a>
                                <small>Группа:
                                    <a href="{{ route('admin.services.executors.index') .'?group='. $executor->service_group_id }}">
                                        {{ $executor->group->title }}
                                    </a>
                                </small>
                            </td>
                            <td>{{ $executor->price }}</td>
                            <td class="hidden-xs text-center">{!! $published !!}</td>
                            <td class="col-action">
                                <div class="btn-group">
                                    @haspermission('edit_services')
                                        <a href="{{ route('admin.services.executors.edit', $executor->id) }}"
                                           class="btn btn-sm btn-primary">
                                            <i class="fa fa-pencil-square-o"></i>
                                        </a>
                                    @endauth
                                    @haspermission('delete_services')
                                    <a href="{{ route('admin.services.executors.delete', $executor->id) }}"
                                       class="btn btn-sm btn-danger hidden-xs"
                                       onclick="return confirmMessage()">
                                        <i class="fa fa-trash-o"></i>
                                    </a>
                                    @endauth
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">Исполнители не найдены</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>

            </div>

            @if ($servicesExecutors->hasPages())
                <div class="block_footer">
                    <div class="pagination-block">
                        {{ $servicesExecutors->links() }}
                    </div>
                </div>
            @endif

        </section>
    </div>
@endsection
