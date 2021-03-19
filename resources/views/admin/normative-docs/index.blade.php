@extends('layouts.admin.app')

@section('heading', $heading)
@section('title', $title)

@section('breadcrumbs')
    @breadcrumbs([
        'breadcrumbs' => [
            'admin.dashboard.index' => 'Главная',
            'admin.normative-docs.index' => $heading,
        ]
    ])
    @endbreadcrumbs
@endsection

@section('content')
    <div class="col-md-10 col-md-offset-1">
        <section class="block">
            <header class="block_header">
                <h2>
                    {{ $title }}
                    <span class="label label-success animated bounceIn" title="Количество статей">{{ $countND }}</span>
                </h2>
                <div class="action">
                    <a href="{{ route('admin.normative-docs.create') }}" class="btn btn-default btn-xs">
                        <span class="hidden-xs">Добавить</span> <i class="fa fa-plus"></i>
                    </a>
                </div>
            </header>
            <div class="block_body">

                <table class="table table-striped table-condensed">
                    <thead class="hidden-xs">
                    <tr>
                        <th>Заголовок</th>
                        <th class="hidden-sm">Дата изменения</th>
                        <th>Статус</th>
                        <th class="col-action">Действие</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($normative_docs as $nd)
                        @php $published = ($nd->published)
                            ? '<a href="'. route('admin.normative-docs.deactivate', $nd->id) .'"
                                title="Отключить статью"
                                onclick="return confirmMessage(\'Вы подтверждаете отключение? Новость будет недоступна пользователям!\')"><span class="label label-success">Вкл.</span></a>'
                            : '<a href="'. route('admin.normative-docs.activate', $nd->id) .'"
                                title="Активировать статью"
                                onclick="return confirmMessage(\'Вы подтверждаете включение? Новость будет доступна пользователям!\')"><span class="label label-grey">Выкл.</span></a>'
                        @endphp
                        <tr>
                            <td class="hidden-xs">
                                <a href="{{ route('admin.normative-docs.edit', $nd->id) }}" title="Редактировать">{{$nd->title}}</a></td>
                            <td class="hidden-sm hidden-xs">
                                <time title="{{ format_date($nd->updated_at, 4) }}">
                                    {{ \Carbon\Carbon::parse($nd->updated_at)->diffForHumans() }}
                                </time>
                            </td>
                            <td class="hidden-xs text-center">{!! $published !!}</td>
                            <td class="col-action">
                                <div class="btn-group">
{{--                                    <a href="{{ route('admin.normative-docs.statistic', $nd->id) }}" class="btn btn-default btn-sm" title="Смотреть статистику"><i class="fa fa-bar-chart"></i></a>--}}
                                    <a href="{{ route('admin.normative-docs.edit', $nd->id) }}" class="btn btn-sm btn-primary"><i class="fa fa-pencil-square-o"></i></a>
                                    <a href="{{ route('admin.normative-docs.delete', $nd->id) }}" class="btn btn-sm btn-danger hidden-xs" onclick="return confirmMessage()"><i class="fa fa-trash-o"></i></a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">Нормативные документы не найдены</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>

            </div>

            @if($normative_docs->hasPages())
                <div class="block_footer">
                    <div class="pagination-block">
                        {{ $normative_docs->links() }}
                    </div>
                </div>
            @endif

        </section>
    </div>
@endsection
