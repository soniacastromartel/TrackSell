@extends('layouts.logged')

@section('content')
@include('inc.navbar')
@include('common.alert')


<div id="alertErrorCalculate" class="alert alert-danger" role="alert" style="display: none">
</div>

<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-7">
                <div class="card card-info text-white mb-4 ml-4 p-0 mt-0">
                    <div class="card-header">
                        <i class="material-icons" style="color: var(--red-icot)">info</i>
                        <span class="font-size-18" style="vertical-align:super; font-weight:bold; color: var(--red-icot);">Instrucciones</span>
                    </div>
                    <div class="card-body" id="cardBody">
                        @if ($user->rol_id == 1)
                        <h5 class="card-title font-size-18">- <strong>Importar Objetivos</strong>, puede
                            descargar la plantilla* desde <a style="color:var(--red-icot)" href="{{ asset('assets/excel/plantilla_importar_objetivos_centros.xls') }}"><strong>aquí</strong></a>
                        </h5>
                        <hr>
                        @endif
                        <h5 class="card-title font-size-18">- <strong>Importar Venta Privada</strong>, puede
                            descargar la plantilla* desde <a style="color:var(--red-icot)" href="{{ asset('assets/excel/plantilla_importar_venta_privada_centros.xls') }}"><strong>aquí</strong></a>
                        </h5>
                        <hr>
                        <h5 class="card-title font-size-18">- <strong>Incentivos: </strong>Indicar en formulario centro / empleado / fecha
                            según se requiera y hacer click en botón <span style="color:var(--red-icot);font-weight: bolder;"> <span class="material-icons">file_download</span>Exportar</span>
                            <h5>
                                <h5 class="text-right" style="color:grey;font-size:14px;">* Tenga en cuenta que el fichero a importar debe
                                    tener extensión .xls</h5>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body">
            <form id="importTargetForm" method="POST">
                @csrf
                @method('POST')
                <div class="row">
                    <div class="col-lg-4 mt-2">
                        <div class="card" style="min-height:442px;">
                            <div class="card-header card-header-danger">
                                <h5 class="card-title">Objetivos</h5>
                            </div>

                            <div class="card-body">
                                <div class="row" style="margin-top: 30px;margin-left: 120px;margin-bottom: 30px;">
                                    <div class="col-sm-7" style="margin-left: 16px;">
                                        <label class="label">Año <span class="obligatory">*</span> </label>
                                        <div class="input-group date">
                                            <input id="yearTargetPicker" class='form-control' type="text" placeholder="yyyy" />
                                            <input type="hidden" name="yearTarget" id="yearTarget" />
                                        </div>
                                    </div>
                                    @if ($user->rol_id != 1)
                                    <span class="m-5"></span>
                                    @endif
                                    @if ($user->rol_id == 1)
                                    <div class="form-group col-sm-7" style="padding-top: 30px;">
                                        <div id="btnImportTargets" class="file-upload btn btn-block btn-outline-corporate">
                                            <span class="material-icons mr-1">upload</span>{{ __('Importar Objetivos') }}
                                            <input type="file" name="targetInputFile" id="targetInputFile" class="upload" />
                                        </div>
                                        <button id="targetInputFileLoad" type="submit" class="file-upload btn btn-success" style="display: none">
                                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                            {{ __('Importando datos...') }}
                                        </button>
                                    </div>
                                    @endif
                                    <div class="form-group col-sm-7">
                                        <div id="btnImportSales" class="file-upload btn btn-block btn-red-icot">
                                            <span class="material-icons mr-1">upload</span>{{ __('Importar Venta Privada') }}
                                            <input type="file" name="targetInputSalesFile" id="targetInputSalesFile" class="upload" />
                                        </div>
                                    </div>
                                    @if ($user->rol_id != 1)
                                    <span class="m-3"></span>
                                    @endif
                                    <div class="form-group col-sm-7">
                                        <button id="btnTracingTargets" class="file-upload btn btn-block btn-success">
                                            <span class="material-icons mr-1">download</span>{{ __('Descargar Seguimiento') }}
                                        </button>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-8">
                        <div class="card" style="min-height:398px;">
                            <div class="card-header card-header-danger">
                                <h5 class="card-title">Incentivos</h5>
                            </div>
                            <div class="card-body" style="margin-top: 30px;">

                                <div class="row" style="margin-left: 60px; margin-right: 60px">
                                    <div class="form-group col-4 mt-5">
                                        <label class="label">Fecha <span class="obligatory">*</span> </label>
                                        <div class="form-group input-group date">
                                            <input type="hidden" name="monthYear" id="monthYear" />
                                            <input id="monthYearPicker" class='form-control' type="text" placeholder="yyyy/mm" />
                                        </div>
                                    </div>

                                    <div class="col-8">
                                        <div class="form-group col-md-10 mx-auto">
                                            <div class="dropdown bootstrap-select">
                                                <label class="label" for="centre_origin_id">Centro <span class="obligatory">*</span></label>
                                                <select class="selectpicker" name="centre_id" id="centre_id" data-size="7" data-style="btn btn-red-icot btn-round" title=" Seleccione Centro" tabindex="-98">
                                                    @if ($user->rol_id != 1)
                                                    @foreach ($centres as $centre)
                                                    @if ($centre->id == $user->centre_id)
                                                    <option value="{{ $centre->id }}" selected>{{ $centre->name }}</option>
                                                    @endif
                                                    @endforeach

                                                    @else
                                                    @foreach ($centres as $centre)
                                                    <option value="{{ $centre->id }}">{{ $centre->name }}</option>
                                                    @endforeach
                                                    @endif
                                                </select>
                                                <input type="hidden" name="centre" id="centre" />
                                            </div>
                                        </div>
                                        <br>
                                        <div class="form-group col-md-10 mx-auto">
                                            <div class="dropdown bootstrap-select">
                                                <label class="label">Empleado <span class="obligatory">*</span></label>
                                                <select class="selectpicker" name="employee_id" id="employee_id" data-size="7" data-style="btn btn-red-icot btn-round" title=" Seleccione Empleado" tabindex="-98">
                                                <option>SIN SELECCION </option>
                                                    @if ($user->rol_id != 1)
                                                    @foreach ($employees as $employee)
                                                    @if ($employee->centre_id== $user-> centre_id)
                                                    <option value="{{ $employee->id }}">{{ $employee->name}}</option>
                                                    @endif
                                                    @endforeach

                                                    @else
                                                    @foreach ($employees as $employee)
                                                    <option value="{{ $employee->id }}">{{ $employee->name}}</option>
                                                    @endforeach
                                                    @endif
                                                </select>
                                                <input type="hidden" name="employee" id="employee" />
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <hr class="mt-4">
                                <div class="row" style="display: flex;justify-content: space-evenly;">
                                    <div class="form-group">
                                        <button id="btnClear" href="#" class="myBtn btn  btn-warning">
                                            <span class="material-icons">clear_all</span>{{ __('Limpiar formulario') }}
                                        </button>
                                        <button id="btnSubmit" type="submit" class="myBtn btn btn-dark-black">
                                            <span class="material-icons">file_download</span>
                                            {{ __('Exportar') }}</button>
                                        <button id="btnSubmitLoad" type="submit" class="btn  btn-dark-black" style="display: none">
                                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                            {{ __('Obteniendo datos...') }}
                                        </button>
                                        <button id="btnIncentivesPreview" class="myBtn btn btn-grey">
                                            <span class="material-icons mr-1">visibility</span>{{ __('Ver Incentivos') }}
                                        </button>
                                        <button id="btnIncentivesLoad" type="submit" class="file-upload btn btn-dark-black" style="display: none">
                                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                            {{ __('Obteniendo datos...') }}
                                        </button>
                                        <button id="btnSummaryPreview" class="myBtn btn btn-grey">
                                            <span class="material-icons mr-1">visibility</span>{{ __('Ver Resumen') }}
                                        </button>
                                        <button id="btnSummaryLoad" type="submit" class="file-upload btn btn-dark-black" style="display: none">
                                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                            {{ __('Obteniendo datos...') }}
                                        </button>
                                        @if ($user->rol_id != 1)
                                        <button id="btnTargetsPreview" class="myBtn btn btn-grey">
                                            @else
                                            <button id="btnTargetsPreview" class="myBtn btn btn-grey" disabled="disabled">
                                                @endif
                                                <span class="material-icons mr-1">visibility</span>{{ __('Ver Objetivos') }}
                                            </button>
                                            <button id="btnTargetsLoad" type="submit" class="file-upload btn btn-dark-black" style="display: none">
                                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                                {{ __('Obteniendo datos...') }}
                                            </button>


                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="col-sm-3" style="margin-left: 560px;">
            <label id="centreName" class=""></label>
        </div>

        <div class="row" id="targetsData">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header card-header-danger">
                        <h4 class="card-title" id="title-targets">OBJETIVOS</h4>
                    </div>
                    <div class="card-header-table">
                        <table class="table table-striped table-bordered targets-datatable col-lg-12">
                            <thead class="table-header">
                                <tr>
                                    <th>Objetivo Venta Cruzada</th>
                                    <th>Objetivo Venta Privada</th>
                                    <th>Venta Cruzada</th>
                                    <th>Venta Privada</th>
                                    <th>Número de Personas</th>
                                    <th>Venta Media por Persona</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="row" id="incentivesData">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header card-header-danger">
                        <h4 class="card-title" id="title-incentives">INCENTIVOS</h4>
                    </div>
                    <div class="card-header-table">
                        <table class="table table-striped table-bordered incentives-datatable col-lg-12">
                            <thead class="table-header">
                                <tr>
                                    <th>Centro</th>
                                    <th>HC</th>
                                    <th>Paciente</th>
                                    <th>Servicio</th>
                                    <th>Cantidad</th>
                                    <th>Empleado</th>
                                    <th>Precio</th>
                                    <th>Incentivo</th>
                                    <th>Bonus</th>
                                    <th>Ingresos</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="row" id="summaryData">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header card-header-danger">
                        <h4 class="card-title" id="title-summary">RESUMEN</h4>
                    </div>
                    <div class="card-header-table">
                        <table class="table table-striped table-bordered summary-datatable col-lg-12">
                            <thead class="table-header">
                                <tr>
                                    <th>Centro</th>
                                    <th>Suma Incentivo</th>
                                    <th>Suma Bonus</th>
                                    <th>Suma Ingreso</th>
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
    #centreName {
        display: block;
        text-align: center;
        font-weight: bold;
        font-size: xx-large;
        font-family: monospace;
        color: white;
        border: 2px solid var(--red-icot);
        background-color: var(--red-icot);
    }

    .ui-datepicker-calendar {
        display: none;
    }

    .myBtn {
        margin: 25px;
    }

    .file-upload {
        margin: 0 10px 0 25px;

    }

    .file-upload input.upload {
        position: absolute;
        top: 0;
        right: 0;
        margin: 0;
        padding: 0;
        z-index: 10;
        font-size: 20px;
        cursor: pointer;
        height: 36px;
        opacity: 0;
        filter: alpha(opacity=0);
        background-color: var(--red-icot);
    }

    #fileuploadurl {
        border: none;
        font-size: 12px;
        padding-left: 0;
        width: 250px;
    }

    .ui-datepicker-calendar {
        display: none;
    }



    table.dataTable.dataTable_width_auto {
        width: 100% !important;
    }

    .card-header-table {
        width: 100%;
        margin-top: 0px !important;
    }

    .incentives-datatable {
        table-layout: fixed;
        width: 100% !important;
    }

    .incentives-datatable td,
    .incentives-datatable th {
        text-overflow: ellipsis;
        overflow: hidden;
    }


    #DataTables_Table_0_paginate>ul.pagination {
        margin: 16px 0 !important;
    }
