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

    <div class="col-md-12">
        <section class="block">
            <header class="block_header">
                <h2>
                    {{ $title }}
                    <span class="label label-success animated bounceIn" title="Количество заявок">{{ $countServiceRequests }}</span>
                </h2>
                <div class="action">
                    @haspermission('create_services')
                        <a href="{{ route('admin.services.request.create') }}" class="btn btn-default btn-xs">
                            <span class="hidden-xs">Добавить заявку</span> <i class="fa fa-plus"></i>
                        </a>
                    @endauth
                </div>
            </header>
            <div class="block_body">

                <table class="table table-striped table-condensed">
                    <thead>
                        <tr>
                            <th class="hidden-mobi">№</th>
                            <th class="hidden-mobi">Дата</th>
                            <th class="hidden-mobi">Домовладение</th>
                            <th class="requests__descrip">Описание</th>
                            <th>Статус</th>
                            <th>Действие</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse ($serviceRequests as $service)
                        <tr>
                            <td class="hidden-mobi">{{ $service->id }}</td>
                            <td class="hidden-mobi"><span class="nowrap">{{ $service->created }}</span></td>
                            <td class="hidden-mobi">
                                <a href="{{ route('admin.services.request.show', $service->id) }}" title="Карточка заявки">
                                    {{ $service->area->address }}
                                </a>
                            </td>
                            <td class="requests__descrip">
                                @if ($service->title)
                                    {{ Str::limit($service->title, 35) }}
                                @else
                                    <strong>Свободная форма:</strong> {{ Str::limit(strip_tags(html_decode($service->text_free)), 35) }}
                                @endif
                            </td>
                            <td title="{{ $service->status_title }}">
                                <p class="badge badge-{{ $service->status_color }}">{{ $service->status_label }}</p>
                                @if ($service->status == 3 && $service->rating)
                                    &nbsp;&nbsp; {!! $service->rating !!}
                                @endif
                            </td>
                            <td class="col-action">
                                <div class="btn-group">
                                    <a href="{{ route('admin.services.request.show', $service->id) }}"
                                       class="btn btn-default btn-xs"><i class="fa fa-search"></i></a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">Заявки не найдены</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>

            </div>

            @if($serviceRequests->hasPages())
                <div class="block_footer">
                    <div class="pagination-block">
                        {{ $serviceRequests->links() }}
                    </div>
                </div>
            @endif

        </section>
    </div>
@endsection
