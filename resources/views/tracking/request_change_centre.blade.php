@extends('layouts.logged')
@section('content')
@include('inc.navbar')
@include('common.alert')

<link rel="stylesheet" href="{{ asset('/css/buttons.css') }}">
<link rel="stylesheet" href="{{ asset('/css/tracking.css') }}">

<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card " style="margin-top:100px">

                    <div class="card-header card-header-danger">
                    
                            <h4 class="card-title">Nueva solicitud</h4>
                     
                    </div>

                    <div class="card-body ">
                        <form id="createRequestChangeCentre" action="{{ route('tracking.saveRequest') }}" method="POST">
                            @csrf
                            @method('POST')
                            @include('tracking.form_change_centre')
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card ">
                    <div class="card-header card-header-danger">
                            <h4 class="card-title">Solicitudes</h4>
                    </div>
                    <div class="card-body ">
                        <table class="table table-bordered request-changes-datatable">
                            <thead class="table-header">
                                <tr>
                                    <th>Fecha inicio</th>
                                    <th>Fecha fin</th>
                                    <th>Empleado</th>
                                    <th>Centro Origen</th>
                                    <th>Centro Destino</th>
                                    <th>Observaciones</th>
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
    </div>
</div>

@include('common.modal')

<script type="text/javascript">
    var columnsFilled = [];
    columnsFilled.push({
        data: 'start_date',
        name: 'start_date'
    });
    columnsFilled.push({
        data: 'end_date',
        name: 'end_date'
    });
    columnsFilled.push({
        data: 'employee',
        name: 'employee'
    });
    columnsFilled.push({
        data: 'centre_origin',
        name: 'centre_origin'
    });
    columnsFilled.push({
        data: 'centre_destination',
        name: 'centre_destination'
    });
    columnsFilled.push({
        data: 'observations',
        name: 'observations'
    });
    columnsFilled.push({
        data: 'action',
        name: 'action',
        searchable: true,
        width: 300
    });

    var table;

    function validateRequest(state, id) {
        if (state == -1) {
            $("#message-validation").html('¿Confirma la eliminación de la solicitud?');
            $("#modal-title").html('ELIMINACIÓN');
        } else if (state == 0) {
            $("#message-validation").html('¿Confirma la invalidación de la solicitud?');
            $("#modal-title").html('CONFIRMACIÓN');
        } else {
            $("#message-validation").html('¿Confirma la validación de la solicitud?');
            $("#modal-title").html('CONFIRMACIÓN');
        }

        $("#validateVal").val(state);
        $("#id").val(id);
        $("#modal-validate").modal('show');
    }

    function confirmRequest() {
        var params = {
            'id': $("#id").val(),
            'state': $("#validateVal").val(),
            '_token': "{{ csrf_token() }}"
        };
        $.ajax({
            url: "{{ route('tracking.confirmRequest') }}",
            type: 'post',
            data: params,
            dataType: 'json',
            success: function(data, textStatus, jqXHR) {

                // if success, HTML response is expected, so replace current
                if (textStatus === 'success') {

                }
            },
            error: function(xhr, status, error) {

            },
            complete: function() {
                $("#modal-validate").modal('hide');
                table.ajax.reload();
            }

        }).fail(function(jqXHR, textStatus, errorThrown) {
            alert('Error'+jqXHR.responseText);

        });
    }

    $(function() {
        $(".nav-item").each(function() {
            $(this).removeClass("active");
        });
        $('#pagesTracking').addClass('show');
        $('#requestChange').addClass('active');

        $("#btnConfirmRequest").on('click', function(event) {
            confirmRequest();
        });

        table = $('.request-changes-datatable').DataTable({
            order: [0, "desc"],
            processing: true,
            serverSide: true,
            language: {
                "url": "{{ asset('dataTables/Spanish.json') }}"
            },
            ajax: {
                url: '{{route("tracking.getRequestChanges")}}',
                type: "POST",
                data: function(d) {
                    d._token = "{{ csrf_token() }}"
                    //d.centre_id  = $('#centre_id option:selected').val()
                }
            },
            columns: columnsFilled,
            columnDefs: [{
                    width: "10%",
                    targets: 0
                },
                {
                    width: "10%",
                    targets: 1
                },
                {
                    width: "15%",
                    targets: 2
                },
                {
                    width: "10%",
                    targets: 3
                },
                {
                    width: "10%",
                    targets: 4
                },
                {
                    width: "25%",
                    targets: 5
                },
                {
                    width: "15%",
                    targets: 5
                },
                {
                    targets: '_all',
                    visible: true,
                    className: 'dt-body-center'
                },
                {
                    targets: 0,
                    data: "state_date",
                    type: "date",
                    render: function(data, type, row) {

                        var datetime = moment(data, 'YYYY-M-D');
                        var displayString = moment(datetime).format('D-MM-YYYY');

                        if (type === 'display' || type === 'filter') {
                            return displayString;
                        } else {
                            return datetime; // for sorting
                        }
                    }
                },
                {
                    targets: 1,
                    data: "state_date",
                    type: "date",
                    render: function(data, type, row) {
                        var datetime = moment(data, 'YYYY-M-D');
                        var displayString = moment(datetime).format('D-MM-YYYY');

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

    });
    
</script>
@endsection
