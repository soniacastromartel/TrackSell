@extends('layouts.logged')
@section('content')
    @include('inc.navbar')

    <link rel="stylesheet" href="{{ asset('/css/buttons.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/tracking.css') }}">

    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-banner " style="margin-top:120px">

                        <div class="card-header card-header-danger">

                            <h4 class="card-title">Cambio De Centro</h4>

                        </div>

                        <div class="card-body col-lg-10 " style="margin-left: 30px;">
                            <form id="createRequestChangeCentre" action="{{ route('tracking.saveRequest') }}"
                                method="POST">
                                @csrf
                                @method('POST')
                                @include('tracking.form_change_centre')
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row" style="margin-top:50px;">
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

    <style>
        .card-banner {
            background-image: url(/assets/img/banners/2.jpg);
            background-repeat: no-repeat;
            background-size: contain;
            background-position-x: right;
            /* max-height: 420px; */
        }
    </style>
    <script type="text/javascript">
        setDate();

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
            data: 'action',
            name: 'action',
            searchable: true,
            width: 300
        });

        function confirm(state, id) {
            confirmedRequest().then((result) => {
                if (result.isConfirmed) {
                    destroy(id);
                }
            });
        }

        var table;


        function destroy(id) {
            console.log(id);
            var params = {
                'id': id,
                '_token': "{{ csrf_token() }}"
            };
            $.ajax({
                url: "{{ route('tracking.confirmRequest') }}",
                type: 'post',
                data: params,
                dataType: 'json',
                success: function(data, textStatus, jqXHR) {
                    if (textStatus === 'success') {
                        showAlert('success', 'Eliminado Correctamente');
                    }
                },
                error: function(xhr, status, error) {
                    var response = JSON.parse(xhr.responseText);
                    showAlert('error', response);
                },
                complete: function(xhr, jqXHR) {
                    console.log(xhr.responseText);
                    console.log(jqXHR.responseText);
                    console.log(jqXHR);
                    showAlert('success', 'Eliminado Correctamente')
                    table.ajax.reload();
                }

            }).fail(function(jqXHR, textStatus, errorThrown) {
                showAlert('error', jqXHR.responseText);
            });
        }

        $(function() {
            $(".nav-item").each(function() {
                $(this).removeClass("active");
            });
            $('#pagesTracking').addClass('show');
            $('#requestChange').addClass('active');

            table = $('.request-changes-datatable').DataTable({
                order: [0, "desc"],
                processing: true,
                serverSide: true,
                language: {
                    "url": "{{ asset('dataTables/Spanish.json') }}"
                },
                ajax: {
                    url: '{{ route('tracking.getRequestChanges') }}',
                    type: "POST",
                    data: function(d) {
                        d._token = "{{ csrf_token() }}"
                    }
                },
                columns: columnsFilled,
                columnDefs: [{
                        width: "15%",
                        targets: 0
                    },
                    {
                        width: "15%",
                        targets: 1
                    },
                    {
                        width: "20%",
                        targets: 2
                    },
                    {
                        width: "20%",
                        targets: 3
                    },
                    {
                        width: "20%",
                        targets: 4
                    },
                    {
                        width: "10%",
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
                                return datetime;
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
                                return datetime;
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
