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


<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card ">
                    <div class="card-header card-header-info card-header-text">
                        <div class="card-text">
                            <h4 class="card-title">Nueva solicitud</h4>
                        </div>
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
                    <div class="card-header card-header-info card-header-text">
                        <div class="card-text">
                            <h4 class="card-title">Solicitudes</h4>
                        </div>
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

<div class="modal" tabindex="-1" role="dialog" id="modal-validate">
    <input type="hidden" id="idRequest" />
    <input type="hidden" id="validateVal" />
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmación</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p id="message-validation" class="px-4 text-center"></p>
            </div>
            <div class="modal-footer center">
                <button id="btnConfirmRequest" type="button" class="btn btn-success">SI</button>
                <button id="btnCancelRequest" type="button" class="btn btn-warning" data-dismiss="modal">NO</button>
                </p>
            </div>
        </div>
    </div>
</div>




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

    function validateRequest(state, requestId) {
        if (state == 1) {
            $("#message-validation").html('¿Confirma la validación de la solicitud?');
        } else {
            $("#message-validation").html('¿Confirma la invalidación de la solicitud?');
        }

        $("#validateVal").val(state);
        $("#idRequest").val(requestId);
        $("#modal-validate").modal('show');
    }

    function confirmRequest() {

        var params = {
            'idrequest': $("#idRequest").val(),
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
            columnDefs: [
                {
                    targets: -1,
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