@extends('layouts.logged')
@section('content')
    @include('inc.navbar')

    <link rel="stylesheet" href="{{ asset('/css/buttons.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/tracking.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/modal.css') }}">

    <div class="content">
        <div class="container-fluid">
            <div class="row ">
                <div class="col-lg-12 mb-5" style="margin-top: 120px">
                    <div class="card " style="min-height: 200px">
                        <div class="card-header card-header-danger">
                            <h4 class="card-title">Borrar seguimiento</h4>
                        </div>
                        <div class="d-flex justify-content-end">
                            <img src="/assets/img/deleteServices.png" width="300">
                        </div>
                    </div>
                </div>
            </div>

            <table class="table  table-striped table-bordered  tracking-delete-datatable">
                <thead class="table-header">
                    <th>Centro Prescriptor</th>
                    <th>Empleado </th>
                    <th>H.C.</th>
                    <th>Paciente</th>
                    <th>Servicio</th>
                    <th>Cita</th>
                    <th>Fecha Servicio</th>
                    <th>Fecha Facturación</th>
                    <th>Fecha Validación</th>
                    <th>Acciones</th>
                </thead>
                <tbody>
                </tbody>
            </table>

        </div>
    </div>


    <style>
        .col-md-12 {
            padding: 0;
        }

        .row {
            justify-content: center;
            margin: 0;
        }

        table.dataTable.dataTable_width_auto {
            width: 100%;
        }
    </style>

    <script type="text/javascript">
        function confirmRequest(state, id) {
            confirmWithInput().then((result) => {
                    destroy(id,result);
            });
        }

        var table;
        $(function() {
            $(".nav-item").each(function() {
                $(this).removeClass("active");
            });
            $("#btnCheckDelete").text('Buscar');
            $('#trackingRemove').addClass('active');
            $('#pagesTracking').addClass('show');
            loadDeleteTable();

            function loadDeleteTable() {
                var url = "{{ route('tracking.searchDelete') }}";
                table = $('.tracking-delete-datatable').DataTable({
                    processing: true,
                    serverSide: true,
                    language: {
                        "url": "{{ asset('dataTables/Spanish.json') }}"
                    },
                    ajax: {
                        url: url,
                        type: "POST",
                        data: function(d) {
                            d._token = "{{ csrf_token() }}",
                                d.search = $('input[type="search"]').val()

                        }
                    },
                    columnDefs: [{
                        targets: [-1, 0, 1, 2, 3, 4, 5, 6, 7, 8],
                        visible: true,
                        className: 'dt-body-center'
                    }],
                    columns: [{
                            data: 'centre',
                            name: 'centre'
                        },
                        {
                            data: 'employee',
                            name: 'employee'
                        },
                        {
                            data: 'hc',
                            name: 'hc'
                        },
                        {
                            data: 'patient_name',
                            name: 'patient_name'
                        },
                        {
                            data: 'service',
                            name: 'service'
                        },
                        {
                            data: 'apointment_date',
                            name: 'apointment_date'
                        },
                        {
                            data: 'service_date',
                            name: 'service_date'
                        },
                        {
                            data: 'invoiced_date',
                            name: 'invoiced_date'
                        },
                        {
                            data: 'validation_date',
                            name: 'validation_date'
                        },
                        {
                            data: 'action',
                            name: 'action',
                            searchable: true
                        }
                    ],
                    search: {
                        "regex": true,
                        "smart": true,
                    },
                    initComplete: function() {
                        this.api().columns().every(function() {
                            var column = this;
                            var input = document.createElement("input");
                            $(input).appendTo($(column.footer()).empty())
                                .on('change', function() {
                                    var val = $.fn.dataTable.util.escapeRegex($(this)
                                .val());
                                    column
                                        .search(val ? '^' + val + '$' : '', true, false)
                                        .draw();
                                });
                        });
                    }
                });
            }
        });

        function destroy(id,reason) {
            trackingId = id;
            var params = {
                reason: reason,
                id: trackingId
            };

            $.ajax({
                url: 'destroy/' + trackingId,
                type: 'get',
                data: params,
                success: function(response, textStatus, jqXHR) {
                    if (textStatus === 'success') {
                        showAlert('success', 'Seguimiento Eliminado Correctamente')
                        table.ajax.reload();
                    }
                },
                complete: function() {
                    table.ajax.reload();
                },
                error: function(xhr, status, error) {
                    var response = JSON.parse(xhr.responseText);
                    showAlert('error', response);

                }
            }).fail(function(jqXHR, textStatus, errorThrown) {
                alert('Error' + jqXHR.responseText);
            });
        }
    </script>
@endsection
