@extends('layouts.admin.app')

@section('heading', $heading)
@section('title', $title)

@section('breadcrumbs')
    @breadcrumbs([
        'breadcrumbs' => [
            'admin.dashboard.index' => 'Главная',
            'admin.services.request.index'  => $heading,
            'admin.services.request.form' => $title,
        ]
    ])
    @endbreadcrumbs
@endsection

@section('after-scripts')
    <script src="{{ url('vendor/select2/dist/js/select2.min.js') }}"></script>
    <link href="{{ url('vendor/select2/dist/css/select2.min.css') }}" rel="stylesheet">
    <link href="{{ url('vendor/select2-bootstrap/dist/select2-bootstrap.min.css') }}" rel="stylesheet">
    <script src="{{ url('vendor/tinymce/js/tinymce/tinymce.min.js') }}"></script>
    <script src="{{ url('vendor/moment.js') }}"></script>
    <script src="{{ url('vendor/bootstrap-datetimepicker/locale/ru.js') }}"></script>
    <script src="{{ url('vendor/bootstrap-datetimepicker/bootstrap-datetimepicker.min.js') }}"></script>
    <link href="{{ url('vendor/bootstrap-datetimepicker/bootstrap-datetimepicker.min.css') }}" rel="stylesheet">
    <script>
        $(document).ready(function () {
            $('.datetimepicker').datetimepicker({pickDate: true, pickTime: true});
            $('#services').select2({tags: [], theme: 'bootstrap'});
            $('#address').select2({tags: [], theme: 'bootstrap'});
            $('#quantity').on('change', function() {
                let quantity = $(this).val();
                let price = $('#price').data('price');
                $('#totalPrice').text(price*quantity);
            });
        });
    </script>

    @include('admin/partials/tinymce')

@endsection

@section('content')
    <form action="{{ $action }}" class="validate" method="post" accept-charset="utf-8" enctype="multipart/form-data">
        @if ($serviceRequest->id)
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
                            <label for="title">Услуга <span class="required">*</span></label>
                            <select id="services" name="service_id" class="form-control" placeholder="Выберите адрес">
                                <option value="0">-- Выберите услугу --</option>
                                @foreach ($services as $service)
                                    <option value="{{ $service->id }}"
                                        @php selected($service->id == $serviceRequest->service_id) @endphp
                                    >
                                        {{ $service->title }} Группа: {{ $service->group->title }} ({{ $service->price }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="quantity">Количество единиц услуги
                                <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top"
                                   title="Окончательное количество укажет исполнитель на этапе согласования заявки и выполнения работы"> </i>
                                <span class="required">*</span></label>
                            <input type="number" min="1" step="1" name="quantity"
                                   value="{{ old('quantity', $serviceRequest->quantity) ?? 1 }}" id="quantity"
                                   class="form-control" required="">
                        </div>

                        <div class="form-group">
                            <label for="address">Адрес участка</label>

                            @foreach ($addresses as $address)
                                @if ($address['main'] == 'on' && $address['area_id'] == $serviceRequest->area_id)
                                    <p class="form-control">{{ $address['title'] }}
                                        (№ договора {{ $address['contract_number'] }},
                                        собственник {{ $address['username'] }})</p>
                                @endif
                            @endforeach

                        </div>

                    </fieldset>

                    <div class="form-actions">
                        <input type="submit" name="submit" value="Сохранить" class="btn btn-success">
                        <a class="btn btn-default" href="{{ route('admin.services.request.show', $serviceRequest->id) }}">Отмена</a>
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

                        <div class="form-group">
                            <label>Дата и время выполнения</label>
                            <div class="datetimepicker input-group date">
                                <input data-date-format="YYYY-MM-DD HH:mm:ss"
                                       value="{{ old('execution_date', $serviceRequest->execution_date) }}" type="text"
                                       class="form-control" name="execution_date" required="">
                                <span class="input-group-addon" style="cursor: pointer;"><span
                                        class="fa-calendar fa"></span></span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="execution_hours">Кол-во часов для выполнения</label>
                            <input type="number"
                                   name="execution_hours"
                                   value="{{ old('execution_hours', $serviceRequest->execution_hours) }}"
                                   id="execution_hours"
                                   class="form-control" required=""
                            >
                        </div>

                        <div class="form-group">
                            <label>Цена, руб.</label>
                            <p id="price" data-price="{{ $serviceRequest->price }}" class="form-control">{{ $serviceRequest->price }}</p>
                        </div>

                        <div class="form-group">
                            <label>Итого, руб.</label>
                            <p id="totalPrice" class="form-control">{{ $serviceRequest->price * $serviceRequest->quantity }}</p>
                        </div>

                    </fieldset>

                </div>
            </section>
        </div>
    </form>
@endsection
