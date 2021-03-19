@extends('layouts.admin.app')

@section('heading', $heading)
@section('title', $title)

@section('breadcrumbs')
    @breadcrumbs([
        'breadcrumbs' => [
            'admin.dashboard.index' => 'Главная',
            'admin.services.executors.index'  => $heading,
            'admin.services.executors.form' => $title,
        ]
    ])
    @endbreadcrumbs
@endsection

@section('after-scripts')
    <script src="{{ url('vendor/tinymce/js/tinymce/tinymce.min.js') }}"></script>

    @include('admin/partials/tinymce')
@endsection

@section('content')
    <form action="{{ $action }}" class="validate" method="post" accept-charset="utf-8" enctype="multipart/form-data">
        @if ($serviceExecutor->id)
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
                            <label for="title">Имя исполнителя услуг <span class="required">*</span></label>
                            <input type="text" name="title" value="{{ old('title', $serviceExecutor->title) }}"
                                   id="title"
                                   class="form-control"
                                   required="">
                        </div>

                        <div class="form-group">
                            <label for="description">Описание <span class="required">*</span></label>
                            <textarea name="description" rows="16" id="description" class="form-control wysiwyg"
                                      required="">{{ old('fulltext', html_decode($serviceExecutor->description)) }}</textarea>
                        </div>

                    </fieldset>

                    <div class="form-actions">
                        <input type="submit" name="submit" value="Сохранить" class="btn btn-success">
                        <a class="btn btn-default" href="{{ route('admin.services.executors.index') }}">Отмена</a>
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
                                @isset($serviceExecutor->published)
                                    <input type="checkbox" name="published"
                                           value="1"@php checked($serviceExecutor->published == '1') @endphp>
                                @else
                                    <input type="checkbox" name="published" value="1" checked="">
                                @endisset
                                Опубликовано
                            </label>
                        </div>

                        <div class="form-group">
                            <label for="price">Цена <span class="required">*</span></label>
                            <input type="text" name="price"
                                   value="{{ old('price', $serviceExecutor->price) }}" id="price" class="form-control"
                                   required="">
                        </div>

                        <div class="form-group">
                            <label for="group_id">Группа <span class="required">*</span></label>
                            <select id="group_id" name="group_id" tabindex="-1" class="form-control" required="">
                                <option value="">-- Выбрать группу --</option>
                                @foreach($servicesGroups as $group)

                                    @isset($serviceExecutor->service_group_id)
                                        <option
                                                value="{{ $group->id }}"@php selected($group->id == $serviceExecutor->service_group_id) @endphp>{{ $group->title }}</option>
                                    @elseif($selectGroup)
                                        <option
                                                value="{{ $group->id }}"@php selected($group->id == $selectGroup) @endphp>{{ $group->title }}</option>
                                    @else
                                        <option value="{{ $group->id }}">{{ $group->title }}</option>
                                    @endisset

                                @endforeach
                            </select>
                        </div>

                    </fieldset>

                </div>
            </section>
        </div>
    </form>
@endsection
