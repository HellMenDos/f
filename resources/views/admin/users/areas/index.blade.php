@extends('layouts.admin.app')

@section('heading', $heading)
@section('title', $title)

@section('breadcrumbs')
    @breadcrumbs([
        'breadcrumbs' => [
            'admin.dashboard.index' => 'Главная',
            'admin.areas.index' => $heading,
        ]
    ])
    @endbreadcrumbs
@endsection

@section('after-scripts')
    <script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
    <link href="https://cdn.datatables.net/1.10.22/css/jquery.dataTables.min.css" rel="stylesheet">
    <style>
        table.dataTable thead th,
        table.dataTable thead td {
            border-bottom: 2px solid #ddd;
        }
        table.dataTable.no-footer {
            border-bottom: 1px solid #ddd;
        }
    </style>
    <script>
        $(document).ready(function () {
            $('#areas').DataTable(
                {
                    "language": {
                        "sProcessing": "Подождите...",
                        "sLengthMenu": "Показать _MENU_ записей",
                        "sZeroRecords": "Записи отсутствуют.",
                        "sInfo": "Записи с _START_ до _END_ из _TOTAL_ записей",
                        "sInfoEmpty": "Записи с 0 до 0 из 0 записей",
                        "sInfoFiltered": "(отфильтровано из _MAX_ записей)",
                        "sInfoPostFix": "",
                        "sSearch": "Поиск:",
                        "sUrl": "",
                        "oPaginate": {
                            "sFirst": "Первая",
                            "sPrevious": "Предыдущая",
                            "sNext": "Следующая",
                            "sLast": "Последняя"
                        },
                        "oAria": {
                            "sSortAscending": ": активировать для сортировки столбца по возрастанию",
                            "sSortDescending": ": активировать для сортировки столбцов по убыванию"
                        }

                    },
                    "bPaginate": false,
                    "columns": [
                        null,
                        null,
                        null,
                        null,
                        null,
                        { "sortable": false },
                    ],
                },

            );

        });
        $(document).on('submit', '#ajax-notify-form', function (e) {
            e.preventDefault();

            const form = $(this);

            $.ajax({
                type: 'post',
                url: $adminUrl + '/' + window.$routeNotify,
                data: form.serialize(),
                dataType: 'json',
                success: function (response, textStatus, xhr) {
                    if (response.type === 'success') {
                        form[0].reset();
                        $('.modal').modal('hide');
                    }
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    serverError(xhr, ajaxOptions, thrownError);
                }
            });
        });
    </script>
@endsection

@section('content')
    <div class="col-md-12">
        <section class="block">
            <header class="block_header">
                <h2>
                    {{ $title }}
                    <span class="label label-success animated bounceIn"
                          title="Количество участков">{{ $countAreas }}</span>
                </h2>
                <div class="action">
                    <a href="javascript:;" data-remote="{{ route('admin.areas.notify') }}" data-recipient="all"
                       class="btn btn-primary btn-xs js-modal">Отправить уведомления всем</a>
                    <a href="{{ route('admin.areas.create') }}" class="btn btn-default btn-xs"><span class="hidden-xs">Добавить</span>
                        <i class="fa fa-plus"></i></a>
                </div>
            </header>
            <div class="block_body">

                <table id="areas" class="table table-striped table-condensed">
                    <thead class="hidden-xs">
                    <tr>
                        <th>#ID</th>
                        <th>Адрес</th>
                        <th>ФИО ответственного лица</th>
                        <th>Статус</th>
                        <th>Баланс</th>
                        <th class="col-action">Действие</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($areas as $area)
                        <tr>
                            <td class="hidden-xs"><strong>{{ $area->id }}</strong></td>
                            <td>
                                <a href="{{ route('admin.areas.show', $area->id) }}" title="Просмотр">
                                    {{ $area->address }}
                                </a>
                            </td>
                            <td class="nowrap">
                                <a href="{{ route('admin.users.show', $area->user_id) }}">
                                    {{ $area->username }}
                                </a>
                                <small>
                                    <a href="javascript:;" class="js-modal"
                                       data-remote="{{ route('admin.feedback.ajax_send_email') }}"
                                       data-email="{{ $area->email }}">
                                        {{ $area->email }}
                                    </a>
                                </small>
                            </td>
                            <td data-value="по">
                                @if ($area->status == 'no-data-water')
                                    <a href="{{ route('admin.areas.show', $area->id) }}?tab=tab-meters-data"
                                       class="js-modal badge badge-default"
                                       data-remote="{{ route('admin.areas.ajax_set_meters', $area->id) }}">Показания
                                        водоснабжения не указаны!</a>
                                @elseif ($area->status == 'no-data-electricity')
                                    <a href="{{ route('admin.areas.show', $area->id) }}?tab=tab-meters-data"
                                       class="js-modal badge badge-warning"
                                       data-remote="{{ route('admin.areas.ajax_set_meters', $area->id) }}">Показания
                                        электроэнергии не указаны!</a>
                                @elseif ($area->status == 'no-paid')
                                    <a href="{{ route('admin.areas.show', $area->id) }}?tab=tab-meters-data"
                                       class="badge badge-success-light">Ожидается оплата!</a>
                                @elseif ($area->status == 'no-data')
                                    <a href="{{ route('admin.areas.show', $area->id) }}?tab=tab-meters-data"
                                       class="badge badge-danger">Показания не указаны!</a>
                                @elseif ($area->status == 'is-paid')
                                    <a href="{{ route('admin.areas.show', $area->id) }}?tab=tab-meters-data"
                                       class="badge badge-success">Оплачено</a>
                                @endif
                            </td>
                            <td>
                                {{ $area->balance ?? '0.00' }}
                            </td>
                            <td class="col-action">
                                <div class="btn-group">
                                    <a class="btn btn-success btn-sm" href="{{ route('admin.areas.show', $area->id) }}"><i
                                            class="fa fa-search"></i></a>
                                    <div class="dropdown">
                                        <a href="javascript:;" class="btn btn-default btn-sm dropdown-toggle"
                                           data-toggle="dropdown" aria-expanded="true"><i class="fa fa-ellipsis-h"></i></a>
                                        <ul class="dropdown-menu dropdown-menu-right">
                                            <li><a href="{{ route('admin.areas.all', $area->id) }}"><i
                                                        class="fa fa-users"></i> Все проживающие</a></li>
                                            <li><a href="{{ route('admin.areas.edit', $area->id) }}"><i
                                                        class="fa fa-pencil-square-o"></i> Редактировать</a></li>
                                            <li><a href="javascript:;" data-remote="{{ route('admin.areas.notify') }}"
                                                   data-recipient="one" data-user-id="{{ $area->user_id }}"
                                                   class="js-modal"><i class="fa fa-envelope-o"></i> Отправить
                                                    уведомление</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">Участки не найдены</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>

            </div>

            @if($areas->hasPages())
                <div class="block_footer">
                    <div class="pagination-block">
                        {{ $areas->links() }}
                    </div>
                </div>
            @endif

        </section>
    </div>
@endsection
