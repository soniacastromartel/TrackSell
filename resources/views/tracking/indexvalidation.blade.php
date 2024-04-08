<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />

@extends('layouts.logged')
@section('content')
@include('inc.navbar')
@include('common.alert')

<link rel="stylesheet" href="{{ asset('/css/tracking.css') }}">
<link rel="stylesheet" href="{{ asset('/css/buttons.css') }}">
<link rel="stylesheet" href="{{ asset('/css/dashboard.css') }}">

<div id="alertErrorTrackingDate" class="alert alert-danger" role="alert" style="display: none">
</div>
<div id="alertTrackingDate" class="alert alert-success" role="alert" style="display: none">
</div>

<div class="content">
    <div class="container-fluid">
        <form id="finalValidationForm" method="POST">
            @csrf
            @method('POST')
            <div class="row">
                <div class="col-lg-8 p-0"   >
                    <div class="col-lg-9 ">
                        <div class="card card-info text-white ml-4 mt-0">
                            <div class="card-header">
                                <i class="material-icons" style="color: var(--red-icot);">info</i>
                                <span style="font-size:16px; vertical-align:super; font-weight:bold; color: var(--red-icot)">Funcionamiento</span>
                            </div>
                            <div class="card-body py-0 mb-3">
                                <div class="row" >
                                    <div class="col-md-3">
                                        <h5 class="card-title" style="font-size:18px !important;">Calcular</h5>
                                    </div>
                                    <div class="col-md-9">
                                        <h5 class="card-title" style="font-size:18px !important;">- Seleccionar mes / año para primera carga de datos</h5>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <h5 class="card-title" style="font-size:18px !important;">Buscar</h5>
                                    </div>
                                    <div class="col-md-9">
                                        <h5 class="card-title" style="font-size:18px !important;">- Seleccionar empresa y obtener datos</h5>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <h5 class="card-title" style="font-size:18px !important;">Pagar todos</h5>
                                    </div>
                                    <div class="col-md-9">
                                        <h5 class="card-title" style="font-size:18px !important;">- Acción de pagar todos los del mes seleccionado </h5>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <h5 class="card-title" style="font-size:18px !important;">Exportar</h5>
                                    </div>
                                    <div class="col-md-9">
                                        <h5 class="card-title" style="font-size:18px !important;">- Coge los que están pagados en día actual</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header card-header-danger">
                    <h4 class="card-title">Seguimiento</h4>
                </div>
                <div class="card-container" >

                    <div class="col-md-6" style="padding:20px;" >

                        <div class="row" style=" padding:20px;">

                            <div class="col-md-4">

                                <select class="selectpicker" name="business_id" id="business_id" data-size="7" data-style="btn btn-red-icot" title="Empresa" tabindex="-98">
                                    <option value="">SIN CODIGO </option>
                                    @foreach ($a3business as $a3business)
                                    <option value="{{$a3business->code_business}}">{{ $a3business->code_business .'-'. $a3business->name_business}}</option>
                                    @endforeach
                                </select>
                            </div>

                                <div id="monthYearPickerContainer">
                                <input id="monthYearPicker"  type="text" placeholder="yyyy/mm" />
                                <span id="icon-date" class="material-symbols-outlined"> calendar_month</span>
                                <input type="hidden" name="monthYear" id="monthYear" />
                            </div>


                        </div>

                        <div class="row" style=" padding:20px;">

                            <div class="btn-container" style= "">

                            <button id="btnClear" href="#" class="btn-refresh" >
                                <span id="icon-refresh" class="material-icons">refresh</span>
                                {{ __('Limpiar formulario') }}
                            </button>
                            <button id="btnCalculate" type="button" class="btn-calculate" >
                            <span id="icon-calculate" class="material-icons">calculate</span>{{ __('Calcular') }}</button>
                            <button id="btnCalculateLoad" type="button" class="btn-calculate" style="display: none">
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                          
                            </button>
                            <button id="btnSubmit" type="submit" class="btn-search">
                            <span id="icon-search" class="material-icons">search</span>{{ __('Buscar') }}</button>
                            <button id="btnSubmitLoad" type="submit" class="btn btn-success" style="display: none">
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                              
                            </button>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="row" style=" padding:20px;">
                        <div class="btn-container" style= "width:100%;display:flex; justify-content:flex-end;">
                        <button id="btnExport" type="button" class="btn-export">
                            <span id="icon-export" class="material-icons">file_download</span>{{ __('Exportar') }}</button>
                            <button id="btnExportLoad" type="submit" class="btn-export" style="display: none">
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                          
                            </button>
                            <button id="btnValidate" type="button" class="btn-pay-all">
                            <span id="icon-pay" class="material-icons">paid</span>{{ __('Pagar todos') }}</button>
                            <button id="btnValidateLoad" type="submit" class="btn btn-success" style="display: none">
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            
                            </button>
                            <button id="btnUnvalidate" type="button" class="btn-deshacer-pagar">
                            <span id="icon-deshacer" class="material-icons">restart_alt</span>{{ __('Deshacer Pagar todos') }}</button>
                            <button id="btnUnvalidateLoad" type="submit" class="btn btn-red-icot" style="display: none">
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        
                            </button>
                        </div>
                    </div>
                        
                </div>
                
                </div>
            </div>
        </form>
        <table class="table  table-striped table-bordered tracking-validation-datatable" style="width:100%;">
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
            <tbody>
            </tbody>
        </table>
    </div>
