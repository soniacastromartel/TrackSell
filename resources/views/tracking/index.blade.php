@extends('layouts.logged')

@section('content')
@include('inc.navbar')
@include('common.alert')

<div id="alertErrorTrackingDate" class="alert alert-danger" role="alert" style="display: none">
</div>


<div class="content">
    <div class="container-fluid">

        <div class="col-lg-12">

            <!--tarjeta-->
            <div class="card ">
                <div class="card-header card-header-danger">
                    <h4 class="card-title">Informes</h4>
                </div>
                <div class="card-body" style="margin-top: 30px;margin-bottom: 30px;">

                    <form id="exportTracking" action="{{ route('tracking.export') }}" method="POST">

                        @csrf
                        @method('PUT')
                        <div class="row" style="justify-content: space-evenly;">
                            <div class="col-md-3">
                                <div>
                                    <label class="label" for="dateFrom">Fecha desde </label>
                                    <div class=" input-group ">
                                    <input type="date" id="date_from" name="date_from" max="3000-12-31" min="1000-01-01" class="form-control"></input>
                                    </div>


                                    <br>
                                    <label class="label" for="dateTo">Fecha hasta </label>
                                    <div class=" input-group ">
                                    <input type="date" id="date_to" name="date_to" max="3000-12-31" min="1000-01-01" class="form-control"></input>
                                    </div>
                                </div>

                            </div>



                            <div class="col-md-6">
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <div class="dropdown bootstrap-select">
                                            <select class="selectpicker" name="centre_id" id="centre_id" data-size="7" data-style="btn btn-red-icot btn-round" title=" Seleccione Centro" tabindex="-98">
                                            <option>SIN SELECCION </option>
                                                @foreach ($centres as $centre)
                                                <option class= "text-uppercase" value="{{ $centre->id }}" @if (isset($tracking) && $centre->id == $tracking->centre_id) selected="selected" @endif>
                                                    {{ $centre->name }}
                                                </option>
                                                @endforeach
                                            </select>
                                            <input type="hidden" name="centre" id="centre" />
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <div class="dropdown bootstrap-select">
                                            <select class="selectpicker" name="employee_id" id="employee_id" data-size="7" data-style="btn btn-red-icot btn-round" title=" Seleccione Empleado" tabindex="-98">
                                            <option>SIN SELECCION </option>
                                                @foreach ($employees as $employee)
                                                <option value="{{ $employee->id }}">{{ $employee->name }}
                                                </option>
                                                @endforeach
                                            </select>
                                            <input type="hidden" name="employee" id="employee" />
                                        </div>
                                    </div>
                                </div>
                                <br />
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <div class="dropdown bootstrap-select text-uppercase">
                                            <select class="selectpicker" name="service_id" id="service_id" data-size="7" data-style="btn btn-red-icot btn-round" title=" Seleccione Servicio" tabindex="-98">
                                            <option>SIN SELECCION </option>
                                                @foreach ($services as $service)
                                                <option   value="{{ $service->id }}" @if (isset($tracking) && $service->id == $tracking->service_id) selected="selected" @endif>
                                                    {{ $service->name }}
                                                </option>
                                                @endforeach

                                            </select>
                                            <input type="hidden" name="service" id="service" />
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <div class="dropdown bootstrap-select">
                                            <select class="selectpicker" name="patient_name" id="patient_name" data-size="7" data-style="btn btn-red-icot btn-round" title=" Seleccione Paciente" tabindex="-98">
                                            <option>SIN SELECCION </option>
                                                @foreach ($patients as $patient)
                                                <option value="{{ $patient->patient_name }}">
                                                    {{ $patient->patient_name }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <div class="dropdown bootstrap-select">
                                            <select class="selectpicker" name="state_id" id="state_id" data-size="7" data-style="btn btn-red-icot btn-round" title=" Seleccione Estado" tabindex="-98">
                                            <option>SIN SELECCION </option>
                                                @foreach ($states as $state)
                                                <option value="{{ $state->texto }}">{{$state->nombre}}</option>
                                                @endforeach
                                                
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-5 px-5" style="justify-content: space-evenly;">
                            <div class="col">
                                <button id="btnClear" href="#" class="btn btn-fill btn-warning">
                                    <span class="material-icons mr-1">
                                        clear_all
                                    </span> {{ __('Limpiar formulario') }}
                                </button>
                                <button id="btnSubmitFind" type="submit" class="btn btn-fill btn-success"><span class="material-icons">
                                        search</span> {{ __('Buscar') }}</button>
                                <button id="btnSubmitFindLoad" type="submit" class="btn btn-success">
                                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                    {{ __('Obteniendo datos...') }}
                                </button>
                                <button id="btnSubmit" type="submit" class="btn btn-dark-black"><span class="material-icons">
                                        file_download
                                    </span> {{ __('Exportar') }}</button>
                                <button id="btnSubmitLoad" type="submit" class="btn btn-dark-black" style="display: none">
                                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                    {{ __('Exportando datos...') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>


            <!-- tabla-->
            <div class="row col-12 mb-3 right">
                <div class="row" style="margin-bottom: 50px;">
                    <a href="{{ route('tracking.create') }}" id="btnNewTracking" class="btn btn-red-icot btn-lg"><span class="material-icons">
                            add_circle</span> Nuevo Seguimiento</a>
                </div>

            </div>
            <table class="table  table-striped table-bordered tracking-datatable">
                <thead class="table-header">
                    <tr>
                        <th>Centro Prescriptor</th>
                        <th>Empleado</th>
                        <th>H.C.</th>
                        <th>Paciente</th>
                        <th>Servicio</th>
                        <th>Estado</th>
                        <th>F. Inicio</th>
                        <th>F. Cancelación</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>



<script type="text/javascript">
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
        name: 'state_date',
        data: 'state_date'
    });
    columnsFilled.push({
        name: 'cancellation_date',
        data: 'cancellation_date'
    });
    columnsFilled.push({
        data: 'action',
        name: 'action',
        searchable: true,
        width: 300
    });


    $(function() {

    var d = new Date();
    var dayOfMonth = d.getDate();
    var year = d.getFullYear();
    var month = d.getMonth()+1;

    var textMonthYear = month >= 10 ? month : '0' + month;
    fullDate =year +'-'+textMonthYear+ '-' + dayOfMonth;
    document.getElementById("date_to").value = fullDate;
    document.getElementById("date_from").value = fullDate;

        $(".nav-item").each(function() {
            $(this).removeClass("active");
        });
        $('#pagesTracking').addClass('show');
        $('#trackingStarted').addClass('active');

        var state = "{{ collect(request()->segments())->last() }}";
        state = state.split("_")[1];

        var tableHtml = '';

        tableHtml = '<tr><th>Centro Prescriptor</th></tr>';
        getTrackingData();

        // Buscar
        $("#btnSubmitFind").on('click', function(e) {
            e.preventDefault();
            //$("#finalValidationForm").attr('action','{{ route("tracking.index_validation_final") }}');
            $('#btnSubmitFind').hide();
            $('#btnSubmitFindLoad').show();
            $('#btnSubmitFindLoad').prop('disabled', true);
            //$('#centre').val($( "#centre_id option:selected" ).text());
            getTrackingData();
        });

        function clearForms() {
            var d = new Date();
            var textMonthYear = d.getFullYear()+ '-' +'0'+(d.getMonth()+ 1)+'-' +d.getDate() ;
            document.getElementById("date_to").value = textMonthYear;
            document.getElementById("date_from").value = textMonthYear;
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
            $('.tracking-datatable').DataTable().ajax.reload();
            //$('select').selectpicker("refresh");
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
            alert('Error cargando servicios');

        });
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
                    url: '{{route("tracking.index")}}',
                    type: "POST",
                    data: function(d) {
                        d._token = "{{ csrf_token() }}",
                        d.centre_id = $('#centre_id option:selected').val(),
                        d.employee=  $('#employee_id option:selected').text(),
                        d.patient=  $('#patient_name option:selected').val(),
                        d.service=  $('#service_id option:selected').text(),
                        d.state = $('#state_id option:selected').text(),
                        date1=$('#date_from').val().replaceAll('-', '/');
                        date2=$('#date_to').val().replaceAll('-', '/');
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
                        targets: 6,
                        data: "state_date",
                        type: "date",
                        // className: 'dt-body-center',
                        render: function(data, type, row) {

                            var datetime = moment(data, 'YYYY-M-D');
                            var displayString = moment(datetime).format('D-M-YYYY');

                            if (type === 'display' || type === 'filter') {
                                return displayString;
                            } else {
                                return datetime; // for sorting
                            }
                        }
                    },
                    {
                        targets: '_all',
                        className: 'dt-body-center',
                    },
                    {
                        targets: -1,
                        width: '30%'
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

        console.log(params.trackingState);

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
            console.log(textStatus);
            console.log(errorThrown);

        });
    });
</script>

@endsection