@extends('layouts.logged')

@section('content')
@include('inc.navbar')

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
                <div class="col-lg-8 p-0">
                    <div class="col-lg-9 ">
                        <div class="card card-info text-white ml-4 mt-0">
                            <div class="card-header">
                                <i class="material-icons" style="color: var(--red-icot);">info</i>
                                <span style="font-size:16px; vertical-align:super; font-weight:bold; color: var(--red-icot)">Funcionamiento</span>
                            </div>
                            <div class="card-body py-0 mb-3">
                                <div class="row">
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
                <div class="card-body row justify-between">
                    <div class="col-md-6" style="margin-top:35px;">
                        <div class="row m-0" >
                            <div class="col-md-4 dropdown bootstrap-select mb-2" style="margin-right: 25px;">
                                <select class="selectpicker" name="business_id" id="business_id" data-size="7" data-style="btn btn-red-icot btn-round" title=" Seleccione Cod Empresa" tabindex="-98">
                                    <option value="-1">SIN CODIGO </option>
                                    @foreach ($a3business as $a3business)
                                    <option value="{{$a3business->code_business}}">{{ $a3business->code_business .'-'. $a3business->name_business}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="input-group date">
                                <input id="monthYearPicker" class='form-control' type="text" placeholder="yyyy/mm" />
                                <input type="hidden" name="monthYear" id="monthYear" />
                            </div>
                        </div>
                            <div class="col-md-12 input-group px-3" style="margin-top:35px;">
                                <button id="btnCalculate" type="button" class="btn btn-fill btn-grey" ><span class="material-icons">
                            calculate</span> {{ __('Calcular') }}</button>
                                <button id="btnCalculateLoad" type="button" class="btn btn-success" style="display: none">
                                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                    {{ __('Calculando datos...') }}
                                </button>
                                <button id="btnClear" href="#" class="ml-2 btn btn-fill btn-warning">
                                <span class="material-icons">
                            clear_all
                            </span>    {{ __('Limpiar formulario') }}
                                </button>
<<<<<<< Updated upstream
                                <button id="btnSubmit" type="submit" class="btn btn-success">{{ __('Buscar') }}</button>
                                <button id="btnSubmitLoad" type="submit" class="btn btn-success" style="display: none">
=======
                                <button id="btnSubmit" type="submit" class="btn btn-success"><span class="material-icons">
                                    {{ __('Obteniendo datos...') }}
                                </button>
                            </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="col-md-12 input-group date px-3 actions-container" style="padding-top: 15px;">
                            <button id="btnValidate" type="button" class="ml-2 btn btn-fill btn-red-icot"><span class="material-icons">
                            paid</span> {{ __('Pagar todos') }}</button>
                            <button id="btnValidateLoad" type="submit" class="btn btn-red-icot" style="display: none">
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                {{ __('Validando datos...') }}
                            </button>
<<<<<<< Updated upstream
                            <button id="btnExport" type="button" class="ml-2 btn btn-fill btn-dark-black">{{ __('Exportar') }}</button>
=======
                            <button id="btnExport" type="button" class="ml-2 btn btn-fill btn-default"><span class="material-icons">
                            file_download
                            </span> {{ __('Exportar') }}</button>
>>>>>>> Stashed changes
                            <button id="btnExportLoad" type="submit" class="btn btn-grey" style="display: none">
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                {{ __('Exportando datos...') }}
                            </button>
                        </div>
                </div>
                
                </div>
            </div>
        </form>
        <table class="table  table-striped table-bordered tracking-validation-datatable">
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
<style>
    .tracking-validation-datatable tr>:nth-child(6) {
        color: #959ba3;
    }

    .month-picker-open-button {
        margin-right: 15px;
    }

    .actions-container {
        display: flex;
        align-items: flex-end;
        justify-content: flex-end;
        height: 100%;
    }
</style>
<script type="text/javascript">
    var table;
    $(function() {

        $(".nav-item").each(function() {
            $(this).removeClass("active");
        });
        $('#pagesTracking').addClass('show');
        $('#trackingValidateFinal').addClass('active');

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

        var d = new Date();
        var textMonthYear = (d.getMonth() + 1) + '/' + d.getFullYear();
        $('#monthYearPicker').val(textMonthYear);
        $('#monthYearPicker').MonthPicker();

        function getEmployeeIncentives() {

            if ($.fn.dataTable.isDataTable('.tracking-validation-datatable')) {
                $('.tracking-validation-datatable').DataTable().ajax.reload();
            }

            table = $('.tracking-validation-datatable').DataTable({
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
            //table.columns.adjust().draw();
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
                $('#alertErrorTrackingDate').text("No hay datos seleccionados");
                $('#alertErrorTrackingDate').show().delay(2000).slideUp(300);
                return;
            }
            var validateData = table.ajax.json();
            if (validateData['recordsTotal'] == 0) {
                $('#alertErrorTrackingDate').text("No hay datos seleccionados");
                $('#alertErrorTrackingDate').show().delay(2000).slideUp(300);
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
                        $('#alertTrackingDate').text("Recomendaciones validadas correctamente");
                        $('#alertTrackingDate').show().delay(2000).slideUp(300);
                        $('.tracking-validation-datatable').DataTable().ajax.reload();
                    }
                },
                error: function(xhr, status, error) {
                    //alert('OK calculados incentivos');
                    //$('#btnCalculate').show();
                    $('#alertErrorTrackingDate').text("Error al validar");
                    $('#alertErrorTrackingDate').show().delay(2000).slideUp(300);
                    $('#btnValidateLoad').hide();
                }

            }).fail(function(jqXHR, textStatus, errorThrown) {
                //alert('Error cargando servicios');
            }).done(function() {
                timeOutAlert($('#alertErrorTrackingDate'));
                timeOutAlert($('#alertTrackingDate'));
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
                    // if success, HTML response is expected, so replace current
                    //alert('OK calculados incentivos'); 
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
                //alert('Error cargando servicios');
            }).done(function() {
                timeOutAlert($('#alertErrorTrackingDate'));
                timeOutAlert($('#alertTrackingDate'));
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
                $('#alertErrorTrackingDate').text("Indique una empresa");
                $('#alertErrorTrackingDate').show().delay(2000).slideUp(300);
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
    });

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
                    $('#alertTrackingDate').text(response.mensaje);
                    $('#alertTrackingDate').show().delay(2000).slideUp(300);
                    //table.ajax.reload();
                    $('.tracking-validation-datatable').DataTable().ajax.reload();
                }
            },
            error: function(xhr, status, error) {
                var response = JSON.parse(xhr.responseText);
                //alert(response.errors); 
                window.location = response.url;
                $('#alertErrorTrackingDate').text(response.mensaje);
                $('#alertErrorTrackingDate').show().delay(2000).slideUp(300);
                $('#btnSubmitLoad').hide();
                $('#btnSubmit').show();
            }

        }).fail(function(jqXHR, textStatus, errorThrown) {

            //alert('Error cargando servicios');

        }).done(function() {
            timeOutAlert($('#alertErrorTrackingDate'));
            timeOutAlert($('#alertTrackingDate'));
        });
    }

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
                var response = JSON.parse(xhr.responseText);
                window.location = response.url;
                $('#btnSubmitLoad').hide();
                $('#btnSubmit').show();
            }
        }).done(function() {
            timeOutAlert($('#alertErrorTrackingDate'));
            timeOutAlert($('#alertTrackingDate'));

            $('#btnExport').show();
            $('#btnExportLoad').hide();
        });
    }
</script>

@endsection