</div>

<script type="text/javascript">
    var table;
    $(function() {

        $(".nav-item").each(function() {
            $(this).removeClass("active");
        });
        $('#pagesTracking').addClass('show');
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
        var textMonthYear =  setDate(date);
        $('#monthYearPicker').val(textMonthYear);
    
        $('#monthYearPicker').MonthPicker({
                ShowIcon: false
            });

        function getEmployeeIncentives() {
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
                    url: '{{ route("tracking.index_validation_final") }}',
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
           // table.columns.adjust().draw();
        }

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
                timeOutAlert($('#alertErrorTrackingDate'),"No hay datos seleccionados");
                return;
            }
            var validateData = table.ajax.json();
            if (validateData['recordsTotal'] == 0) {
                timeOutAlert($('#alertErrorTrackingDate'),"No hay datos seleccionados");
                return;
            }
            $('#btnValidateLoad').show();
            $('#btnValidate').hide();

            $.ajax({
                url: "{{ route('tracking.validateTrackings') }}",
                type: 'post',
                data: params,
                success: function(data, textStatus, jqXHR) {

                    // if success, HTML response is expected, so replace current
                    if (textStatus === 'success') {
                        $('#btnValidateLoad').hide();
                        $('#btnValidate').show();
                        timeOutAlert($('#alertTrackingDate'), "Pagado correctamente")
                        $('.tracking-validation-datatable').DataTable().ajax.reload();
                    }
                },
                error: function(xhr, status, error) {
                    //$('#btnCalculate').show();
                    timeOutAlert($('#alertErrorTrackingDate'),"Error al validar")
                    $('#btnValidateLoad').hide();
                }
            }).fail(function(jqXHR, textStatus, errorThrown) {
                timeOutAlert($('#alertErrorTrackingDate'),jqXHR.responseText)
            }).done(function() {
                $('#btnUnvalidate').show();
                $('#btnValidateLoad').hide();
                $('#btnValidate').hide();
                timeOutAlert($('#alertTrackingDate'));
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
                timeOutAlert($('#alertErrorTrackingDate'),"No hay datos seleccionados");
                return;
            }
            var validateData = table.ajax.json();
            if (validateData['recordsTotal'] == 0) {
                timeOutAlert($('#alertErrorTrackingDate'),"No hay datos seleccionados");
                return;
            }
            $('#btnUnvalidateLoad').show();
            $('#btnUnvalidate').hide();

            $.ajax({
                url: "{{ route('tracking.unvalidateTrackings') }}",
                type: 'post',
                data: params,
                success: function(data, textStatus, jqXHR) {

                    // if success, HTML response is expected, so replace current
                    if (textStatus === 'success') {
                        $('#btnUnvalidateLoad').hide();
                        $('#btnUnvalidate').show();
                        timeOutAlert($('#alertTrackingDate'), "Realizado correctamente")
                        $('.tracking-validation-datatable').DataTable().ajax.reload();
                    }
                },
                error: function(xhr, status, error) {
                    //alert('OK calculados incentivos');
                    //$('#btnCalculate').show();
                    timeOutAlert($('#alertErrorTrackingDate'),"Error al validar")
                    $('#btnUnvalidateLoad').hide();
                }

            }).fail(function(jqXHR, textStatus, errorThrown) {
                alert('Error'+jqXHR.responseText);
            }).done(function() {
                $('#btnValidate').show();
                $('#btnUnvalidate').hide();
                timeOutAlert($('#alertTrackingDate'), "Realizado correctamente");
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
            $('#alertTrackingDate').hide();
            $('#alertErrorTrackingDate').hide();
            $('#btnCalculate').hide();
            $('#btnCalculateLoad').show();
            $.ajax({
                url: "{{ route('tracking.calculateValidationRRHH') }}",
                type: 'post',
                data: params,
                success: function(response, textStatus, jqXHR) {
                    console.log($("#business_id option:selected").val());
                    console.log('holi');
                    // if success, HTML response is expected, so replace current
                    timeOutAlert($('#alertTrackingDate'), "Datos Calculados");
                    $('#btnCalculate').show();
                    $('#btnCalculateLoad').hide();
                    getEmployeeIncentives();
                },
                error: function(xhr, status, error) {
                    //alert('OK calculados incentivos');
                    $('#btnCalculate').show();
                    $('#btnCalculateLoad').hide();
                }

            }).fail(function(jqXHR, textStatus, errorThrown) {
                timeOutAlert($('#alertErrorTrackingDate'), jqXHR.responseText);
            }).done(function() {
                console.log($("#business_id option:selected").val());
                    console.log('holi');
                timeOutAlert($('#alertTrackingDate'), "Datos Calculados");
            });
        });

        // Accion BUSCAR
        $("#btnSubmit").on('click', function(e) {
            $('#alertTrackingDate').hide();
            $('#alertErrorTrackingDate').hide();
            e.preventDefault();
            $('#alertErrorTrackingDate').hide();

            var codBusiness = $("#business_id option:selected").val();
            if (codBusiness == "") {
                timeOutAlert($('#alertErrorTrackingDate'),"Indique una empresa")
                return false;
            }
            $('#btnSubmit').hide();
            $('#btnSubmitLoad').show();
            $('#btnSubmitLoad').prop('disabled', true);
            $('#monthYear').val($("#monthYearPicker").val());
            getEmployeeIncentives();
        });

        // Accion EXPORTAR
        $("#btnExport").on('click', function(e) {
            $('#alertTrackingDate').hide();
            $('#alertErrorTrackingDate').hide();
            exportData();
            $('#btnExport').hide();
            $('#btnExportLoad').show();
            $('#btnExportLoad').prop('disabled', true);
        });

        function clearForms() {
            var textMonthYear = setDate(date);
            $('#monthYearPicker').val(textMonthYear);
            // $('select').val('');
            $('select#business_id').val('');
            $('select#business_id').selectpicker("refresh");
            $('.tracking-validation-datatable').DataTable().ajax.reload();
        }

        $("#btnClear").on('click', function(e) {
            e.preventDefault();
            clearForms();
        });
    });

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
                // if success, HTML response is expected, so replace current
                if (textStatus === 'success') {
                    timeOutAlert($('#alertTrackingDate'), response.mensaje);
                    $('.tracking-validation-datatable').DataTable().ajax.reload();
                }
            },
            error: function(xhr, status, error) {
                var response = JSON.parse(xhr.responseText);
                //alert(response.errors); 
                window.location = response.url;
                $('#alertErrorTrackingDate').text(response.mensaje);
                $('#alertErrorTrackingDate').show();
                $('#btnSubmitLoad').hide();
                $('#btnSubmit').show();
            }

        }).fail(function(jqXHR, textStatus, errorThrown) {
            timeOutAlert(alert, jqXHR.responseText);

        }).done(function() {
            timeOutAlert($('#alertTrackingDate', "Realizado correctamente"));
        });
    }

    /*
    *Función para EXPORTAR los datos
    */
    function exportData() {
        params = {};
        params["monthYear"] = $('#monthYearPicker').val();
        params["cod_business"] = $("#business_id option:selected").val();
        params["_token"] = "{{ csrf_token() }}";
        params["business"] = $("#business_id option:selected").text();


        $.ajax({
            url: '{{ route("tracking.exportFinalValidation") }}',
            type: 'post',
            data: params,
            xhrFields: {
                'responseType': 'blob'
            },
            success: function(data, textStatus, jqXHR) {
                // if success, HTML response is expected, so replace current
                if (textStatus === 'success') {
                    timeOutAlert($('#alertTrackingDate'), 'Exportando los datos...');


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
                timeOutAlert($('#alertErrorTrackingDate', error));
                // var response = JSON.parse(xhr.responseText);
                // window.location = response.url;
                $('#btnSubmitLoad').hide();
                $('#btnSubmit').show();
            }
        }).fail(function(jqXHR, textStatus, errorThrown) {
            timeOutAlert($('#alertErrorTrackingDate'), jqXHR.responseText);
        }).done(function() {
            timeOutAlert($('#alertTrackingDate', "Realizado correctamente"));

            $('#btnExport').show();
            $('#btnExportLoad').hide();
        });
    }

    function setDate($date) {
        date = new Date();
        year = date.getFullYear();
        month = date.getMonth()+1;
        textMonthYear = month >= 10 ? month : '0' + month;
        fecha= textMonthYear + '/' +year;
        return fecha;
    }

    function timeOutAlert($alert, $message) {
        $alert.text($message);
        $alert.show().delay(3000).slideUp(300);
    }

</script>

@endsection

