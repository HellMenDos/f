@extends('layouts.admin.app')

@section('heading', $heading)
@section('title', $title)

@section('breadcrumbs')
    @breadcrumbs([
        'breadcrumbs' => [
            'admin.dashboard.index' => 'Главная',
            'admin.polls.index' => $heading,
            'admin.polls.create' => $title,
        ]
    ])
    @endbreadcrumbs
@endsection

@section('after-scripts')
    <script src="{{ url('vendor/tinymce/js/tinymce/tinymce.min.js') }}"></script>
    <script src="{{ url('vendor/moment.js') }}"></script>
    <script src="{{ url('vendor/bootstrap-datetimepicker/locale/ru.js') }}"></script>
    <script src="{{ url('vendor/bootstrap-datetimepicker/bootstrap-datetimepicker.min.js') }}"></script>
    <link href="{{ url('vendor/bootstrap-datetimepicker/bootstrap-datetimepicker.min.css') }}" rel="stylesheet">
    <script>
        $(document).ready(function() {
            $('.datetimepicker').datetimepicker({pickDate: true, pickTime: false});

            let option_value_row = @isset($option_value) {{ count($option_value) }} @else 0 @endisset;

            $('body').on('click', '.add-option-value', function() {

                let q_id= $(this).data('id');

                html  = '<tr id="option-value-row-'+ option_value_row +'">';
                html += '<td><input type="text" name="option_value['+ q_id +']['+ option_value_row +'][title]" value="" required="" class="form-control"></td>';
                html += '<td><input type="text" name="option_value['+ q_id +']['+ option_value_row +'][ordering]" value="0" size="1" class="form-control"></td>';
                html += '<td><a href="javascript:;" onclick="$(\'#option-value-row-'+ option_value_row +'\').remove();" class="btn btn-danger btn-sm"><i class="fa fa-trash-o"></i></a></td>';
                html += '</tr>';

                $(this).closest("#option-value").append(html);

                option_value_row++;
            });
        });
    </script>
    <script>
        $(function() {
            let question_value = @isset($question) {{ count($question) }} @else 1 @endisset;
            $('#add-question').on('click', function( e ) {
                e.preventDefault();
                $('<div/>').addClass( 'new-text-div' )
                    .html( $('<div class="input-group">' +
                        '           <input type="text" name="question['+ question_value +']" id="question'+ question_value +'" value="" class="form-control" required=""/>\n' +
                        '           <div class="input-group-btn">\n' +
                        '               <button class="del_q remove btn btn-danger"><i class="fa fa-trash-o"></i></button>\n' +
                        '           </div>'+
                        '     </div>'+
                        '                           <table class="table" id="option-value">\n' +
                        '                                <thead>\n' +
                        '                                <tr>\n' +
                        '                                    <th>Ответ: <span class="required">*</span></th>\n' +
                        '                                    <th>Порядок:</th>\n' +
                        '                                    <th class="th-action_delete"></th>\n' +
                        '                                </tr>\n' +
                        '                                </thead>\n' +
                        '                                <tbody>\n' +
                        '\n' +
                        '\n' +
                        '                                @if (isset($option_value) && $option_value)\n' +
                        '                                    @php $key = 0 @endphp\n' +
                        '                                    @foreach ($option_value as $value)\n' +
                        '                                        <tr id="option-value-row-{{ $key }}">\n' +
                        '                                            <td>\n' +
                        '                                                <input type="text" name="option_value[{{ $key }}][title]" value="{{ $value->title }}" class="form-control" required="">\n' +
                        '                                            </td>\n' +
                        '                                            <td>\n' +
                        '                                                <input type="text" name="option_value[{{ $key }}][ordering]" value="{{ $value->ordering }}" size="1" class="form-control">\n' +
                        '                                            </td>\n' +
                        '                                            <td>\n' +
                        '                                                <a href="javascript:;" onclick="$(\'#option-value-row-{{ $key }}\').remove();" class="btn btn-danger btn-sm"><i class="fa fa-trash-o"></i></a>\n' +
                        '                                            </td>\n' +
                        '                                        </tr>\n' +
                        '                                        @php $key++ @endphp\n' +
                        '                                    @endforeach\n' +
                        '                                @endif\n' +
                        '                                </tbody>\n' +
                        '                                <tfoot>\n' +
                        '\n' +
                        '                                <tr>\n' +
                        '                                    <td colspan="2" width="100%"></td>\n' +
                        '                                    <td>\n' +
                        '                                        <a href="javascript:;" title="Добавить вариант ответа" id="add-option-value" data-id="'+question_value+'" class="add-option-value btn btn-primary btn-sm"><span class="hidden-xs"><i class="fa fa-plus"></i></span><span class="visible-xs-inline"><i class="fa fa-plus"></i></span></a>\n' +
                        '                                    </td>\n' +
                        '                                </tr>\n' +
                        '                                </tfoot>\n' +
                        '                            </table>' +
                        '<br>' +

                        '<br>') ).insertBefore( this );
                question_value++;
            });
            $(document).on('click', 'button.remove', function( e ) {
                e.preventDefault();
                $(this).closest( 'div.new-text-div' ).remove();
            });

        });
    </script>

