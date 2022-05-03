@extends('layouts.logged')

@section('content')
@include('inc.navbar')


@if (session('success'))
<div class="alert alert-success" role="alert">
    {{ session('success') }}
</div>
@endif

@if (session('error'))
<div class="alert alert-danger" role="alert">
    {{ session('error') }}
</div>
@endif
<div id="alertErrorTrackingDate" class="alert alert-danger" role="alert" style="display: none">
</div>


<div class="content">
    <div class="container-fluid">
        <div class="row col-md-12 mb-3 ">
            <div class="col-md-1 form-group input-group date" style="padding-top: 15px;">
                <label for="monthYearPicker">Fecha: </label>
                <input id="monthYearPicker" class='form-control' type="text" placeholder="yyyy/mm" />
                <input type="hidden" name="monthYear" id="monthYear" />
            </div>

            <div class="form-group col-md-3">
                <div class="dropdown bootstrap-select">
                    <select class="selectpicker" name="centre_id" id="centre_id" data-size="7" data-style="btn btn-primary btn-round" title=" Seleccione Centro" tabindex="-98">
                        @foreach ($centres as $centre)
                        <option value="{{$centre->id}}">{{$centre->name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-group col-md-2">
                <div class="dropdown bootstrap-select">
                    <select class="selectpicker" name="state_id" id="state_id" data-size="7" data-style="btn btn-primary btn-round" title=" Seleccione Estado" tabindex="-98">
                        @foreach ($states as $state)
                        <option value="{{  $state  }}">{{$state}}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-md-3  mt-2">
                <button id="btnClear" href="#" class="btn btn-fill btn-warning">
                    {{ __('Limpiar formulario') }}
                </button>
                <button id="btnSubmit" type="submit" class="btn btn-fill btn-outline-corporate">{{ __('Buscar') }}</button>
                <button id="btnSubmitLoad" type="submit" class="btn btn-success" style="display: none">
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    {{ __('Obteniendo datos...') }}
                </button>
            </div>
            <div class="col-md-2 text-right" id="blockNewTracking">
                <a href="{{ route('tracking.create') }}" id="btnNewTracking" class="btn btn-addition btn-lg"> Nuevo Seguimiento</a>
            </div>
        </div>
        <table class="table table-bordered tracking-datatable">
            <thead>
                <tr>
                    <th>Centro Prescriptor</th>
                    <th>Empleado</th>
                    <th>H.C.</th>
                    <th>Paciente</th>
                    <th>Servicio</th>
                    <th>Estado</th>
                    <th>Fecha</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
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
        data: 'action',
        name: 'action',
        searchable: true,
        width: 300
    });

    var d = new Date();
    var dayOfMonth = d.getDate();
    var year = d.getFullYear();
    var month = 1;

    if (d.getMonth() < 11) {
        if (dayOfMonth > 20) {
            month = d.getMonth() + 2;
        } else {
            month = d.getMonth() + 1;
        }
    } else {
        if (dayOfMonth > 20) {
            month = 1;
            year = year + 1;
        } else {
            month = d.getMonth() + 1;
        }
    }

    var textMonthYear = month >= 10 ? month : '0' + month;
    textMonthYear += '/' + year;

    $('#monthYearPicker').val(textMonthYear);
    // Default functionality.
    $('#monthYearPicker').MonthPicker();
    $('#monthPicker').datepicker($.datepicker.regional["es"]);

    $(function() {
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

        $("#btnSubmit").on('click', function(e) {
            e.preventDefault();
            //$("#finalValidationForm").attr('action','{{ route("tracking.index_validation_final") }}');
            $('#btnSubmit').hide();
            $('#btnSubmitLoad').show();
            $('#btnSubmitLoad').prop('disabled', true);
            //$('#centre').val($( "#centre_id option:selected" ).text());
            getTrackingData();
        });

        function clearForms() {
            $('select').val('');
            $('select#centre_id').selectpicker("refresh");
            $('select#state_id').selectpicker("refresh");
            //$('select').selectpicker("refresh");
        }


        $("#btnClear").on('click', function(e) {
            e.preventDefault();
            clearForms();
        });
    });

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
                $('#alertErrorTrackingDate').show();
                $('#btnSubmitLoad').hide();
                $('#btnSubmit').show();
            }

        }).fail(function(jqXHR, textStatus, errorThrown) {

            //alert('Error cargando servicios');

        });
    }


    function getTrackingData() {

        //var fecha = $('#monthYearPicker').val(); 

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
                            d.state = $('#state_id option:selected').val(),
                            d.date = $('#monthYearPicker').val(),
                            d.search = $('input[type="search"]').val()

                    },
                    dataSrc: function(json) {
                        $('#btnSubmit').show();
                        $('#btnSubmitLoad').hide();

                        return json.data;
                    }
                },
                columns: columnsFilled,
                columnDefs: [{
                        targets: 6,
                        data: "state_date",
                        type: "date",
                        render: function(data, type, row) {

                            var datetime = moment(data, 'YYYY-M-D');
                            var displayString = moment(datetime).format('D-M-YYYY');

                            if (type === 'display' || type === 'filter') {
                                return displayString;
                            } else {
                                return datetime; // for sorting
                            }
                        }
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
</script>

@endsection