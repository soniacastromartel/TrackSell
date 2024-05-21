@extends('layouts.logged')
@section('content')
@include('inc.navbar')
@include('common.alert')

<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
<link rel="stylesheet" href="{{ asset('/css/buttons.css') }}">
<link rel="stylesheet" href="{{ asset('/css/tracking.css') }}">
<link rel="stylesheet" href="{{ asset('/css/dashboard.css') }}">

<div id="alertErrorTrackingDate" class="alert alert-danger" role="alert" style="display: none"></div>


<div class="content">
    <div class="container-fluid" style="margin-top:120px" >
        <div class="card">
            <div class="card-header card-header-danger">
                <h4 class="card-title">Notificaciones</h4>
            </div>
            <div class="card-body">
                <div class="row col-md-12 mb-3 justify-between">
                    <div class="col-md-2">
                        <div class="mt-2 input-group date">
                            <input id="monthYearPicker" class='form-control' type="text" placeholder="yyyy/mm" />
                            <span id="icon-date" class="material-symbols-outlined"> calendar_month</span>
                            <input type="hidden" name="monthYear" id="monthYear" />
                        </div>
                    </div>

                    <div class="row align-content-end">
                        <button id="btnClear" href="#" class="btn-refresh">
                        <span id="icon-refresh" class="material-icons">
                            refresh
                            </span>   {{ __('Limpiar formulario') }}
                        </button>
                        <button id="btnSubmit" type="submit" class="btn-search"><span id="icon-search" class="material-icons">
                            search</span> {{ __('Buscar') }}</button>
                        <button id="btnSubmitLoad" type="submit" class="btn-search" style="display: none">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            
                        </button>
                    </div>

                </div>


            </div>
        </div>
        <table class="table table-bordered notifications-datatable">
            <thead class="table-header">
                <tr>
                    <th>ID</th>
                    <th>Empleado</th>
                    <th>Servicio</th>
                    <th>Fecha de Inicio</th>
                    <th>Fecha de cancelación</th>
                    <th>Motivo de cancelación</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>

<script type="text/javascript">
    var table;
    setDate();

    var columnsFilled = [];
    columnsFilled.push({
        data: 'id',
        name: 'id'
    });
    columnsFilled.push({
        data: 'employee',
        name: 'employee',
        searchable: true
    });
    columnsFilled.push({
        data: 'service',
        name: 'service',
        searchable: true
    });
    columnsFilled.push({
        name: 'started_date',
        data: 'started_date'
    });
    columnsFilled.push({
        data: 'cancellation_date',
        name: 'cancellation_date'
    });
    columnsFilled.push({
        data: 'cancellation_reason',
        name: 'cancellation_reason'
    });

    // var d = new Date();
    // var dayOfMonth = d.getDate();
    // var year = d.getFullYear();
    // var month = 1;

    // if (d.getMonth() < 11) {
    //     if (dayOfMonth > 20) {
    //         month = d.getMonth() + 2;
    //     } else {
    //         month = d.getMonth() + 1;
    //     }
    // } else {
    //     if (dayOfMonth > 20) {
    //         month = 1;
    //         year = year + 1;
    //     } else {
    //         month = d.getMonth() + 1;
    //     }
    // }

    // var textMonthYear = month >= 10 ? month : '0' + month;
    // textMonthYear += '/' + year;

    // $('#monthYearPicker').val(textMonthYear);
    // // Default functionality.
    // $('#monthYearPicker').MonthPicker();
    // $('#monthPicker').datepicker($.datepicker.regional["es"]);

    $(function() {
        $(".nav-item").each(function() {
            $(this).removeClass("active");
        });
        $('#pagesNotification').addClass('show');
        $('#supervisorNotificationsIndex').addClass('active');

        var state = "{{ collect(request()->segments())->last() }}";
        state = state.split("_")[1];

        var tableHtml = '';

        tableHtml = '<tr><th>Centro Prescriptor</th></tr>';
        getTrackingData();

        $("#btnSubmit").on('click', function(e) {
            e.preventDefault();
            $('#btnSubmit').hide();
            $('#btnSubmitLoad').show();
            $('#btnSubmitLoad').prop('disabled', true);
            getTrackingData();
        });

        function clearForms() {
            setDate();
            table.search('').draw();
            table.ajax.reload();
        }

        $("#btnClear").on('click', function(e) {
            e.preventDefault();
            clearForms();
        });
    });

    function setDate(){
    var d = new Date();
    var dayOfMonth = d.getDate();
    var year = d.getFullYear();
    var month = 1;

    if (d.getMonth() < 11) {
        if (dayOfMonth > 20) {
            month = d.getMonth() + 1;
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
    $('#monthYearPicker').MonthPicker({
        ShowIcon: false,
    });
    $('#monthPicker').datepicker($.datepicker.regional["es"]);
}

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
                $('#btnSubmit').show();
            }

        }).fail(function(jqXHR, textStatus, errorThrown) {
            alert('Error'+jqXHR.responseText);
            //alert('Error cargando servicios');

        });
    }


    function getTrackingData() {

        if ($.fn.dataTable.isDataTable('.notifications-datatable')) {
            table = $('.notifications-datatable').DataTable();
        } else {
            table = $('.notifications-datatable').DataTable({

                order: [4, "desc"],
                processing: true,
                serverSide: true,
                language: {
                    "url": "{{ asset('dataTables/Spanish.json') }}"
                },
                ajax: {
                    url: '{{route("notifications.index")}}',
                    type: "POST",
                    data: function(d) {
                        d._token = "{{ csrf_token() }}",
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
                columnDefs: [
                    { width: "5%", targets: 0 },
                    { width: "15%", targets: 1 },
                    { width: "15%", targets: 2 },
                    { width: "10%", targets: 3 },
                    { width: "10%", targets: 4 },
                    { width: "15%", targets: 5 },
                    {

                        targets: 4,
                        data: "cancellation_date",
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
                    },

                    {
                        targets: 3,
                        data: "started_date",
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