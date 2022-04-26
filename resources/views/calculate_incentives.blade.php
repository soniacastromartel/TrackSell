@extends('layouts.logged')

@section('content')
@include('inc.navbar')


<div id="alertErrorCalculate" class="alert alert-danger" role="alert" style="display: none">
</div>
@if (session('success'))
    <div class="alert alert-success" role="alert">
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger" role="alert">
        {{ session('error') }}
    </div>
@endif


<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">

                <div class="card ">
                    <div class="card-header card-header-info card-header-text">
                      <div class="card-text">
                        <h4 class="card-title">Informes</h4>
                      </div>
                    </div>
                    <div class="card-body">
                            <form id="importTargetForm"  method="POST" >
                            
                                @csrf
                                @method('POST')
                                <div class="row px-5">
                                    <div class="card text-white mb-3" style="background-color: #999999 !important;max-width:70%">
                                        <div class="card-header">
                                            <i class="material-icons" style="color: #b61836">info</i>
                                            <span style="font-size:18px; vertical-align:super">Pasos a realizar:</span>   
                                        </div>
                                        <div class="card-body">
                                          <h5 class="card-title">1.- Importar venta privada, plantilla puede descargarse <a style="color:blue" href="{{asset('assets/excel/plantilla_importar_objetivos_centros.xls')}}">aquí</a> </h5>
                                          <h5 class="card-title">2.- Indicar en formulario centro / empleado / fecha según se requiera y hacer click en botón Calcular</h5>
                                          <h5 class="card-title">3.- Tenga en cuenta que el fichero a importar debe tener extensión .xls</h5>
                                        </div>
                                    </div>
                                </div>    
                                @if ( $user->rol_id == 1)
                                <div class="row ml-2 px-5">
                                    <div class="input-group date" style="padding-top: 15px;width:60px !important">
                                        <input id="yearTargetPicker" class='form-control' type="text"  placeholder="yyyy" />
                                        <input type="hidden" name="yearTarget" id="yearTarget"/>
                                    </div>
                                    <div class="form-group" style="flex:0 0 17% !important; padding-left:0px; padding-top: 6px;">
                                        <div id="btnImportTargets"  class="file-upload mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--accent" style="margin-left: 16px">
                                            <span>{{ __('Importar Objetivos') }}</span>
                                            <input type="file" name="targetInputFile" id="targetInputFile" class="upload" />
                                            <input type="text" id="fileuploadurl" readonly placeholder="Tamaño máximo de fichero son 2MB">
                                        </div>
                                        <button id="targetInputFileLoad" type="submit" class="file-upload btn btn-success" style="display: none">
                                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                            {{ __('Importando datos...') }}
                                        </button>
                                    </div>
                                    <div class="form-group col-md-4" style="padding-top: 6px;">
                                        <button id="btnTracingTargets" class="btn btn-info mdl-button" >
                                            {{ __('Seguimiento de objetivos') }}
                                        </button>
                                    </div>
                                </div>
                                @endif
                                <div class="row  mt-2 px-5">
                                    <div class="form-group col-md-4" style="flex:0 0 17% !important; padding-left:0px; padding-top: 6px;">
                                        <div id="btnImportSales"  class="file-upload mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--accent" style="margin-left: 16px">
                                            <span>{{ __('Importar Venta Privada') }}</span>
                                            <input type="file" name="targetInputSalesFile" id="targetInputSalesFile" class="upload" />
                                            <input type="text" id="fileuploadurl" readonly placeholder="Tamaño máximo de fichero son 2MB">
                                        </div>
                                    </div>
                                    <div class="form-group col-md-2">
                                        <div class="dropdown bootstrap-select">
                                            <select class="selectpicker" name="centre_id" id="centre_id" data-size="7" data-style="btn btn-primary btn-round" title=" Seleccione Centro" tabindex="-98">
                                                
                                                @foreach ($centres as  $centre)
                                                <option value="{{$centre->id}}">{{$centre->name}}</option>
                                                @endforeach
                                            </select>
                                            <input type="hidden" name="centre" id="centre"/>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-2">
                                        <div class="dropdown bootstrap-select">
                                            <select class="selectpicker" name="employee_id" id="employee_id" data-size="7" data-style="btn btn-primary btn-round" title=" Seleccione Empleado" tabindex="-98">
                                                
                                                @foreach ($employees as  $employee)
                                                <option value="{{$employee->id}}" >{{$employee->name}}</option>
                                                @endforeach
                                            </select>
                                            <input type="hidden" name="employee" id="employee"/>
                                        </div>
                                    </div>
                                    <div class="col-md-6 input-group date" style="padding-top: 15px;">
                                        <input id="monthYearPicker" class='form-control' type="text"  placeholder="yyyy/mm" />
                                        <input type="hidden" name="monthYear" id="monthYear"/>
                                    </div>
                                </div>
                                <div class="row mt-2 px-5">
                                    <div class="col-md-5">
                                        <button id="btnClear" href="#" class="btn btn-fill btn-warning">
                                            {{ __('Limpiar formulario') }}
                                            </button> 
                                        <button id="btnSubmit" type="submit" class="btn btn-fill btn-success">{{ __('Calcular') }}</button>
                                        <button id="btnSubmitLoad" type="submit" class="btn btn-success" style="display: none">
                                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                            {{ __('Obteniendo datos...') }}
                                        </button>
                                    </div>
                                </div>
                            </form>
                    </div>
                </div>
            </div>
        </div>
    </div>       
