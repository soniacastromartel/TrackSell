@extends('layouts.logged')
@section('content')
    @include('inc.navbar')


    <link rel="stylesheet" href="{{ asset('/css/buttons.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/incentives.css') }}">

    <div class="content">
        <div class="container-fluid">
            <div class="card incentive-logo" style="margin-top:120px ">
                <form id="incentivesForm" method="POST">
                    @csrf
                    @method('POST')
                    <div class="card-header card-header-danger">
                        <h4 class="card-title">Tarifas & Incentivos</h4>
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
                                            @if ($user->rol_id != 1)
                                                @foreach ($centres as $centre)
                                                    @if ($centre->id == $user->centre_id)
                                                        <option class="text-uppercase" value="{{ $centre->id }}" selected>
                                                            {{ $centre->name }}
                                                        </option>
                                                    @endif
                                                @endforeach
                                            @else
                                                @foreach ($centres as $centre)
                                                    <option class="text-uppercase" value="{{ $centre->id }}">
                                                        {{ $centre->name }}
                                                    </option>
                                                @endforeach
                                            @endif
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
                        @if ($user->rol_id == 1)
                            <div class="row col-lg-8" style="margin-left: 25px; margin-bottom: 50px;">
                                <div class="form-group">
                                    <div class="select-wrapper">
                                        <button id="btnImportIncentives" class="file-upload btn-import">
                                            <span id="icon-import" class="material-symbols-outlined">upload</span>Importar
                                            Incentivos
                                            <input type="file" name="incentiveInputFile" id="incentiveInputFile"
                                                class="upload" />
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </form>
            </div>

            {{-- Tabla --}}
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
        <!-- Modal -->
        <div class="modal fade" id="editIncentiveModal" tabindex="-1" aria-labelledby="editIncentiveModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editIncentiveModalLabel">Editar Incentivo</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="editIncentiveForm" method="POST">
                            @csrf
                            @method('POST')
                            <div class="mb-3">
                                <label for="name" class="form-label">Servicio</label>
                                <input type="string" class="form-control" id="name" name="name">
                            </div>
                            <hr>
                            <div class="mb-3">
                                <label for="price" class="form-label">Precio</label>
                                <input type="number" class="form-control" id="price" name="price">
                            </div>
                            <div class="mb-3">
                                <label for="incentive_direct" class="form-label">Incentivo Directo</label>
                                <input type="number" class="form-control" id="incentive_direct"
                                    name="incentive_direct">
                            </div>
                            <div class="mb-3">
                                <label for="incentive_obj1" class="form-label">Incentivo Objetivo 1</label>
                                <input type="number" class="form-control" id="incentive_obj1" name="incentive_obj1">
                            </div>
                            <div class="mb-3">
                                <label for="incentive_obj2" class="form-label">Incentivo Objetivo 2</label>
                                <input type="number" class="form-control" id="incentive_obj2" name="incentive_obj2">
                            </div>
                            <div class="mb-3">
                                <label for="bonus_obj1" class="form-label">Bonus Supervisor 1</label>
                                <input type="number" class="form-control" id="bonus_obj1" name="bonus_obj1">
                            </div>
                            <div class="mb-3">
                                <label for="bonus_obj2" class="form-label">Bonus Supervisor 2</label>
                                <input type="number" class="form-control" id="bonus_obj2" name="bonus_obj2">
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" id="closeModal" class="btn btn-refresh-circle"
                            data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" id="saveIncentiveBtn" class="btn btn-search-circle">Guardar</button>
                    </div>
                </div>
            </div>
        </div>
        <style>
            .highlight-row {
                color: green !important;
                transition: background-color 1s ease-in-out;
            }
        </style>
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
                data: 'service',
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

                $(document).on('click', '.btn-edit', function() {
                    console.log($(this).data());
                    var serviceId = $(this).data('id');
                    var serviceName = $(this).data('name');
                    var servicePrice = $(this).data('price');
                    var incentiveDirect = $(this).data('direct-incentive');
                    var incentiveObj1 = $(this).data('obj1');
                    var incentiveObj2 = $(this).data('obj2');
                    var bonusObj1 = $(this).data('bonus1');
                    var bonusObj2 = $(this).data('bonus2');

                    $('#editIncentiveModal #name').val(serviceName);
                    $('#editIncentiveModal #price').val(servicePrice);
                    $('#editIncentiveModal #incentive_direct').val(incentiveDirect);
                    $('#editIncentiveModal #incentive_obj1').val(incentiveObj1);
                    $('#editIncentiveModal #incentive_obj2').val(incentiveObj2);
                    $('#editIncentiveModal #bonus_obj1').val(bonusObj1);
                    $('#editIncentiveModal #bonus_obj2').val(bonusObj2);
                    $('#editIncentiveModal').modal('show');
                    $('#saveIncentiveBtn').data('id', serviceId);
                });

                $('#closeModal').on('click', function() {
                    $('#editIncentiveModal').modal('hide');
                });

                $('#saveIncentiveBtn').on('click', function() {
                    var serviceId = $(this).data('id');
                    var price = $('#price').val();
                    var incentiveDirect = $('#incentive_direct').val();
                    var incentiveObj1 = $('#incentive_obj1').val();
                    var incentiveObj2 = $('#incentive_obj2').val();
                    var bonusObj1 = $('#bonus_obj1').val();
                    var bonusObj2 = $('#bonus_obj2').val();
                    var name = $('#name').val();

                    console.log(name);

                    $.ajax({
                        url: '/incentives/edit',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            service_id: serviceId,
                            price: price,
                            name: name,
                            incentive_direct: incentiveDirect,
                            incentive_obj1: incentiveObj1,
                            incentive_obj2: incentiveObj2,
                            bonus_obj1: bonusObj1,
                            bonus_obj2: bonusObj2
                        },
                        success: function(response) {
                            console.log(response);
                            showAlert('success', response.message ||
                                'Incentivo Actualizado Correctamente');
                            $('#editIncentiveModal').modal('hide');

                            var table = $('.services-datatable').DataTable();
                            table.ajax.reload(null, false);


                            var updatedRow = table.row(function(idx, data, node) {
                                console.log("Row data:", data);
                                console.log("Response ID:", response.data.id);
                                return data.serviceprice_id == response.data
                                    .id;
                            });
                            console.log("Updated row:", updatedRow);
                            if (updatedRow.any()) {
                                var rowNode = updatedRow.node();
                                $(rowNode).addClass(
                                    'highlight-row');
                                var rowData = updatedRow.data();
                                console.log(rowData);
                                table.row(rowNode).remove().draw(
                                    false);
                                table.row.add(rowData).draw(
                                    false);
                            }

                        },
                        complete: function() {
                            // table.ajax.reload();
                        },
                        error: function(error) {
                            showAlert('error', error.responseJSON.message ||
                                'Incentivo No Actualizado Correctamente');
                        }
                    });
                });

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
                    document.getElementById("fileuploadurl").value = this.value.replace(
                        /C:\\fakepath\\/i, '');
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
                            url: '{{ route('incentives.index') }}',
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
                    url: "{{ route('incentives.destroy') }}",
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