</style>

<script type="text/javascript">
    var table;

    $(function() {
        // console.log($user);

        $('#centreName').hide();

        $(".nav-item").each(function() {
            $(this).removeClass("active");
        });
        $('#pagesReport').addClass('show');
        $('#targetsData').hide();
        $('#incentivesData').hide();
        $('#summaryData').hide();


        $("#centre_id").on('change', function() {
            $('#btnTargetsPreview').removeAttr('disabled');

        });

        /**
         * Visualizar Objetivos
         */
        $("#btnTargetsPreview").on('click', function(e) {
            e.preventDefault();
            $('#centreName').show();
            document.getElementById('centreName').innerHTML = $("#centre_id option:selected").text();
            $('#btnTargetsPreview').hide();
            $('#btnTargetsLoad').show();
            $('#btnTargetsLoad').prop('disabled', true);
            drawTable('.targets-datatable');

            $('#targetsData').show();
            $('#summaryData').hide();
            $('#incentivesData').hide();
        });

        /**
         * Visualizar Incentivos
         */

        $("#btnIncentivesPreview").on('click', function(e) {
            e.preventDefault();
            $('#btnIncentivesPreview').hide();
            $('#btnIncentivesLoad').show();
            $('#btnIncentivesLoad').prop('disabled', true);
            drawTable('.incentives-datatable');


            $('#targetsData').hide();
            $('#summaryData').hide();
            $('#incentivesData').show();

        });

        /**
         * Visualizar Resumen Incentivos
         */

        $("#btnSummaryPreview").on('click', function(e) {
            e.preventDefault();
            $('#btnSummaryPreview').hide();
            $('#btnSummaryLoad').show();
            $('#btnSummaryLoad').prop('disabled', true);
            drawTable('.summary-datatable');


            $('#targetsData').hide();
            $('#incentivesData').hide();
            $('#summaryData').show();

        });

        function clearForms() {
            $('#centreName').hide();
            $('#targetsData').hide();
            $('#incentivesData').hide();
            $('#summaryData').hide();
            var d = new Date();
            var textMonthYear = setDate(d);
            $('#monthYearPicker').val(textMonthYear);
            $('#yearTargetPicker').val(d.getFullYear());
            $('select').val('');
            $('select').selectpicker("refresh");
            $("input[name=trackingState][value='service']").prop("checked", true);
        }
        $("#btnClear").on('click', function(e) {
            e.preventDefault();
            clearForms();
        });

        /**
         * Exportar Incentivos
         */

        $("#btnSubmit").on('click', function(e) {
            console.log($("#centre_id option:selected").text());
            e.preventDefault();
            $('#alertErrorCalculate').hide();
            $("#importTargetForm").attr('action', '{{ route('target.incentivesReportDownload')}}');
            $('#btnSubmit').hide();
            $('#btnSubmitLoad').show();
            $('#btnSubmitLoad').prop('disabled', true);
            $('#centre').val($("#centre_id option:selected").text());
            $('#employee').val($("#employee_id option:selected").text());
            $('#monthYear').val($("#monthYearPicker").val());

            params = {};
            params["_token"] = "{{ csrf_token() }}";
            params["centre"] = $('#centre').val();
            params["employee"] = $('#employee').val();
            params["monthYear"] = $('#monthYear').val();
            console.log(params);

            $.ajax({
                url: $("#importTargetForm").attr('action'),
                type: 'post',
                data: params,
                dataType: 'binary',
                xhrFields: {
                    'responseType': 'blob'
                },
                xhr: function() {
                    var xhr = new XMLHttpRequest();
                    xhr.onreadystatechange = function() {
                        if (xhr.readyState == 2) {
                            if (xhr.status == 200) {
                                xhr.responseType = "blob";
                            } else {
                                xhr.responseType = "text";
                            }
                        }
                    };
                    return xhr;
                },
                success: function(data, textStatus, jqXHR) {
                    // if success, HTML response is expected, so replace current
                    if (textStatus === 'success') {
                        $('#btnSubmitLoad').hide();
                        $('#btnSubmit').show();
                        var link = document.createElement('a'),
                            filename = 'target.xls';
                        link.href = URL.createObjectURL(data);
                        link.download = filename;
                        link.click();
                    }
                },
                error: function(xhr, status, error) {
                    var response = JSON.parse(xhr.responseText);
                    $('#alertErrorCalculate').text(response.errors);
                    $('#alertErrorCalculate').show();
                    $('#btnSubmitLoad').hide();
                    $('#btnSubmit').show();
                    timeOutAlert($('#alertErrorCalculate'), response);
                },
                complete: function() {
                    $("#btnSubmit").html("<span class='material-icons mr-1'>download</span> Exportar");
                }
            });
        });

        var date = new Date();
        var textMonthYear = setDate(date);
        $('#monthYearPicker').val(textMonthYear);
        $('#monthYearPicker').MonthPicker();

        $('#yearTargetPicker').val(date.getFullYear());
        $('#yearTargetPicker').datepicker({
            changeMonth: false,
            changeYear: true,
            showButtonPanel: true,
            closeText: 'Select',
            currentText: 'This year',
            onClose: function(dateText, inst) {
                $(this).val($.datepicker.formatDate("yy", new Date(inst['selectedYear'], 0, 1)));
            },
        });

        $("#targetInputFile").on('change', function() {
            // document.getElementById("fileuploadurl").value = this.value.replace(/C:\\fakepath\\/i, '');
            $("#importTargetForm").attr('action', '{{ route('target.import')}}');
            $("#importTargetForm").attr('enctype', "multipart/form-data");
            $('#btnImportTargets').hide();
            $('#targetInputFileLoad').show();

            $('#yearTarget').val($("#yearTargetPicker").val());
            $("#importTargetForm").submit();
        });

        $("#targetInputSalesFile").on('change', function() {
            // document.getElementById("fileuploadurl").value = this.value.replace(/C:\\fakepath\\/i, '');
            $("#importTargetForm").attr('action', '{{ route('target.importSales')}}');
            $("#importTargetForm").attr('enctype', "multipart/form-data");
            $('#btnImportSales').hide();
            $('#targetInputFileLoad').show();
            $("#importTargetForm").submit();
        });



        /**
         * Exportar Seguimiento de Objetivos
         */

        $("#btnTracingTargets").on('click', function(e) {
            e.preventDefault();
            $("#btnTracingTargets").html(
                "<span class='spinner-border spinner-border-sm' role='status' aria-hidden='true'></span> Obteniendo datos..."
            );
            params = {};
            params["_token"] = "{{ csrf_token() }}";
            params["yearTarget"] = $("#yearTargetPicker").val();
            $.ajax({
                url: "{{ route('target.targetsReportDownload') }}",
                type: 'post',
                data: params,
                dataType: 'binary',
                xhrFields: {
                    'responseType': 'blob'
                },
                xhr: function() {
                    var xhr = new XMLHttpRequest();
                    xhr.onreadystatechange = function() {
                        if (xhr.readyState == 2) {
                            if (xhr.status == 200) {
                                xhr.responseType = "blob";
                            } else {
                                xhr.responseType = "text";
                            }
                        }
                    };
                    return xhr;
                },
                success: function(data, textStatus, jqXHR) {
                    if (textStatus === 'success') {
                        $('#btnSubmitLoad').hide();
                        $('#btnSubmit').show();
                        var link = document.createElement('a'),
                            filename = 'target.xls';
                        link.href = URL.createObjectURL(data);
                        link.download = filename;
                        link.click();
                    }
                },
                error: function(xhr, status, error) {
                    var response = JSON.parse(xhr.responseText);
                    alert(response.errors);
                    $('#btnSubmitLoad').hide();
                    $('#btnSubmit').show();
                },
                complete: function() {
                    $("#btnTracingTargets").html("<span class='material-icons mr-1'>download</span> Seguimiento de objetivos");
                }

            }).fail(function(jqXHR, textStatus, errorThrown) {
                timeOutAlert($('#alertErrorCalculate'), jqXHR.responseText);

            });
        });



        function drawTable(idDataTable) {
            $('#centre').val($("#centre_id option:selected").text());
            $('#employee').val($("#employee_id option:selected").text());
            $('#monthYear').val($("#monthYearPicker").val());

            params = {};
            params["_token"] = "{{ csrf_token() }}";
            params["centre"] = $('#centre').val();
            params["employee"] = $('#employee').val();
            params["monthYear"] = $('#monthYear').val();

            lenMenu = [];
            url = "";
            columnss = [];

            if (idDataTable == '.incentives-datatable') {
                params["isSumary"] = false;
                // Incentivos
                $('incentives-datatable').DataTable();
                $('incentives-datatable').DataTable().ajax.reload();
                lenMenu = [10, 25, 50];
                url = "{{ route('target.incentivesReportView') }}";
                columnss = [{
                        data: 'centre_employee',
                        name: 'centre_employee',

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
                        data: 'quantity',
                        name: 'quantity'
                    },
                    {
                        data: 'employee',
                        name: 'employee'
                    },
                    {
                        data: 'price',
                        name: 'price'
                    },
                    {
                        data: 'direct_incentive',
                        name: 'direct_incentive'
                    },
                    {
                        data: 'bonus',
                        name: 'bonus'
                    },
                    {
                        data: 'total_paid',
                        name: 'total_paid'
                    }
                ];

                columnsDef = [{
                        width: "5%",
                        targets: 0
                    },
                    {
                        width: "5%",
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
                        width: "5%",
                        targets: 4
                    },
                    {
                        width: "15%",
                        targets: 5
                    },
                    {
                        width: "5%",
                        targets: 6
                    },
                    {
                        width: "5%",
                        targets: 7
                    },
                    {
                        width: "5%",
                        targets: 8
                    },
                    {
                        width: "5%",
                        targets: 9
                    },
                    {
                        targets: [6, 7, 8, 9],
                        render: $.fn.dataTable.render.number('.', ',', 2, '', '€') //columnDefs number renderer (thousands, decimal, precision, simbolo/moneda,posfix)
                    },
                    {
                        targets: [0, 1, 4, 6, 7, 8, 9],
                        visible: true,
                        className: 'dt-body-right'


                    },
                    {
                        targets: [2, 3, 5],
                        visible: true,
                        className: 'dt-body-left'

                    },
                    {
                        targets: [2, 3, 5],
                        data: "employee",
                        render: function(data, type, row) {
                            if (data != null) {
                                return data.toUpperCase();
                            } else {
                                return data;
                            }
                        }
                    },
                    {
                        targets: '_all',
                        defaultContent: ' '
                    }
                ];

            } else if (idDataTable == '.summary-datatable') {
                params["isSumary"] = true;
                $('summary-datatable').DataTable();
                $('summary-datatable').DataTable().ajax.reload();
                lenMenu = [20];
                url = "{{ route('target.incentivesSummaryView') }}";
                columnss = [{
                        data: 'centre_name',
                        name: 'centre_name',

                    },
                    {
                        data: 'total_incentive',
                        name: 'total_incentive'
                    },
                    {
                        data: 'total_super_incentive',
                        name: 'total_super_incentive'
                    },

                    {
                        data: 'total_income',
                        name: 'total_income'
                    }
                ];
                columnsDef = [{
                        targets: [1, 2, 3],
                        render: $.fn.dataTable.render.number('.', ',', 2, '', '€') //columnDefs number renderer (thousands, decimal, precision, simbolo/moneda,posfix)
                    },
                    {
                        targets: [1, 2, 3],
                        visible: true,
                        className: 'dt-body-right'


                    },
                    {
                        targets: [0],
                        visible: true,
                        className: 'dt-body-left'

                    },
                    {
                        targets: '_all',
                        defaultContent: ' '
                    }
                ];



            } else {
                // Objetivos
                params["isSumary"] = false;
                $('targets-datatable').DataTable();
                $('targets-datatable').DataTable().ajax.reload();
                lenMenu = [10];
                url = "{{ route('target.targetsReportView') }}";
                columnss = [{
                        data: 'obj_vc',
                        name: 'obj_vc'
                    },
                    {
                        data: 'obj_vp',
                        name: 'obj_vp'
                    },
                    {
                        data: 'vc',
                        name: 'vc'
                    },
                    {
                        data: 'vp',
                        name: 'vp'
                    },
                    {
                        data: 'cont_employees',
                        name: 'cont_employees'
                    },
                    {
                        data: 'salesPerEmployee',
                        name: 'salesPerEmployee'
                    }

                ];

                columnsDef = [{
                        targets: [0, 1, 2, 3, 5],
                        render: $.fn.dataTable.render.number('.', ',', 2, '', '€') //columnDefs number renderer (thousands, decimal, precision, simbolo/moneda,posfix)
                    },
                    {
                        targets: '_all',
                        visible: true,
                        className: 'dt-body-right'

                    },
                    {
                        targets: '_all',
                        defaultContent: ' '
                    }
                ];
            }

            $(idDataTable).dataTable({
                lengthMenu: lenMenu,
                processing: true,
                bDestroy: true,
                ordering: false,
                language: {
                    "emptyTable": "No hay datos disponibles en la tabla",
                    "paginate": {
                        "previous": "Anterior",
                        "next": "Siguiente"
                    },
                    "infoEmpty": "Sin registros",
                    "search": "Buscar:",
                    "info": "",
                    "loadingRecords": "Cargando...",
                    "processing": "Procesando, espere...",
                    "lengthMenu": "Mostrando _MENU_ registros",
                    "infoFiltered": ""
                },
                ajax: {
                    url: url,
                    type: 'post',
                    data: params,
                    dataSrc: function(json) {
                        if (json == null) {
                            return [];
                        } else {
                            return json.data;
                        }
                    }

                },
                columnDefs: columnsDef,
                columns: columnss,
                initComplete: function(data, idDatatable) {
                    console.log(data.json.data);
                    console.log(data);
                    console.log(idDataTable);
                    if (data.jqXHR.statusText === 'OK') {
                        if (idDataTable == '.incentives-datatable') {
                            $('#btnIncentivesLoad').hide();
                            $('#btnIncentivesPreview').show();
                        }
                        if (idDataTable == '.targets-datatable') {
                            $('#btnTargetsPreview').show();
                            $('#btnTargetsLoad').hide();
                        }
                        if (idDataTable == '.summary-datatable') {
                            $('#btnSummaryPreview').show();
                            $('#btnSummaryLoad').hide();
                        }
                    } else {
                        var response = JSON.parse(xhr.responseText);
                        $('#alertErrorLeague').text(response.errors);
                        $('#alertErrorLeague').show().delay(2000).slideUp(300);
                        if (idDataTable == '.incentives-datable') {
                            $('#btnIncentivesLoad').hide();
                            $('#btnIncentivesPreview').show();
                        }
                        if (idDataTable == '.targets-datatable') {
                            $('#btnTargetsPreview').hide();
                            $('#btnTargetsLoad').show();
                        }
                        if (idDataTable == '.summary-datatable') {
                            $('#btnSummaryPreview').hide();
                            $('#btnSummaryLoad').show();
                        }
                    }
                    this.api().columns().every(function() {
                        var column = this;
                        var input = document.createElement("input");
                        $(input).appendTo($(column.footer()).empty())
                            .on('change', function() {
                                var val = $.fn.dataTable.util.escapeRegex($(this).val());
                                column
                                    .search(val ? '^' + val + '$' : '', true, false)
                                    .draw();
                            });
                    });
                }
            });
        }

    });

    function setDate($date) {
        date = new Date();
        year = date.getFullYear();
        month = date.getMonth() + 1;
        textMonthYear = month >= 10 ? month : '0' + month;
        fecha = textMonthYear + '/' + year;
        return fecha;
    }

    function timeOutAlert($alert, $message) {
        $alert.text($message);
        $alert.show().delay(2000).slideUp(300);
    }
</script>
@endsection