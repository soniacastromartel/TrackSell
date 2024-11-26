@extends('layouts.logged')
@section('content')
    @include('inc.navbar')


    <link rel="stylesheet" href="{{ asset('/css/buttons.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/incentives.css') }}">

    <div class="content">
        <div class="container-fluid">
            <div class="card incentive-logo" style="margin-top:120px ">
                <div class="card-header card-header-danger">
                    <h4 class="card-title">Precios & Incentivos</h4>
                </div>
                <div class="card-body">
                    <div class="row col-md-12 mb-4 ">
                        <div class="row col-lg-8 ">
                            <div class="form-group ">
                                <div class="select-wrapper">
                                    <span id="icon-select" class="icon-select material-symbols-outlined">
                                        business
                                    </span>
                                    <select class="selectpicker" name="centre_id" id="centre_id" data-size="7"
                                        data-style="btn btn-red-icot btn-round" title="Centro" tabindex="-98"
                                        onchange="getServiceIncentives()">
                                        @foreach ($centres as $centre)
                                            <option value="{{ $centre->id }}">{{ $centre->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group col-md-4">
                                <div class="select-wrapper">
                                    <span id="icon-select" class="icon-select material-symbols-outlined">
                                        medical_services </span>
                                    <select class="selectpicker" name="service_id" id="service_id" data-size="7"
                                        data-style="btn btn-red-icot btn-round" title="Servicio" tabindex="-98"
                                        onchange="getServiceIncentives()">
                                        @foreach ($services as $service)
                                            <option value="{{ $service->id }}">{{ $service->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group col-md-4" style="display:flex;justify-content:end;align-items:center;">
                            <button id="btnClearRefresh" href="#" class="btn-refresh-circle">
                                <span class="material-icons">
                                    refresh
                                </span>
                            </button>
                        </div>
                    </div>
                </div>

            </div>
            <div class="col-md-12 mb-3 ">
                <table class="table  table-striped table-bordered services-datatable" style="width:100%;">
                    <thead class="table-header">
                        <tr>
                            <th>Nombre</th>
                            <th>Centro</th>
                            <th>Precio</th>
                            <th>Incentivo Directo</th>
                            <th>Incentivo Objetivo 1</th>
                            <th>Incentivo Objetivo 2</th>
                            <th>Bonus Objetivo 1</th>
                            <th>Bonus Objetivo 2</th>
                            <th>Acciones </th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>

        <script type="text/javascript">
            function confirmRequest(state, id) {
                confirmedRequest().then((result) => {
                    if (result.isConfirmed) {
                        destroyIncentive(id);
                    }
                });
            }

            var table;

            var columnsFilled = [];
            columnsFilled.push({
                data: 'name',
                name: 'name'
            });
            columnsFilled.push({
                data: 'centre',
                name: 'centre'
            });
            columnsFilled.push({
                data: 'price',
                name: 'price'
            });
            columnsFilled.push({
                data: 'incentive_direct',
                name: 'incentive_direct'
            });
            columnsFilled.push({
                data: 'incentive_obj1',
                name: 'incentive_obj1'
            });
            columnsFilled.push({
                data: 'incentive_obj2',
                name: 'incentive_obj2'
            });
            columnsFilled.push({
                data: 'bonus_obj1',
                name: 'bonus_obj1'
            });
            columnsFilled.push({
                data: 'bonus_obj2',
                name: 'bonus_obj2'
            });

            columnsFilled.push({
                data: 'action',
                name: 'action'
            });

            $(function() {
                $(".nav-item").each(function() {
                    $(this).removeClass("active");
                });
                $('#pagesConfig').addClass('show');
                $('#adminServiceIncentive').addClass('active')

                function clearForms() {
                    $('select#centre_id').val('');
                    $('select#service_id').val('');
                    $('select#centre_id').selectpicker("refresh");
                    $('select#service_id').selectpicker("refresh");
                    $('.services-datatable').DataTable().search('').draw();
                    $('.services-datatable').DataTable().ajax.reload();
                }
                $("#btnClearRefresh").on('click', function(e) {
                    e.preventDefault();
                    clearForms();
                });
                getServiceIncentives();
                $("#targetInputIncentiveFile").on('change', function() {
                    document.getElementById("fileuploadurl").value = this.value.replace(/C:\\fakepath\\/i, '');
                    $("#importTargetForm").attr('action', '{{ route('target.importIncentive') }}');
                    $("#importTargetForm").attr('enctype', "multipart/form-data");
                    $('#btnImportIncentives').hide();
                    $('#targetInputFileLoad').show();
                    $("#importTargetForm").submit();
                });

            });

            function getServiceIncentives() {
                if ($.fn.dataTable.isDataTable('.services-datatable')) {
                    table = $('.services-datatable').DataTable();
                } else {
                    table = $('.services-datatable').DataTable({
                        responsive: true,
                        order: [
                            [1, "asc"]
                        ],
                        processing: true,
                        serverSide: true,
                        language: {
                            decimal: ',',
                            thousands: '.',
                            "url": "{{ asset('dataTables/Spanish.json') }}"
                        },
                        ajax: {
                            url: '{{ route('services.incentives') }}',
                            type: "POST",
                            data: function(d) {
                                d.centre = $('#centre_id option:selected').val(),
                                    d._token = "{{ csrf_token() }}",
                                    d.search = $('input[type="search"]').val()
                                d.service = $("#service_id option:selected").val()
                            },
                            dataSrc: function(json) {
                                return json.data;
                            }
                        },
                        columnDefs: [{
                                targets: [1, 2, 3, 4, 5, 6, 7, 8],
                                visible: true,
                                className: 'dt-body-center'
                            },
                            {
                                targets: [2, 3, 4, 5, 6, 7],
                                render: $.fn.dataTable.render.number('.', ',',
                                    2)
                            },

                        ],
                        columns: columnsFilled,
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

            function destroyIncentive(id) {
                params = {};
                params["_token"] = "{{ csrf_token() }}";
                params["serviceprice_id"] = id;
                $.ajax({
                    url: "{{ route('services.destroyIncentive') }}",
                    type: 'post',
                    data: params,
                    success: function(response, textStatus, jqXHR) {

                        if (textStatus === 'success') {
                            showAlert('success', 'Incentivo Eliminado Correctamente');
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
                    alert('Error cargando servicios');
                });
            }
        </script>
    @endsection