</div>
<style>


.ui-datepicker-calendar { display: none; }

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
}

#fileuploadurl{
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
    $(function () {
        
        $(".nav-item").each(function(){
            $(this).removeClass("active");
        });
        $('#pagesReport').addClass('show');
        $('#calculateIncentive').addClass('active');

        function clearForms()
        {
            $('select').val('');
            $('select').selectpicker("refresh");
            $("input[name=trackingState][value='service']").prop("checked",true);
        }
        $("#btnClear").on('click', function(e){
            e.preventDefault();
            clearForms();
        });

        $("#btnSubmit").on('click', function(e){
            e.preventDefault(); 
            $('#alertErrorCalculate').hide();
            $("#importTargetForm").attr('action','{{ route("target.calculateTargets") }}');
            $('#btnSubmit').hide();
            $('#btnSubmitLoad').show();
            $('#btnSubmitLoad').prop('disabled', true);
            $('#centre').val($( "#centre_id option:selected" ).text());
            $('#employee').val($( "#employee_id option:selected").text());
            $('#monthYear').val($( "#monthYearPicker").val());

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
                    if(textStatus === 'success') {
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
                    timeOutAlert($('#alertErrorCalculate')); 
                }

            });
            
        });
        
        var d = new Date();
        var textMonthYear = (d.getMonth()+1) + '/' + d.getFullYear()   ;  
        $('#monthYearPicker').val(textMonthYear);
        // Default functionality.
        $('#monthYearPicker').MonthPicker();

        var textYear = d.getFullYear();  
        $('#yearTargetPicker').val(textYear);
        $('#yearTargetPicker').datepicker({
            changeMonth: false,
            changeYear: true,
            showButtonPanel: true,
            closeText:'Select',
            currentText: 'This year',
            onClose: function(dateText, inst) {
                $(this).val($.datepicker.formatDate("yy", new Date(inst['selectedYear'], 0, 1)));
            },
        });
                        
        $("#targetInputFile").on('change', function () {
            document.getElementById("fileuploadurl").value = this.value.replace(/C:\\fakepath\\/i, '');
            $("#importTargetForm").attr('action','{{ route("target.import") }}');
            $("#importTargetForm").attr('enctype',"multipart/form-data");
            
            $('#btnImportTargets').hide();
            $('#targetInputFileLoad').show();

            $('#yearTarget').val($("#yearTargetPicker").val());
            $("#importTargetForm").submit();
        });

        $("#targetInputSalesFile").on('change', function () {
            document.getElementById("fileuploadurl").value = this.value.replace(/C:\\fakepath\\/i, '');
            $("#importTargetForm").attr('action','{{ route("target.importSales") }}');
            $("#importTargetForm").attr('enctype',"multipart/form-data");
            
            $('#btnImportSales').hide();
            $('#targetInputFileLoad').show();
            $("#importTargetForm").submit();
        });

        $("#btnTracingTargets").on('click', function(e){
            e.preventDefault(); 
            $("#btnTracingTargets").html("<span class='spinner-border spinner-border-sm' role='status' aria-hidden='true'></span> Obteniendo datos...");
            params = {};
            params["_token"]        = "{{ csrf_token() }}";
            params["yearTarget"]    = $("#yearTargetPicker").val();
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
                    if(textStatus === 'success') {
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
                complete: function(){
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