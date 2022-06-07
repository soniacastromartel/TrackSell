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
                                                    descargar la plantilla desde <a style="color:var(--red-icot)"
                                                        href="{{ asset('assets/excel/plantilla_importar_objetivos_centros.xls') }}"><strong>aquí</strong></a>
                                </h5>
                                <hr>
                            @endif
                                <h5 class="card-title font-size-18">- <strong>Importar Venta Privada</strong>, puede
                                                    descargar la plantilla desde <a style="color:var(--red-icot)"
                                                        href="{{ asset('assets/excel/plantilla_importar_venta_privada_centros.xls') }}"><strong>aquí</strong></a>
                                </h5>
                                <h5 class="card-title font-size-18">- Indicar en formulario centro / empleado / fecha
                                                    según se requiera y hacer click en botón Calcular</h5>
                                <h5 class="card-title font-size-18">- Tenga en cuenta que el fichero a importar debe
                                                    tener extensión .xls</h5>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <form id="importTargetForm" method="POST">
                        @csrf
                        @method('POST')
                        @if ($user->rol_id == 1)
                        <div class="row">
                            <div class="col-lg-5 mt-2">
                                <div class="card">
                                    <div class="card-header card-header-danger">
                                        <h5 class="card-title">Objetivos</h5>
                                    </div>
                                    
                                    <div class="card-body">
                                        <div class="row" style="margin-top: 30px;margin-left: 120px;margin-bottom: 30px;">
                                        <div class="col-sm-7" style="margin-left: 16px;">
                                            <label class="label" >Mes <span class="obligatory">*</span> </label>
                                            <div class="input-group date" >
                                                <input id="yearTargetPicker" class='form-control' type="text" placeholder="yyyy" />
                                                <input type="hidden" name="yearTarget" id="yearTarget" />
                                            </div>
                                        </div>
                                        <div class="form-group col-sm-7"  style="padding-top: 30px;">
                                            <div id="btnImportTargets" class="file-upload btn btn-block btn-outline-corporate" style=" ">
                                                <span>{{ __('Importar Objetivos') }}</span>
                                                <input type="file" name="targetInputFile" id="targetInputFile" class="upload" />
                                            </div>

                                                    <div class="form-group col-sm-7"  style="padding-top: 30px;">
                                                        <div id="btnImportTargets" class="file-upload btn btn-block btn-red-icot" style=" ">
                                                            <span class="material-icons">
upload
</span> {{ __('Importar Objetivos') }}</span>
                                                            <input type="file" name="targetInputFile" id="targetInputFile" class="upload" />
                                                            <!-- <input type="text" id="fileuploadurl" readonly placeholder="Tamaño máximo de fichero son 2MB"> -->
                                                        </div>
                                                        <button id="targetInputFileLoad" type="submit" class="file-upload btn btn-success" style="display: none">
                                                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                                            {{ __('Importando datos...') }}
                                                        </button>
                                                </div>

                                                    <div class="form-group col-sm-7">
                                                        <div id="btnImportSales" class="file-upload btn btn-block btn-red-icot">
                                                            <span><span class="material-icons">
upload
</span> {{ __('Importar Venta Privada') }}</span>
                                                            <input type="file" name="targetInputSalesFile" id="targetInputSalesFile" class="upload" />
                                                            <!-- <input type="text" id="fileuploadurl" readonly placeholder="Tamaño máximo de fichero son 2MB"> -->
                                                        </div>
                                                    </div>

                                                    <div class="form-group col-sm-7" >
                                                        <button id="btnTracingTargets" class="file-upload btn btn-block btn-success"><span class="material-icons">
