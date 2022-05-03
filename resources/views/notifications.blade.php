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

        </div>
        <table class="table table-bordered notifications-datatable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Empleado</th>
                    <th>Servicio</th>
                    <th>Fecha</th>
                    <th>Fecha de cancelación</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>

<script type="text/javascript">
    var table;
    btn - addition
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
        name: 'state_date',
        data: 'state_date'
    });
    columnsFilled.push({
        data: 'cancellation_date',
        name: 'cancellation_date'
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
            $('select').val('');
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

        if ($.fn.dataTable.isDataTable('.notifications-datatable')) {
            table = $('.notifications-datatable').DataTable();
        } else {
            table = $('.notifications-datatable').DataTable({

                order: [3, "desc"],
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
                            d.date = $('#monthYearPicker').val()
                    },
                    dataSrc: function(json) {
                        $('#btnSubmit').show();
                        $('#btnSubmitLoad').hide();

                        return json.data;
                    }
                },
                columns: columnsFilled,
                columnDefs: [{

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