@endsection

@section('content')

    <form action="{{ $action }}" class="validate" method="post" accept-charset="utf-8">
        @if ($poll->id)
            {{ method_field('PATCH')}}
        @endif
        {{ csrf_field() }}
        <div class="col-md-8">
            <section class="block">
                <header class="block_header">
                    <h2><i class="fa fa-edit"></i> Основные данные <small>( * обязательно для заполнения)</small></h2>
                </header>
                <div class="block_body">

                    <fieldset>
                        <div class="form-group">
                            <label for="title">Заголовок <span class="required">*</span></label>
                            <input type="text" name="title" value="{{ old('title', $poll->title) }}" id="title" class="form-control" required="">
                        </div>

                        <div class="form-group">
                            <label for="description">Описание<span class="required">*</span></label>
                            <textarea name="description" rows="4" id="description" class="form-control" required="">{{ old('description', $poll->description) }}</textarea>
                        </div>

                        <div class="checkbox">
                            <label>
                                <input type="hidden" name="multiselect" value="0">
                                @isset($poll->multiselect)
                                    <input type="checkbox" name="multiselect" value="1"@php checked($poll->multiselect == '1') @endphp>
                                @else
                                    <input type="checkbox" name="multiselect" value="0">
                                @endisset
                                Можно голосовать за несколько вариантов
                            </label>
                        </div>

                    </fieldset>
                    <fieldset id="question-value">
                        <legend>Вопросы</legend>
                        @if(isset($question))

                            @foreach($question as $kq => $vq)

                                <div class="new-text-div">
                                    @if ($kq > 0)
                                        <div class="input-group">
                                            <input type="text" name="question[{{$kq}}]" id="question" value="{{$vq->title}}" class="form-control" required="">
                                            <div class="input-group-btn">
                                               <button class="del_q remove btn btn-danger"><i class="fa fa-trash-o"></i></button>
                                           </div>
                                        </div>
                                    @else
                                        <input type="text" name="question[{{$kq}}]" id="question" value="{{$vq->title}}" class="form-control" required="">
                                    @endif

                                    <table class="table" id="option-value">
                                        <thead>
                                            <tr>
                                                <th>Ответ: <span class="required">*</span></th>
                                                <th>Порядок:</th>
                                                <th class="th-action_delete"></th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                        @foreach ($answer as $key => $value)
                                            @foreach($value as $k => $v)
                                                @if ($key == $vq->id )
                                                    <tr id="option-value-row-{{ $v->id }}">
                                                        <td>
                                                            <input type="text" name="option_value[{{ $kq }}][{{$k}}][title]" value="{{ $v->title }}" class="form-control" required="">
                                                        </td>
                                                        <td>
                                                            <input type="text" name="option_value[{{ $kq }}][{{$k}}][ordering]" value="{{ $v->ordering }}" size="1" class="form-control">
                                                        </td>
                                                        <td>
                                                            <a href="javascript:;" onclick="$('#option-value-row-{{ $v->id }}').remove();" class="btn btn-danger btn-sm"><i class="fa fa-trash-o"></i></a>
                                                        </td>
                                                    </tr>

                                                @endif
                                            @endforeach
                                        @endforeach

                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="2" width="100%"></td>
                                                <td>
                                                    <a href="javascript:;" title="Добавить вариант ответа" id="add-option-value" data-id="0" class="add-option-value btn btn-primary btn-sm"><span class="hidden-xs"><i class="fa fa-plus"></i></span><span class="visible-xs-inline"><i class="fa fa-plus"></i></span></a>
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                                <br>
                                <br>
                            @endforeach
                        @else
                            <input type="text" name="question[0]" id="question" value="" class="form-control" required="">
                            <table class="table" id="option-value">
                                <thead>
                                <tr>
                                    <th>Ответ: <span class="required">*</span></th>
                                    <th>Порядок:</th>
                                    <th class="th-action_delete"></th>
                                </tr>
                                </thead>
                                <tbody>

                                @if (isset($option_value) && $option_value)
                                    @php $key = 0 @endphp
                                    @foreach ($option_value as $value)
                                        <tr id="option-value-row-{{ $key }}">
                                            <td>
                                                <input type="text" name="option_value[{{ $key }}][title]" value="{{ $value->title }}" class="form-control" required="">
                                            </td>
                                            <td>
                                                <input type="text" name="option_value[{{ $key }}][ordering]" value="{{ $value->ordering }}" size="1" class="form-control">
                                            </td>
                                            <td>
                                                <a href="javascript:;" onclick="$('#option-value-row-{{ $key }}').remove();" class="btn btn-danger btn-sm"><i class="fa fa-trash-o"></i></a>
                                            </td>
                                        </tr>
                                        @php $key++ @endphp
                                    @endforeach
                                @endif
                                </tbody>
                                <tfoot>

                                <tr>
                                    <td colspan="2" width="100%"></td>
                                    <td>
                                        <a href="javascript:;" title="Добавить вариант ответа" id="add-option-value" data-id="0" class="add-option-value btn btn-primary btn-sm"><span class="hidden-xs"><i class="fa fa-plus"></i></span><span class="visible-xs-inline"><i class="fa fa-plus"></i></span></a>
                                    </td>
                                </tr>
                                </tfoot>
                            </table>
                            <br>
                            <br>
                            @if (session('error'))
                                <div class="alert alert-danger text-center msg" id="error">
                                    <strong>{{ session('error') }}</strong>
                                </div>
                            @endif
                        @endif
                        <div class="text-right">
                            <a href="javascript:;" class="btn btn-primary" id="add-question">Добавить вопрос</a>
                        </div>

                    </fieldset>
                    <div class="form-actions">
                        <input type="submit" name="submit" value="Сохранить" class="btn btn-success">
                        <a class="btn btn-default" href="{{ route('admin.polls.index') }}">Отмена</a>
                    </div>

                </div>
            </section>
        </div>
        <div class="col-md-4">

            <section class="block">
                <header class="block_header">
                    <h2><i class="fa fa-cog"></i> Дополнительно</h2>
                </header>
                <div class="block_body">

                    <fieldset>
                        <div class="checkbox">
                            <label>
                                <input type="hidden" name="published" value="0">
                                @isset($poll->published)
                                    <input type="checkbox" name="published" value="1"@php checked($poll->published == '1') @endphp>
                                @else
                                    <input type="checkbox" name="published" value="1" checked="">
                                @endisset
                                Опубликовано
                            </label>
                        </div>

                        <div class="form-group">
                            <label>Дата создания</label>
                            <p class="form-control-static">{{ $poll->created_at }}</p>
                        </div>
                        @isset($poll->updated_at)
                            <div class="form-group">
                                <label>Дата изменения</label>
                                <p class="form-control-static">{{ $poll->updated_at }}</p>
                            </div>
                        @endisset

                       <div class="form-group">
                            <label>Дата начала</label>
                            <div class="datetimepicker input-group date">
                                <input data-date-format="YYYY-MM-DD" value="{{ old('started_at', $poll->started_at ) }}" type="text" class="form-control" name="started_at">
                                <span class="input-group-addon" style="cursor: pointer;"><span class="fa-calendar fa"></span></span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Дата окончания</label>
                            <div class="datetimepicker input-group date">
                                <input data-date-format="YYYY-MM-DD" value="{{ old('ended_at', $poll->ended_at) }}" type="text" class="form-control" name="ended_at">
                                <span class="input-group-addon" style="cursor: pointer;"><span class="fa-calendar fa"></span></span>
                            </div>
                        </div>

                    </fieldset>

                </div>
            </section>
        </div>
    </form>
@endsection
