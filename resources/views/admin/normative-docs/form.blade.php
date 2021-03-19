@extends('layouts.admin.app')

@section('heading', $heading)
@section('title', $title)

@section('breadcrumbs')
    @breadcrumbs([
        'breadcrumbs' => [
            'admin.dashboard.index' => 'Главная',
            'admin.normative-docs.index' => $heading,
            'admin.normative-docs.create' => $title,
        ]
    ])
    @endbreadcrumbs
@endsection

@section('after-scripts')
    <script src="{{ url('vendor/tinymce/js/tinymce/tinymce.min.js') }}"></script>
    <script src="{{ url('vendor/moment.js') }}"></script>
    <script src="{{ url('vendor/select2/dist/js/select2.min.js') }}"></script>
    <link href="{{ url('vendor/select2/dist/css/select2.min.css') }}" rel="stylesheet">
    <link href="{{ url('vendor/select2-bootstrap/dist/select2-bootstrap.min.css') }}" rel="stylesheet">
    <script src="{{ url('vendor/bootstrap-datetimepicker/locale/ru.js') }}"></script>
    <script src="{{ url('vendor/bootstrap-datetimepicker/bootstrap-datetimepicker.min.js') }}"></script>
    <link href="{{ url('vendor/bootstrap-datetimepicker/bootstrap-datetimepicker.min.css') }}" rel="stylesheet">
    <script src="{{ asset('vendor/bootstrap-imageupload/js/bootstrap-imageupload.js') }}"></script>
    <link href="{{ asset('vendor/bootstrap-imageupload/css/bootstrap-imageupload.min.css') }}" rel="stylesheet">

    @include('admin/partials/tinymce')

@endsection

@section('content')
    <form action="{{ $action }}" class="validate" method="post" accept-charset="utf-8" enctype="multipart/form-data">
    @if ($normative_docs->id)
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
                            <input type="text" name="title" value="{{ old('title', $normative_docs->title) }}" id="title" class="form-control" required="">
                        </div>

                        <div class="form-group">
                            <label>Документ</label>
                            <div class="imageupload">
                                <div class="file-tab">

                                    @if ( ! empty($normative_docs->file_path))
                                        <input size="40" type="text" name="filePath" value="{{$normative_docs->file_path}}" disabled>
                                        <a href="{{route('admin.normative-docs.download_file', $normative_docs->id)}}" class="btn btn-success"><i class="fa fa-download" aria-hidden="true"></i></a>
                                    <br>
                                    <br>
                                        <label class="btn btn-primary btn-file btn-xs">
                                            <span>Сменить</span>
                                            <input type="file" name="imageForm">
                                        </label>
                                        <button type="button"
                                                class="btn btn-danger btn-xs"
                                                style="display: inline-block;">Удалить</button>
                                        <input type="hidden" name="imageLoaded" value="true">
                                    @else
                                        <label class="btn btn-primary btn-file btn-xs">
                                            <span>Выбрать</span>
                                            <input type="file" name="imageForm" required/>
                                        </label>
                                        <input type="hidden" name="imageLoaded" value="false">
                                        <button type="button" class="btn btn-danger btn-xs">Удалить</button>
                                    @endif

                                </div>
                            </div>
                        </div>
                    </fieldset>

                    <div class="form-actions">
                        <input type="submit" name="submit" value="Сохранить" class="btn btn-success">
                        <a class="btn btn-default" href="{{ route('admin.normative-docs.index') }}">Отмена</a>
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
                                @isset($normative_docs->published)
                                    <input type="checkbox" name="published" value="1"@php checked($normative_docs->published == '1') @endphp>
                                @else
                                    <input type="checkbox" name="published" value="1" checked="">
                                @endisset
                                Опубликовано
                            </label>
                        </div>

                        <div class="form-group">
                            <label>Дата создания</label>
                            <p class="form-control-static">{{ $normative_docs->created_at }}</p>
                        </div>
                        @isset($normative_docs->updated_at)
                            <div class="form-group">
                                <label>Дата изменения</label>
                                <p class="form-control-static">{{ $normative_docs->updated_at }}</p>
                            </div>
                        @endisset
                    </fieldset>

                </div>
            </section>
        </div>
    </form>
@endsection
