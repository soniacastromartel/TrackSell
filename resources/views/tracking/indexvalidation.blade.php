<link rel="stylesheet"
    href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />

@extends('layouts.logged')
@section('content')
    @include('inc.navbar')

    <link rel="stylesheet" href="{{ asset('/css/tracking.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/buttons.css') }}">

    <div class="content" style="padding-top: 120px">
        <div class="container-fluid">
            <form id="finalValidationForm" method="POST">
                @csrf
                @method('POST')
                {{-- Card 2 --}}
                <div class="card card-banner ">
                    <div class="card-header card-header-danger">
                        <h4 class="card-title">Validar RRHH</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            {{-- Left Side - col-lg-10 --}}
                            <div class="col-lg-8">
                                <div class="informes-container">
                                    <div class="date-informes-container">
                                        <label class="col-form-label-lg" for="dateFrom">Fecha</label>
                                        <div id="monthYearPickerContainer" style="margin-bottom:15px;">
                                            <input id="monthYearPicker" type="text" placeholder="yyyy/mm">
                                            <span id="icon-date" class="icon-select material-symbols-outlined">
                                                calendar_month
                                            </span>
                                            <input type="hidden" name="monthYear" id="monthYear" />
                                        </div>

                                        <button id="btnClear" href="#" class="btn-refresh" style="margin-top:30px;">
                                            <span id="icon-refresh" class="material-symbols-outlined">refresh</span>
                                            {{ __('RECARGAR') }}
                                        </button>
                                    </div>

                                    <div id="picker-btn-container" class="picker-btn-container">
                                        <div id="picker-container" class="picker-container">
                                            <label class="col-form-label-md" for="actions" style="">1.- CARGA DE
                                                DATOS
                                            </label>
                                            <button id="btnCalculate" type="button" class="btn-calculate">
                                                <span id="icon-calculate" class="material-symbols-outlined">calculate</span>
                                                {{ __('CALCULAR') }}
                                            </button>
                                            <button id="btnCalculateLoad" type="button" class="btn-calculate"
                                                style="display: none">
                                                <span class="spinner-border spinner-border-sm" role="status"
                                                    aria-hidden="true"></span>
                                            </button>
                                            <label class="col-form-label-md" for="dateFrom" style="margin-top:30px;">2.
                                                SELECCIÓN DE SOCIEDAD</label>
                                            <div class="select-wrapper">
                                                <span id="icon-select" class="icon-select material-symbols-outlined">
                                                    business
                                                </span>
                                                <select class="selectpicker" name="business_id" id="business_id"
                                                    data-size="7" data-style="btn btn-red-icot" title="Empresa"
                                                    tabindex="-98">
                                                    <option value="">SIN CODIGO </option>
                                                    @foreach ($a3business as $a3business)
                                                        <option value="{{ $a3business->code_business }}">
                                                            {{ $a3business->code_business . '-' . $a3business->name_business }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <label class="col-form-label-md" for="dateFrom" style="margin-top:30px;">3.
                                                PAGAR TODOS</label>
                                            <button id="btnValidate" type="button" class="btn-pay-all">
                                                <span id="icon-pay" class="material-symbols-outlined">paid</span>
                                                {{ __('PAGAR') }}
                                            </button>
                                            <button id="btnValidateLoad" type="submit" class="btn btn-success"
                                                style="display: none;margin-top:30px;">
                                                <span class="spinner-border spinner-border-sm" role="status"
                                                    aria-hidden="true"></span>
                                            </button>
                                            <label class="col-form-label-lg" for="dateFrom"
                                                style="margin-top:30px;display:none">3. DESHACER PAGAR</label>
                                            <button id="btnUnvalidate" type="button" class="btn-deshacer-pagar"
                                                style="margin-top:30px;">
                                                <span id="icon-deshacer" class="material-symbols-outlined">undo</span>
                                                {{ __('DESHACER') }}
                                            </button>
                                            <button id="btnUnvalidateLoad" type="submit" class="btn btn-red-icot"
                                                style="display: none;margin-top:30px;">
                                                <span class="spinner-border spinner-border-sm" role="status"
                                                    aria-hidden="true"></span>
                                            </button>

                                            <label class="col-form-label-md" for="dateFrom" style="margin-top:30px;">4.
                                                EXPORTAR DOCUMENTO *</label>
                                            <button id="btnExport" type="button" class="btn-export"
                                                style="margin-top:30px;">
                                                <span id="icon-export"
                                                    class="material-symbols-outlined">file_download</span>
                                                {{ __('EXPORTAR') }}
                                            </button>
                                            <button id="btnExportLoad" type="submit" class="btn-export"
                                                style="display: none;margin-top:30px;">
                                                <span class="spinner-border spinner-border-sm" role="status"
                                                    aria-hidden="true"></span>
                                            </button>
                                            <P>* Se exportan los que se han pagado hoy</P>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
        </div>

        {{-- Table --}}
        <table class="table-striped table-bordered tracking-validation-datatable table" style="width:100%;">
            <thead class="table-header">
                <tr>
                    <th>C.Empresa</th>
                    <th>C.Empleado</th>
                    <th>NIF</th>
                    <th>Centro</th>
                    <th>Empleado</th>
                    <th>Fecha Baja</th>
                    <th>Total Ingreso</th>
                    <th>Pagado</th>
                </tr>
            </thead>
        </table>
        </form>
    </div>
    </div>
    <script type="text/javascript">
        var table;
        $(function() {
            $(".nav-item").each(function() {
                $(this).removeClass("active");
            });
            $('#pagesRRHH').addClass('show');
            $('#trackingValidateFinal').addClass('active');

            $('#btnUnvalidate').hide();

            var columnsFilled = [];
            columnsFilled.push({
                data: 'cod_business',
                name: 'cod_business'
            });
            columnsFilled.push({
                data: 'cod_employee',
                name: 'cod_employee'
            });
            columnsFilled.push({
                data: 'dni',
                name: 'dni'
            });
            columnsFilled.push({
                data: 'centre',
                name: 'centre'
            });
            columnsFilled.push({
                data: 'name',
                name: 'name'
            });
            columnsFilled.push({
                data: 'cancellation_date',
                name: 'cancellation_date'
            });
            columnsFilled.push({
                data: 'total_income',
                name: 'total_income'
            });
            columnsFilled.push({
                data: 'action',
                name: 'action',
                width: 300
            });
            $.fn.dataTable.ext.errMode = 'none';

            var date = new Date();
            var textMonthYear = setDate(date);
            $('#monthYearPicker').val(textMonthYear);

            $('#monthYearPicker').MonthPicker({
                ShowIcon: false
            });

            //ACCION SELECCIONAR EMPRESA
            $('#business_id').change(function() {
                getEmployeeIncentives(columnsFilled);
            });

            // Accion PAGAR TODOS
            $("#btnValidate").on('click', function(e) {
                $('#alertTrackingDate').hide();
                $('#alertErrorTrackingDate').hide();
                params = {};
                params["_token"] = "{{ csrf_token() }}";
                params["monthYear"] = $('#monthYearPicker').val();
                params["cod_business"] = $("#business_id option:selected").val();
                params["business"] = $("#business_id option:selected").text();
                if (table == undefined) {
                    showAlert('info', 'No hay Datos Seleccionados');
                    return;
                }
                var validateData = table.ajax.json();
                if (validateData['recordsTotal'] == 0) {
                    showAlert('info', 'No hay Datos Seleccionados');
                    return;
                }
                $('#btnValidateLoad').show();
                $('#btnValidate').hide();
                $.ajax({
                    url: "{{ route('tracking.validateTrackings') }}",
                    type: 'post',
                    data: params,
                    success: function(data, textStatus, jqXHR) {
                        if (textStatus === 'success') {
                            $('#btnValidateLoad').hide();
                            $('#btnValidate').show();
                            showAlert('success', 'Pagado Correctamente');
                            $('.tracking-validation-datatable').DataTable().ajax.reload();
                        }
                    },
                    error: function(xhr, status, error) {
                        showAlert('error', error);
                        $('#btnValidateLoad').hide();
                    }
                }).fail(function(jqXHR, textStatus, errorThrown) {
                    showAlert('error', jqXHR.responseText);
                }).done(function() {
                    $('#btnUnvalidate').show();
                    $('#btnValidateLoad').hide();
                    $('#btnValidate').hide();
                    showAlert('success', 'Realizado Correctamente');
                });
            });

            // Accion DESHACER PAGAR TODOS
            $("#btnUnvalidate").on('click', function(e) {
                $('#alertTrackingDate').hide();
                $('#alertErrorTrackingDate').hide();
                params = {};
                params["_token"] = "{{ csrf_token() }}";
                params["monthYear"] = $('#monthYearPicker').val();
                params["cod_business"] = $("#business_id option:selected").val();
                params["business"] = $("#business_id option:selected").text();

                if (table == undefined) {
                    showAlert('info', 'No hay Datos Seleccionados');
                    return;
                }
                var validateData = table.ajax.json();
                if (validateData['recordsTotal'] == 0) {
                    showAlert('info', 'No hay Datos Seleccionados');
                    return;
                }
                $('#btnUnvalidateLoad').show();
                $('#btnUnvalidate').hide();

                $.ajax({
                    url: "{{ route('tracking.unvalidateTrackings') }}",
                    type: 'post',
                    data: params,
                    success: function(data, textStatus, jqXHR) {
                        if (textStatus === 'success') {
                            $('#btnUnvalidateLoad').hide();
                            $('#btnUnvalidate').show();
                            showAlert('success', 'Realizado Correctamente');
                            $('.tracking-validation-datatable').DataTable().ajax.reload();
                        }
                    },
                    error: function(xhr, status, error) {
                        showAlert('error', error);
                        $('#btnUnvalidateLoad').hide();
                    }

                }).fail(function(jqXHR, textStatus, errorThrown) {
                    showAlert('error', jqXHR.responseText);
                }).done(function() {
                    $('#btnValidate').show();
                    $('#btnUnvalidate').hide();
                    showAlert('success', 'Realizado Correctamente');
                });
            });

            // Accion CALCULAR
            $("#btnCalculate").on('click', function(e) {
                params = {};
                params["_token"] = "{{ csrf_token() }}";
                params["monthYear"] = $('#monthYearPicker').val();
                params["_token"] = "{{ csrf_token() }}";
                params["search"] = $('input[type="search"]').val();
                params["codbusiness"] = $("#business_id option:selected").val();
                $('#btnCalculate').hide();
                $('#btnCalculateLoad').show();
                $.ajax({
                    url: "{{ route('tracking.calculateValidationRRHH') }}",
                    type: 'post',
                    data: params,
                    success: function(response, textStatus, jqXHR) {
                        showAlert('success', 'Datos Calculados');
                        $('#btnCalculate').show();
                        $('#btnCalculateLoad').hide();
                        getEmployeeIncentives(columnsFilled);
                    },
                    error: function(xhr, status, error) {
                        showAlert('error', error);
                        $('#btnCalculate').show();
                        $('#btnCalculateLoad').hide();
                    }

                }).fail(function(jqXHR, textStatus, errorThrown) {
                    showAlert('error', jqXHR.responseText);
                }).done(function() {
                    showAlert('success', 'Datos Calculados');
                });
            });

            // Accion BUSCAR
            $("#btnSubmit").on('click', function(e) {
                e.preventDefault();
                var codBusiness = $("#business_id option:selected").val();
                if (codBusiness == "") {
                    showToast('warning', '¡Indique una empresa!');
                    return false;
                }
                $('#btnSubmit').hide();
                $('#btnSubmitLoad').show();
                $('#btnSubmitLoad').prop('disabled', true);
                $('#monthYear').val($("#monthYearPicker").val());
                getEmployeeIncentives(columnsFilled);
            });

            // Accion EXPORTAR
            $("#btnExport").on('click', function(e) {
                exportData(columnsFilled);
                $('#btnExport').hide();
                $('#btnExportLoad').show();
                $("#btnValidate").show();
                // $("#btnUnvalidate").hide();
                $('#btnExportLoad').prop('disabled', true);
            });

            $("#btnClear").on('click', function(e) {
                e.preventDefault();
                clearForms();
            });
        });

        function getEmployeeIncentives(columnsFilled) {
            if ($.fn.dataTable.isDataTable('.tracking-validation-datatable')) {
                $('.tracking-validation-datatable').DataTable().ajax.reload();
            }
            table = $('.tracking-validation-datatable').DataTable({
                responsive: true,
                ordering: false,
                processing: true,
                serverSide: true,
                language: {
                    "url": "{{ asset('dataTables/Spanish.json') }}"
                },
                ajax: {
                    url: '{{ route('tracking.index_validation_final') }}',
                    type: "POST",
                    data: function(d) {
                        d.monthYear = $('#monthYearPicker').val(),
                            d._token = "{{ csrf_token() }}",
                            d.search = $('input[type="search"]').val()
                        d.codbusiness = $("#business_id option:selected").val()
                    },
                    dataSrc: function(json) {
                        $('#btnSubmit').show();
                        $('#btnSubmitLoad').hide();
                        return json.data;
                    }
                },
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

        function clearForms() {
            var date = new Date();
            var textMonthYear = setDate(date);
            $('#monthYearPicker').val(textMonthYear);
            $('select#business_id').val('');
            $('select#business_id').selectpicker("refresh");
            $('.tracking-validation-datatable').DataTable().ajax.reload();
        }

        /**
         * FUNCION PAGAR INDIVIDUAL
         */
        function updateValidation(employeeId, trackingIds, totalIncome, supervisor, back) {
            $('#alertErrorTrackingDate').hide();
            $('#alertTrackingDate').hide();
            var trackingDate = $("#tracking_date_" + employeeId).val();
            state = 'paid';
            params = {};
            params["_token"] = "{{ csrf_token() }}";
            params["employee_id"] = employeeId;
            params["trackingDate"] = trackingDate;
            params["totalIncome"] = totalIncome;
            params["supervisor"] = supervisor;
            params["trackingIds"] = trackingIds;
            params["back"] = back;
            params["monthYear"] = $('#monthYearPicker').val();

            $.ajax({
                url: "{{ route('tracking.updatePaidState') }}",
                type: 'post',
                data: params,
                success: function(response, textStatus, jqXHR) {

                    if (textStatus === 'success') {
                        showAlert('success', response.mensaje);
                        $('.tracking-validation-datatable').DataTable().ajax.reload();
                    }
                },
                error: function(xhr, status, error) {
                    var response = JSON.parse(xhr.responseText);
                    //alert(response.errors); 
                    window.location = response.url;
                    showAlert('error', xhr.responseText);
                    $('#btnSubmitLoad').hide();
                    $('#btnSubmit').show();
                }

            }).fail(function(jqXHR, textStatus, errorThrown) {
                showAlert('error', jqXHR.responseText);
            }).done(function() {
                showAlert('info', 'Realizado Correctamente')
                $('#btnExport').show();
                $('#btnExportLoad').hide();
            });
        }

        /*
         *Función para EXPORTAR los datos
         */
        function exportData(columnsFilled) {
            params = {};
            params["monthYear"] = $('#monthYearPicker').val();
            params["cod_business"] = $("#business_id option:selected").val();
            params["_token"] = "{{ csrf_token() }}";
            params["business"] = $("#business_id option:selected").text();

            $.ajax({
                url: '{{ route('tracking.exportFinalValidation') }}',
                type: 'post',
                data: params,
                xhrFields: {
                    'responseType': 'blob'
                },
                success: function(data, textStatus, jqXHR) {
                    if (textStatus === 'success') {
                        showToast('info', 'Exportando...')
                        $('#btnSubmitLoad').hide();
                        $('#btnSubmit').show();

                        var link = document.createElement('a'),
                            filename = 'export_incentives.xls';
                        link.href = URL.createObjectURL(data);
                        link.download = filename;
                        link.click();
                    }
                },
                error: function(xhr, status, error) {
                    console.log('error ' + error);
                    showAlert('error', error);
                    $('#btnSubmitLoad').hide();
                    $('#btnSubmit').show();
                }
            }).fail(function(jqXHR, textStatus, errorThrown) {
                showAlert('error', jqXHR.responseText);
            }).done(function() {
                showAlert('info', 'Realizado Correctamente')
                $('#btnExport').show();
                $('#btnExportLoad').hide();
                setTimeout(function() {
                    location.reload();
                }, 1500);
            });
        }

        function setDate($date) {
            date = new Date();
            year = date.getFullYear();
            month = date.getMonth() + 1;
            textMonthYear = month >= 10 ? month : '0' + month;
            fecha = textMonthYear + '/' + year;
            return fecha;
        }
    </script>
    <style>
        .card-banner {
            background-image: url(/assets/img/banners/6.jpg);
            background-repeat: no-repeat;
            background-size: contain;
            background-position-x: right;
            min-height: 320px;
            min-width: fit-content;
        }

        /* Mejora del diseño general */
        .custom-card {
            border-radius: 12px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            background: rgb(241, 240, 240);
            padding: 15px;
            max-width: 800px;
            margin: auto;
        }

        /* Estilo de la cabecera */
        .custom-card-header {
            background: var(--red-icot);
            color: whitesmoke;
            font-weight: bold;
            font-size: 18px;
            border-radius: 8px 8px 0 0;
            padding: 12px;
        }

        /* Espaciado del cuerpo */
        .custom-body {
            padding: 20px;
        }

        /* Ítems de la lista */
        .custom-item {
            background: white;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            transition: 0.3s ease-in-out;
        }

        /* Hover para mejor interactividad */
        .custom-item:hover {
            background: #eef2ff;
            transform: scale(1.02);
        }

        p {
            margin: 5px 0 0;
            font-size: 14px;
            color: #555;
        }
    </style>
@endsection