zoom_in
</span>
                                                            {{ __('Seguimiento de objetivos') }}
                                                        </button>
                                                    </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif

                            <div class="col-lg-7">
                                <div class="card" style="min-height:399px;">
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
                                                        @foreach ($centres as $centre)
                                                        <option value="{{ $centre->id }}">{{ $centre->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    <input type="hidden" name="centre" id="centre" />
                                                </div>
                                            </div>
                                            <br>
                                            <div class="form-group col-md-10 mx-auto">
                                                <div class="dropdown bootstrap-select">
                                                    <label class="label">Empleado <span class="obligatory">*</span></label>
                                                    <select class="selectpicker" name="employee_id" id="employee_id" data-size="7" data-style="btn btn-red-icot btn-round" title=" Seleccione Empleado" tabindex="-98">
                                                        @foreach ($employees as $employee)
                                                        <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    <input type="hidden" name="employee" id="employee" />
                                                </div>
                                            </div>



                                        </div>

                                        <div class="row mt-2 px-5">
                                            <div class="col-md-5" style="margin-top: 30px;">

                                                <button id="btnSubmitLoad" type="submit" class="btn btn-dark-black" style="display: none">
                                                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                                    {{ __('Obteniendo datos...') }}
                                                </button>
                                                <button id="btnClear" href="#" class="btn btn-fill btn-warning"><span class="material-icons">
                            clear_all
                            </span>
                                                    {{ __('Limpiar formulario') }}
                                                </button>
                                                <button id="btnSubmit" type="submit" class="btn btn-fill btn-default"> <span class="material-icons">
                            file_download
                            </span> {{ __('Exportar') }}</button>
                                            </div>
                                            
                                        </div>
                                        <hr class="mt-4">
                                        <div class="row">
                                        <div class="col-md-7" style="margin-top: 16px;">
                                            <button id="btnSubmit" type="submit" class="btn btn btn-dark-black">{{ __('Exportar') }}</button>
                                            <button id="btnSubmitLoad" type="submit" class="btn btn-dark" style="display: none">
                                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                                {{ __('Obteniendo datos...') }}
                                            </button>
                                        </div>
                                        <div class="col-md-5" style="margin-top: 16px; text-align: right;">
                                            <button id="btnClear" href="#" class="btn btn-fill btn-warning">
                                                {{ __('Limpiar formulario') }}
                                            </button>
                                        </div>                                          
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
<style>
    .ui-datepicker-calendar {
        display: none;
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
</style>
<script type="text/javascript">
    $(function() {

        $(".nav-item").each(function() {
            $(this).removeClass("active");
        });
        $('#pagesReport').addClass('show');
        $('#calculateIncentive').addClass('active');

        function clearForms() {
            $('select').val('');
            $('select').selectpicker("refresh");
            $("input[name=trackingState][value='service']").prop("checked", true);
        }
        $("#btnClear").on('click', function(e) {
            e.preventDefault();
            clearForms();
        });

        $("#btnSubmit").on('click', function(e) {
            e.preventDefault();
            $('#alertErrorCalculate').hide();
            $("#importTargetForm").attr('action', '{{ route('target.calculateTargets')}}');
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
                    $('#alertErrorCalculate').show().delay(2000).slideUp(300);
                    $('#btnSubmitLoad').hide();
                    $('#btnSubmit').show();
                    timeOutAlert($('#alertErrorCalculate'));
                }

            });

        });

        var d = new Date();
        var textMonthYear = (d.getMonth() + 1) + '/' + d.getFullYear();
        $('#monthYearPicker').val(textMonthYear);
        // Default functionality.
        $('#monthYearPicker').MonthPicker();

        var textYear = d.getFullYear();
        $('#yearTargetPicker').val(textYear);
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
            document.getElementById("fileuploadurl").value = this.value.replace(/C:\\fakepath\\/i, '');
            $("#importTargetForm").attr('action', '{{ route('target.import')}}');
            $("#importTargetForm").attr('enctype', "multipart/form-data");

            $('#btnImportTargets').hide();
            $('#targetInputFileLoad').show();

            $('#yearTarget').val($("#yearTargetPicker").val());
            $("#importTargetForm").submit();
        });

        $("#targetInputSalesFile").on('change', function() {
            document.getElementById("fileuploadurl").value = this.value.replace(/C:\\fakepath\\/i, '');
            $("#importTargetForm").attr('action', '{{ route('target.importSales')}}');
            $("#importTargetForm").attr('enctype', "multipart/form-data");

            $('#btnImportSales').hide();
            $('#targetInputFileLoad').show();
            $("#importTargetForm").submit();
        });

        $("#btnTracingTargets").on('click', function(e) {
            e.preventDefault();
            $("#btnTracingTargets").html(
                "<span class='spinner-border spinner-border-sm' role='status' aria-hidden='true'></span> Obteniendo datos..."
            );
            params = {};
            params["_token"] = "{{ csrf_token() }}";
            params["yearTarget"] = $("#yearTargetPicker").val();
            $.ajax({
                url: "{{ route('target.tracingTargets') }}",
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
                    alert(response.errors);
                    $('#btnSubmitLoad').hide();
                    $('#btnSubmit').show();
                },
                complete: function() {
                    $("#btnTracingTargets").html("Seguimiento de objetivos");
                }

            }).fail(function(jqXHR, textStatus, errorThrown) {

                //FIXME... show error alert
                //$.parseJSON(jqXHR.responseText);
                // alert('Error: ' + textStatus);

            });
        });

    });
</script>
@endsection