@extends('layouts.logged')
@section('content')
@include('inc.navbar')

<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
<link rel="stylesheet" href="{{ asset('/css/buttons.css') }}">
<link rel="stylesheet" href="{{ asset('/css/tracking.css') }}">

<div class="content">
    <div class="container-fluid" style="margin-top:120px" >
        <div class="card">
            <div class="card-header card-header-danger">
                <h4 class="card-title">Notificaciones</h4>
            </div>
            <div class="card-body">
                <div class="row col-md-12 mb-3 justify-between">
                        <div class="input-group date">
                            <input id="monthYearPicker" class='form-control' type="text" placeholder="yyyy/mm" />
                            <span id="icon-date" class="material-symbols-outlined"> calendar_month</span>
                            <input type="hidden" name="monthYear" id="monthYear" />
                        </div>
                    <div class="row align-content-end">
                        <button id="btnClear" href="#" class="btn-refresh">
                        <span id="icon-refresh" class="material-icons">
                            refresh
                            </span>   {{ __('Refrescar') }}
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

    var columnsFilled = [
        { data: 'id', name: 'id' },
        { data: 'employee', name: 'employee', searchable: true },
        { data: 'service', name: 'service', searchable: true },
        { data: 'started_date', name: 'started_date' },
        { data: 'cancellation_date', name: 'cancellation_date' },
        { data: 'cancellation_reason', name: 'cancellation_reason' }
    ];

    $(document).ready(function() {
        $(".nav-item").each(function() {
            $(this).removeClass("active");
        });
        $('#pagesTracking').addClass('show');
        $('#supervisorNotificationsIndex').addClass('active');

        table = $('.notifications-datatable').DataTable({
            order: [4, "desc"],
            processing: true,
            serverSide: true,
            language: {
                "url": "{{ asset('dataTables/Spanish.json') }}"
            },
            ajax: {
                url: '{{ route("notifications.index") }}',
                type: "POST",
                data: function(d) {
                    d._token = "{{ csrf_token() }}";
                    d.date = $('#monthYearPicker').val();
                    d.search = $('input[type="search"]').val();
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
                        var datetime = moment(data, 'YYYY-MM-DD');
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
                        var datetime = moment(data, 'YYYY-MM-DD');
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
            }
        });

        $('#monthYearPicker').on('change', function() {
            table.ajax.reload();
        });

        $("#btnClear").on('click', function(e) {
            e.preventDefault();
            clearForms();
        });
    });

    function setDate(){
        var d = new Date();
        var year = d.getFullYear();
        var month = d.getMonth() + 1;
        var textMonthYear = month >= 10 ? month : '0' + month;
        textMonthYear += '/' + year;

        $('#monthYearPicker').val(textMonthYear);
        $('#monthYearPicker').MonthPicker({
            ShowIcon: false,
            OnAfterChooseMonth: function(selectedDate) {
                $('#monthYearPicker').trigger('change');
            }
        });
    }

    function clearForms() {
        setDate();
        table.search('').draw();
        table.ajax.reload();
    }
</script>


@endsection