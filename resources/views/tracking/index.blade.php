@extends('layouts.logged')
@section('content')
    @include('inc.navbar')

    <link rel="stylesheet" href="{{ asset('/css/buttons.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/tracking.css') }}">

    <div class="content">
        <div class="container-fluid">
            <div class="col-12">
                <div class="card card-banner" style="margin-top:120px">
                    <div class="card-header card-header-danger">
                        <h4 class="card-title">Registro de Ventas</h4>
                    </div>

                    <div class="card-body">
                        <div class="col-lg-10">
                            <form id="exportTracking" action="{{ route('tracking.export') }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="informes-container">

                                    <div class="date-informes-container">

                                        <label class="label" for="dateFrom" style="margin-top:20px;">Fecha desde </label>
                                        <div class="select-wrapper row interspace">
                                            <div id="monthYearPickerContainer" class="interspace">

                                                <input type="date" id="date_from" name="date_from" max="3000-12-31"
                                                    min="1000-01-01"class="form-date">
                                                <span id="icon-date" class="icon-select material-symbols-outlined">
                                                    calendar_month</span>
                                            </div>
                                        </div>
                                        </input>

                                        <label class="label" for="dateTo" style="">Fecha hasta </label>
                                        <div class="icon-container">
                                            <input type="date" id="date_to" name="date_to" max="3000-12-31"
                                                min="1000-01-01" class="form-date">
                                            <span id="icon-date" class="icon-select material-symbols-outlined">
                                                calendar_month</span>
                                        </div>
                                        </input>
                                    </div>


                                    <div id="picker-btn-container" class="picker-btn-container">
                                        <div id="picker-container" class="picker-container">
                                            <!-- Centro Selector -->
                                            <div class="select-wrapper">
                                                <span id="icon-select"
                                                    class="icon-select material-symbols-outlined">business</span>
                                                <select class="selectpicker" name="centre_id" id="centre_id" data-size="7"
                                                    data-style="btn btn-red-icot btn-round" title="Centro">
                                                    @if ($user->rol_id != 1)
                                                        @foreach ($centres as $centre)
                                                            @if ($centre->id == $user->centre_id)
                                                                <option class="text-uppercase" value="{{ $centre->id }}"
                                                                    selected>
                                                                    {{ $centre->name }}
                                                                </option>
                                                            @endif
                                                        @endforeach
                                                    @else
                                                        @foreach ($centres as $centre)
                                                            <option class="text-uppercase" value="{{ $centre->id }}">
                                                                {{ $centre->name }}
                                                            </option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                                <input type="hidden" name="centre" id="centre" />
                                            </div>

                                            <!-- Empleado Selector -->
                                            <div class="select-wrapper">
                                                <span id="icon-select"
                                                    class="icon-select material-symbols-outlined">engineering</span>
                                                <select class="selectpicker" name="employee_id" id="employee_id"
                                                    data-size="7" data-style="btn btn-red-icot btn-round" title="Empleado">
                                                    <option>SIN SELECCION</option>
                                                    @if ($user->rol_id != 1)
                                                        @foreach ($employees as $employee)
                                                            @if ($employee->centre_id == $user->centre_id)
                                                                <option value="{{ $employee->id }}">{{ $employee->name }}
                                                                </option>
                                                            @endif
                                                        @endforeach
                                                    @else
                                                        @foreach ($employees as $employee)
                                                            <option value="{{ $employee->id }}">{{ $employee->name }}
                                                            </option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                                <input type="hidden" name="employee" id="employee" />
                                            </div>

                                            <!-- Servicio Selector -->
                                            <div class="select-wrapper">
                                                <span id="icon-select"
                                                    class="icon-select material-symbols-outlined">medical_services</span>
                                                <select class="selectpicker" name="service_id" id="service_id"
                                                    data-size="7" data-style="btn btn-red-icot btn-round" title="Servicio">
                                                    <option>SIN SELECCION</option>
                                                    @foreach ($services as $service)
                                                        <option value="{{ $service->id }}">{{ $service->name }}</option>
                                                    @endforeach
                                                </select>
                                                <input type="hidden" name="service" id="service" />
                                            </div>

                                            <!-- Paciente Selector -->
                                            <div class="select-wrapper">
                                                <span id="icon-select"
                                                    class="icon-select material-symbols-outlined">personal_injury</span>
                                                <select class="selectpicker" name="patient_name" id="patient_name"
                                                    data-size="7" data-style="btn btn-red-icot btn-round" title="Paciente">
                                                    <option>SIN SELECCION</option>
                                                    @foreach ($patients as $patient)
                                                        <option value="{{ $patient->patient_name }}">
                                                            {{ $patient->patient_name }}</option>
                                                    @endforeach
                                                </select>
                                                <input type="hidden" name="patient" id="patient" />
                                            </div>

                                            <!-- Estado Selector -->
                                            <div class="select-wrapper">
                                                <span id="icon-select"
                                                    class="icon-select material-symbols-outlined">list_alt_check</span>
                                                <select class="selectpicker" name="state_id" id="state_id"
                                                    data-size="7" data-style="btn btn-red-icot btn-round"
                                                    title="Estado">
                                                    <option>SIN SELECCION</option>
                                                    @foreach ($states as $state)
                                                        <option class="text-uppercase" value="{{ $state->texto }}">
                                                            {{ $state->nombre }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Botones -->
                                        <div id="btn-container-box" class="btn-container-box">
                                            <button id="btnClear" class="btn-refresh">
                                                REFRESCAR
                                                <span id="icon-refresh" class="material-symbols-outlined">refresh</span>
                                            </button>
                                            <button id="btnSubmit" type="submit" class="btn-export">
                                                EXPORTAR
                                                <span id="icon-export"
                                                    class="material-symbols-outlined">file_download</span>
                                            </button>
                                            <button id="btnSubmitLoad" type="submit" class="btn-export"
                                                style="display: none">
                                                <span class="spinner-border spinner-border-sm" role="status"
                                                    aria-hidden="true"></span>
                                            </button>
                                            <button id="btnSell" type="button" class="btn-send"
                                                onclick="navigateToCreateTracking()">
                                                NUEVA <span id="icon-send" class="material-symbols-outlined">
                                                    add_shopping_cart
                                                </span>
                                            </button>

                                        </div>
                                    </div>


                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered tracking-datatable ">
                        <thead class="table-header">
                            <tr>
                                <th>Centro Prescriptor</th>
                                <th>Empleado</th>
                                <th>H.C.</th>
                                <th>Paciente</th>
                                <th>Servicio</th>
                                <th>Estado</th>
                                <th>F. Inicio</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <style>
        .card-banner {
            background-image: url(/assets/img/banners/3.jpg);
            background-repeat: no-repeat;
            background-size: contain;
            background-position-x: right;
            min-height: 320px;
            min-width: fit-content;
        }
    </style>

    <script type="text/javascript">
        function navigateToCreateTracking() {
            window.location.href = "{{ route('tracking.create') }}";
        }

        var table;

        var columnsFilled = [];
        columnsFilled.push({
            data: 'centre',
            name: 'centre'
        });
        columnsFilled.push({
            data: 'employee',
            name: 'employee',
            searchable: true
        });
        columnsFilled.push({
            data: 'hc',
            name: 'hc'
        });
        columnsFilled.push({
            data: 'patient_name',
            name: 'patient_name'
        });
        columnsFilled.push({
            data: 'service',
            name: 'service',
            searchable: true
        });
        columnsFilled.push({
            data: 'state',
            name: 'state',
            searchable: true
        });
        columnsFilled.push({
            name: 'started_date',
            data: 'started_date'
        });

        columnsFilled.push({
            data: 'action',
            name: 'action',
            searchable: true,

        });

        $(function() {
            setDate();
            $(".nav-item").each(function() {
                $(this).removeClass("active");
            });
            $('#pagesTracking').addClass('show');
            $('#trackingStarted').addClass('active');
            $('#centre_id, #employee_id, #service_id, #patient_name, #state_id').on('change', function() {
                getTrackingData();
            });
            var state = "{{ collect(request()->segments())->last() }}";
            state = state.split("_")[1];

            var tableHtml = '';

            tableHtml = '<tr><th>Centro Prescriptor</th></tr>';
            getTrackingData();

            function clearForms() {
                setDate();
                $('select#centre_id').val('');
                $('select#state_id').val('');
                $('select#employee_id').val('');
                $('select#service_id').val('');
                $('select#patient_name').val('');
                $('select#centre_id').selectpicker("refresh");
                $('select#state_id').selectpicker("refresh");
                $('select#employee_id').selectpicker("refresh");
                $('select#service_id').selectpicker("refresh");
                $('select#patient_name').selectpicker("refresh");
                $('input[type="search"]').val('');
                // $('input[type="search"]').selectpicker("refresh");
                //table.ajax.draw();
                table.search('').draw();
                table.ajax.reload();

            }


            $("#btnClear").on('click', function(e) {

                e.preventDefault();
                clearForms();
            });
        });


        //FIXME este método no se está usando al parecer
        function updateDateTracking(state, trackingId, back) {
            $('#alertErrorTrackingDate').hide();
            var trackingDate = $("#tracking_date_" + trackingId).val();
            $.ajax({
                url: 'updateState/' + state + '/' + trackingId + '/' + trackingDate + '/' + back,
                type: 'get',
                success: function(response, textStatus, jqXHR) {
                    // if success, HTML response is expected, so replace current
                    table.columns.adjust().draw();
                    return;
                    if (textStatus === 'success') {
                        //$("div.alert-success").show();
                        //alert(response.mensaje);
                        window.location = response.url;
                    }
                },
                error: function(xhr, status, error) {
                    var response = JSON.parse(xhr.responseText);
                    $('#alertErrorTrackingDate').text(response.mensaje);
                    $('#alertErrorTrackingDate').show().delay(2000).slideUp(300);
                    $('#btnSubmitLoad').hide();
                    $('#btnSubmitFindLoad').hide();
                    $('#btnSubmitFind').show();
                }

            }).fail(function(jqXHR, textStatus, errorThrown) {
                alert('Error' + jqXHR.responseText);

            });
        }

        function setDate() {
            var date = new Date();
            var day = date.getDate();
            var month = date.getMonth() + 1;
            var year = date.getFullYear();
            var startDay = 20;

            day = day < 10 ? '0' + day : day;
            month = month < 10 ? '0' + month : month;

            var dateTo = year + '-' + month + '-' + day;

            var previousMonth = month;
            var previousYear = year;

            if (month === '01' && day < 21) {
                previousMonth = '12';
                previousYear = year - 1;

            } else {
                previousMonth = parseInt(month, 10);
                previousMonth = (day < 21) ? previousMonth - 1 : previousMonth;
                previousMonth = previousMonth < 10 ? '0' + previousMonth : previousMonth.toString();
            }

            var dateFrom = previousYear + '-' + previousMonth + '-' + startDay;

            document.getElementById("date_from").value = dateFrom;
            document.getElementById("date_to").value = dateTo;
        }

        function getTrackingData() {
            if ($.fn.dataTable.isDataTable('.tracking-datatable')) {
                table = $('.tracking-datatable').DataTable();
            } else {
                table = $('.tracking-datatable').DataTable({
                    order: [6, "desc"],
                    processing: true,
                    serverSide: true,
                    language: {
                        "url": "{{ asset('dataTables/Spanish.json') }}"
                    },
                    ajax: {
                        url: '{{ route('tracking.index') }}',
                        type: "POST",
                        data: function(d) {
                            d._token = "{{ csrf_token() }}",
                                d.centre_id = $('#centre_id option:selected').val(),
                                d.employee = $('#employee_id option:selected').text(),
                                d.patient = $('#patient_name option:selected').val(),
                                d.service = $('#service_id option:selected').text(),
                                d.state = $('#state_id option:selected').text(),
                                date1 = $('#date_from').val().replaceAll('-', '/');
                            date2 = $('#date_to').val().replaceAll('-', '/');
                            d.dateFrom = (date1),
                                d.dateTo = (date2),
                                d.search = $('input[type="search"]').val()
                        },
                        dataSrc: function(json) {
                            $('#btnSubmitFind').show();
                            $('#btnSubmit').show();
                            $('#btnSubmitLoad').hide();
                            $('#btnSubmitFindLoad').hide();

                            return json.data;
                        }
                    },
                    // autoWidth:true,
                    columns: columnsFilled,
                    columnDefs: [{
                            targets: 3,
                            className: 'myclass',
                            render: function(data, type, row) {
                                d = data.split('')[0].toUpperCase() + data.slice(1)
                                var d = data.toLowerCase();
                                return d;
                            }

                        },
                        // {
                        //     targets:  8,
                        //     // data: "cancellation_date",
                        //     type: "date",
                        //     render: function(data, type, row) {

                        //         if (data != null) {
                        //             var datetime = moment(data, 'YYYY-M-D');
                        //             var displayString = moment(datetime).format('D-M-YYYY');

                        //             if (type === 'display' || type === 'filter') {
                        //                 return displayString;
                        //             } else {
                        //                 return datetime; // for sorting
                        //             }
                        //         } else {
                        //             return null;
                        //         }

                        //     }
                        // },
                        {
                            width: "10%",
                            targets: 0
                        },
                        {
                            width: "15%",
                            targets: [1, 3, 4]
                        },
                        {
                            width: "5%",
                            targets: 2
                        },

                        {
                            width: "5%",
                            targets: 5
                        },
                        {
                            width: "10%",
                            targets: 7
                        },
                        {
                            targets: -1,
                            width: '30%'
                        },
                        {
                            targets: '_all',
                            className: 'dt-body-center',
                        }

                    ],
                    search: {
                        "regex": true,
                        "smart": true
                    },
                    initComplete: function() {
                        this.api().columns().every(function() {
                            var column = this;
                        });
                    }
                });
            }
            table.columns.adjust().draw();
        }

        // <!--Export-->
        $("#btnSubmit").on('click', function(e) {
            e.preventDefault();
            $('#btnSubmit').hide();
            $('#btnSubmitLoad').show();
            $('#btnSubmitLoad').prop('disabled', true);
            $('#centre').val($("#centre_id option:selected").text());
            $('#employee').val($("#employee_id option:selected").text());
            $('#service').val($("#service_id option:selected").text());
            $('#patient_name').val($("#patient_name option:selected").val());
            $('#trackingState').val($("#state_id option:selected").val());

            params = {};
            params["_token"] = "{{ csrf_token() }}";
            params["centre"] = $('#centre').val();
            params["employee"] = $('#employee').val();
            params["service"] = $('#service').val();
            params["patient_name"] = $('#patient_name').val();
            params["trackingState"] = $("#state_id option:selected").val();
            params["date_from"] = $('#date_from').val();
            params["date_to"] = $('#date_to').val();


            $.ajax({
                url: $("#exportTracking").attr('action'),
                type: 'post',
                data: params,
                // dataType: 'binary',
                xhrFields: {
                    'responseType': 'blob'
                },
                success: function(data, textStatus, jqXHR) {
                    // if success, HTML response is expected, so replace current
                    if (textStatus === 'success') {
                        $('#btnSubmitLoad').hide();
                        $('#btnSubmit').show();

                        var link = document.createElement('a'),
                            filename = 'tracking.xls';
                        link.href = URL.createObjectURL(data);
                        link.download = filename;
                        link.click();
                    }
                }
            }).fail(function(jqXHR, textStatus, errorThrown) {
                alert('Error' + jqXHR.responseText)

            });
        });
    </script>


@endsection
