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
                        <div class="row col-md-12 mb-4 " style="margin-top:30px;">
                            <div class="row col-lg-12 ">
                                <div class="form-group ">
                                    <label class="col-form-label-lg">CENTRO</label>
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

                                <div class="form-group col-md-3">
                                    <label class="col-form-label-lg">SERVICIO</label>

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
                                <div class="form-group col-md-2" style="margin-top:62px;">
                                    <button id="btnClearRefresh" href="#" class="btn-refresh-circle">
                                        <span class="material-symbols-outlined">
                                            refresh
                                        </span>
                                    </button>
                                </div>
                            </div>

                        </div>
                        <hr style="max-width: 600px;">

                        @if ($user->rol_id == 1)
                            <div class="row col-md-12 mb-4 ">
                                <div class="row col-lg-8 ">
                                    <div class="form-group">
                                        <div class="select-wrapper" style="margin-left: 20px;">
                                            <!-- Botón visible que el usuario clickea -->
                                            <button id="btnImportIncentives" class="file-upload btn-import">
                                                <span id="icon-import"
                                                    class="material-symbols-outlined">upload_file</span>IMPORTAR
                                                <!-- Input de archivo oculto -->
                                            </button>
                                            <button id="btnImportLoad" class="file-upload btn-import" style="display: none">
                                                <span id="spinner" class="spinner-border spinner-border-sm" role="status"
                                                    aria-hidden="true"></span> Cargando...
                                            </button>
                                            <input type="file" name="incentiveInputFile" id="incentiveInputFile"
                                                class="upload" style="display: none;" />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="select-wrapper" style="margin-left: 35px;">
                                            <button id="btnAddIncentive" type="button" class="btn-send">
                                                <span id="icon-send" class="material-symbols-outlined">add_card
                                                </span>CREAR

                                            </button>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="row col-md-12 mb-4 ">
                                <div class="row col-lg-8 ">
                                    <div class="form-group">
                                        <div class="select-wrapper">
                                            <h5>- <strong>Importar Incentivos</strong>,
                                                puede
                                                descargar la plantilla* <a style="color:var(--red-icot)"
                                                    href="{{ asset('assets/excel/plantilla_importar_incentivos.xls') }}"><strong>aquí</strong>
                                                    <span class="material-symbols-outlined"
                                                        style="vertical-align: middle;margin: 5px;">download_for_offline</span></a>
                                            </h5>
                                        </div>
                                    </div>
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
                        {{-- <th>Centro</th> --}}
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
                    <button type="button" id="close" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editIncentiveForm" method="POST" data-action-type="create"> <!-- Default set to create -->
                        @csrf
                        @method('POST')
                        <div class="mb-3">
                            <label for="name" class="form-label">Servicio</label>
                            <input type="string" class="form-control" id="name" name="name">
                        </div>
                        <div class="mb-3">

                            <label for="centre" class="form-label">Centro</label>
                            <div class="select-wrapper">
                                <span id="icon-select" class="icon-select material-symbols-outlined">
                                    business
                                </span>
                                <select multiple class="selectpicker" id="centre" name="centre[]" data-size="7"
                                    data-style="btn btn-red-icot btn-round" title="Centro" tabindex="-98">
                                    @if ($user->rol_id != 1)
                                        @foreach ($centres as $centre)
                                            @if ($centre->id == $user->centre_id)
                                                <option class="text-uppercase" value="{{ $centre->name }}" selected>
                                                    {{ $centre->name }}
                                                </option>
                                            @endif
                                        @endforeach
                                    @else
                                        @foreach ($centres as $centre)
                                            <option class="text-uppercase" value="{{ $centre->name }}">
                                                {{ $centre->name }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>

                        </div>
                        <div class="mb-3">
                            <label for="price" class="form-label">Precio</label>
                            <input type="number" class="form-control" id="price" name="price">
                        </div>
                        <div class="mb-3">
                            <label for="incentive_direct" class="form-label">Incentivo Directo</label>
                            <input type="number" class="form-control" id="incentive_direct" name="incentive_direct">
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
                    <div class="row mt-4 text-right">
                        <div class="col-12">
                            <button type="button" id="closeModal" class="icon-button close-btn" data-bs-dismiss="modal"
                                style="margin-right: 10px;" title="Cancelar">
                                <span class="material-symbols-outlined">arrow_back</span>
                            </button>
                            <button id="saveIncentiveBtn" type="submit" class="btn-save" title="Guardar">
                                <span class="material-symbols-outlined">save</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .highlight-row {
            color: green !important;
        }
    </style>
    <script type="text/javascript">
        let centres = @json($centres).reduce((obj, centre) => {
            obj[centre.name] = centre.name;
            return obj;
        }, {});
        $(document).ready(function() {
            getServiceIncentives();

            $('#editIncentiveModal form').validate({
                errorClass: 'error-message',
                errorElement: 'span',
                rules: {
                    price: {
                        required: true,
                        number: true,
                        min: 0
                    },
                    service_name: {
                        required: true,
                        string: true
                    },
                    service_price_direct_incentive: {
                        required: true,
                        number: true,
                        min: 0
                    },
                    service_price_incentive1: {
                        required: true,
                        number: true,
                        min: 0
                    },
                    service_price_incentive2: {
                        required: true,
                        number: true,
                        min: 0
                    },
                    service_price_super_incentive1: {
                        required: true,
                        number: true,
                        min: 0
                    },
                    service_price_super_incentive2: {
                        required: true,
                        number: true,
                        min: 0
                    }
                },
                messages: {
                    price: {
                        required: 'Por favor, ingrese el precio.',
                        number: 'Por favor, ingrese un número válido.',
                        min: 'El precio no puede ser negativo.'
                    },
                    service_name: {
                        required: 'Por favor, ingrese el nombre del servicio.'
                    },
                    service_price_direct_incentive: {
                        required: 'Por favor, ingrese el incentivo directo.',
                        number: 'Por favor, ingrese un número válido.',
                        min: 'El incentivo no puede ser negativo.'
                    },
                    service_price_incentive1: {
                        required: 'Por favor, ingrese el incentivo 1.',
                        number: 'Por favor, ingrese un número válido.',
                        min: 'El incentivo no puede ser negativo.'
                    },
                    service_price_incentive2: {
                        required: 'Por favor, ingrese el incentivo 2.',
                        number: 'Por favor, ingrese un número válido.',
                        min: 'El incentivo no puede ser negativo.'
                    },
                    service_price_super_incentive1: {
                        required: 'Por favor, ingrese el super incentivo 1.',
                        number: 'Por favor, ingrese un número válido.',
                        min: 'El super incentivo no puede ser negativo.'
                    },
                    service_price_super_incentive2: {
                        required: 'Por favor, ingrese el super incentivo 2.',
                        number: 'Por favor, ingrese un número válido.',
                        min: 'El super incentivo no puede ser negativo.'
                    }
                },
                errorPlacement: function(error, element) {
                    error.insertAfter(element);
                },
                highlight: function(element) {
                    $(element).addClass('input-error');
                },
                unhighlight: function(element) {
                    $(element).removeClass('input-error');
                }
            });
        });

        function confirmRequest(state, id) {
            confirmedRequest().then((result) => {
                if (result.isConfirmed) {
                    destroyIncentive(id);
                }
            });
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
                        table.ajax.reload();
                        showAlert('success', 'Incentivo Eliminado Correctamente');
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
                showAlert('error', errorThrown || 'error cargando servicios');
            });
        }

        function downloadTemplate() {
            const link = document.createElement('a');
            link.href = asset('assets/excel/plantilla_importar_incentivos.xls');
            link.download = 'plantilla_incentivos.xls';
            link.click();
        }

        var table;

        var columnsFilled = [];
        columnsFilled.push({
            data: 'service',
            name: 'name'
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
                            targets: [1, 2, 3, 4, 5, 6, 7],
                            visible: true,
                            className: 'dt-body-center'
                        },
                        {
                            targets: [1, 2, 3, 4, 5, 6],
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

        $(function() {
            $(document).ready(function() {

                setupFormChangeDetection('#editIncentiveForm', '#saveIncentiveBtn', '#editIncentiveModal');
            });
            $(".nav-item").each(function() {
                $(this).removeClass("active");
            });
            $('#pagesTracking').addClass('show');
            $('#adminServiceIncentive').addClass('active');

            function setModalValues({
                name,
                centre,
                price,
                incentiveDirect,
                incentiveObj1,
                incentiveObj2,
                bonusObj1,
                bonusObj2,
                actionType,
                readonlyFields = []
            }) {
                $('#editIncentiveModal #name').val(name).prop('readonly', readonlyFields.includes('name'));
                $('#editIncentiveModal #centre').val(centre).prop('readonly', readonlyFields.includes('centre'));
                $('#editIncentiveModal #price').val(price).prop('readonly', readonlyFields.includes('price'));
                $('#editIncentiveModal #incentive_direct').val(incentiveDirect).prop('readonly', readonlyFields
                    .includes('incentive_direct'));
                $('#editIncentiveModal #incentive_obj1').val(incentiveObj1).prop('readonly', readonlyFields
                    .includes('incentive_obj1'));
                $('#editIncentiveModal #incentive_obj2').val(incentiveObj2).prop('readonly', readonlyFields
                    .includes('incentive_obj2'));
                $('#editIncentiveModal #bonus_obj1').val(bonusObj1).prop('readonly', readonlyFields.includes(
                    'bonus_obj1'));
                $('#editIncentiveModal #bonus_obj2').val(bonusObj2).prop('readonly', readonlyFields.includes(
                    'bonus_obj2'));

                $('#editIncentiveModal').find('form').attr('data-action-type', actionType);
                $('#editIncentiveModalLabel').text(actionType === 'create' ? 'Crear Nuevo Incentivo' :
                    'Editar Incentivo');
                $('#editIncentiveModal').modal('show');
            }

            $(document).on('click', '.btn-edit', function() {
                const serviceId = $(this).data('id');
                const serviceName = $(this).data('name');
                const centreName = $(this).data('centre');
                const servicePrice = $(this).data('price');
                const incentiveDirect = $(this).data('direct-incentive');
                const incentiveObj1 = $(this).data('obj1');
                const incentiveObj2 = $(this).data('obj2');
                const bonusObj1 = $(this).data('bonus1');
                const bonusObj2 = $(this).data('bonus2');

                setModalValues({
                    name: serviceName,
                    centre: centreName,
                    price: servicePrice,
                    incentiveDirect,
                    incentiveObj1,
                    incentiveObj2,
                    bonusObj1,
                    bonusObj2,
                    actionType: 'edit',
                    readonlyFields: ['name', 'centre']
                });
                $('#saveIncentiveBtn').data('id', serviceId);
            });

            $(document).on('click', '.btn-see', function() {
                const serviceName = $(this).data('name');

                $.ajax({
                    url: `/service/centres`,
                    method: 'GET',
                    data: {
                        name: serviceName
                    },
                    beforeSend: function() {
                        // Deshabilitamos el evento global temporalmente
                        $(document).off('ajaxStop');
                    },
                    success: function(response) {
                        showListAlert(
                            `Centros para ${serviceName}`,
                            response.centres,
                            `No hay centros disponibles para ${serviceName}.`
                        );
                    },
                    error: function() {
                        showListAlert("Error", [], "Hubo un problema al obtener los centros.");
                    },
                    complete: function() {
                        // Rehabilitamos el evento global solo después de que el usuario cierre el alerta
                        // $(document).on('ajaxStop', function() {
                        //     Swal.close();
                        // });
                    }
                });
            });


            $(document).on('click', '#btn-repeat', function() {
                var serviceData = {
                    servicePriceId: $(this).data('id'),
                    service_name: $(this).data('name'),
                    price: $(this).data('price'),
                    service_price_direct_incentive: $(this).data('direct-incentive'),
                    service_price_incentive1: $(this).data('obj1'),
                    service_price_incentive2: $(this).data('obj2'),
                    service_price_super_incentive1: $(this).data('bonus1'),
                    service_price_super_incentive2: $(this).data('bonus2'),

                }
                confirmAddToCentre(serviceData);
            });

            $('#btnAddIncentive').on('click', function() {
                $('#editIncentiveModal').find('form')[0].reset();
                setModalValues({
                    name: '',
                    centre: '',
                    price: '',
                    incentiveDirect: '',
                    incentiveObj1: '',
                    incentiveObj2: '',
                    bonusObj1: '',
                    bonusObj2: '',
                    actionType: 'create',
                    readonlyFields: []
                });
                $('#saveIncentiveBtn').data('id', null);
            });

            // TODO botones amostrar y ocultar
            document.getElementById('btnImportIncentives').addEventListener('click', function(event) {
                event.preventDefault();
                confirmAdvice('Confirmación',
                        'Tenga en cuenta que esta operación elimina los incentivos anteriores¿Continuar?')
                    .then((result) => {
                        if (result.isConfirmed) {
                            document.getElementById('incentiveInputFile').click();

                        }
                    });
            });

            $('#closeModal').on('click', function() {
                $('#editIncentiveModal').modal('hide');
            });

            $('#close').on('click', function() {
                $('#editIncentiveModal').modal('hide');
            });

            function saveIncentive(data, actionType) {
                console.log(data);
                let url = actionType === 'edit' ? '/incentives/edit' : '/incentives/create';
                let method = 'POST';

                $.ajax({
                    url: url,
                    method: method,
                    data: data,
                    success: function(response) {
                        console.log(response);
                        showAlert('success', response.message || (actionType === 'edit' ?
                            'Incentivo Actualizado Correctamente' :
                            'Incentivo Creado Correctamente'));
                        $('#editIncentiveModal').modal('hide');
                        var table = $('.services-datatable').DataTable();
                        table.ajax.reload();
                    },
                    complete: function() {
                        var table = $('.services-datatable').DataTable();
                        table.ajax.reload();
                    },
                    error: function(error) {
                        showAlert('error', error.responseJSON?.message || 'Incentivo No ' +
                            (actionType === 'edit' ? 'Actualizado' : 'Creado') + ' Correctamente');
                    }
                });
            }

            function confirmAddToCentre(data) {
                confirmWithMultiSelect('Añadir a Centro', centres).then((centre) => {
                    console.log(data, centre);
                    if (centre) {
                        data._token = '{{ csrf_token() }}';
                        data.centre_name = centre;
                        saveIncentive(data, 'create');
                    } else {
                        console.log("Selección cancelada");
                    }
                });

            }

            $('#saveIncentiveBtn').on('click', function() {
                if ($('#editIncentiveModal form').valid()) {
                    var actionType = $('#editIncentiveModal').find('form').attr('data-action-type');
                    var serviceId = actionType === 'edit' ? $(this).data('id') : null;

                    var data = {
                        _token: '{{ csrf_token() }}',
                        price: $('#price').val(),
                        service_name: $('#name').val(),
                        service_price_direct_incentive: $('#incentive_direct').val(),
                        service_price_incentive1: $('#incentive_obj1').val(),
                        service_price_incentive2: $('#incentive_obj2').val(),
                        service_price_super_incentive1: $('#bonus_obj1').val(),
                        service_price_super_incentive2: $('#bonus_obj2').val(),
                    };

                    if (actionType === 'edit') {
                        data.service_id = serviceId;
                    } else {
                        data.centre_name = $('#centre').val() || [];
                    }

                    saveIncentive(data, actionType);
                } else {
                    showAlert('error', 'Por favor, complete todos los campos requeridos.');
                }
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

            $("#incentiveInputFile").on('change', function() {
                handleFileChange("{{ route('incentives.import') }}");
            });

            async function handleFileChange(formAction) {
                $('#btnImportIncentives').hide();
                $('#btnImportLoad').show();
                var table = $('.services-datatable').DataTable();
                event.preventDefault();
                $("#incentivesForm").attr('action', formAction);
                $("#incentivesForm").attr('enctype', "multipart/form-data");

                const formData = new FormData($("#incentivesForm")[0]);
                try {
                    const response = await $.ajax({
                        url: formAction,
                        method: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            setTimeout(() => showAlert('success', response
                                .message || 'Cargado Correctamente'), 8000);
                            $('#btnImportIncentives').show();
                            $('#btnImportLoad').hide();
                            table.ajax.reload();

                        },
                        complete: function() {
                            $('#btnImportIncentives').show();
                            $('#btnImportLoad').hide();
                            table.ajax.reload();

                        },
                        error: function(error) {
                            setTimeout(() => showAlert('error', error.responseJSON
                                    ?.message ||
                                    'Ha habido un error en la importación'),
                                8000);

                            $('#btnImportIncentives').show();
                            $('#btnImportLoad').hide();

                        }
                    });
                    return response;
                } catch (error) {
                    console.error("Error al Importar:", error);
                    throw new Error(error.statusText || "Error en la solicitud");
                } finally {
                    $('#btnImportIncentives').show();
                    $('#btnImportLoad').hide();
                    $('#incentivesForm')[0].reset();
                }
            }



        });
    </script>
@endsection
