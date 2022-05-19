@extends('layouts.logged')

@section('content')
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
<div id="alertErrorServiceIncentive" class="alert alert-danger" role="alert" style="display: none">
</div>
<div id="alertServiceIncentive" class="alert alert-success" role="alert" style="display: none">
</div>


@include('inc.navbar')

<style>
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

    #fileuploadurl {
        border: none;
        font-size: 12px;
        padding-left: 0;
        width: 250px;
        margin-left: 25px;
    }
</style>
<div class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header card-header-danger">
                <h4 class="card-title">Configuración</h4>
            </div>
            <div class="card-body">
                <div class="row col-md-12 mb-3 justify-between">
                    <div class="row col-lg-8 col-md-5">
                        <div class="form-group col-md-5">
                            <div class="dropdown bootstrap-select">
                                <select class="selectpicker" name="centre_id" id="centre_id" data-size="7" data-style="btn btn-red-icot btn-round" title=" Seleccione Centro" tabindex="-98">
    
                                    @foreach ($centres as $centre)
                                    <option value="{{$centre->id}}">{{$centre->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
    
                        <div class="form-group col-md-5">
                            <div class="dropdown bootstrap-select">
                                <select class="selectpicker" name="service_id" id="service_id" data-size="7" data-style="btn btn-red-icot btn-round" title=" Seleccione Servicio" tabindex="-98">
    
                                    @foreach ($services as $service)
                                    <option value="{{  $service->id  }}">{{$service->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mt-2">
                        <button id="btnClear" href="#" class="btn btn-fill btn-warning">
                            {{ __('Limpiar formulario') }}
                        </button>
                        <button id="btnSubmit" type="submit" class="btn btn-fill">{{ __('Buscar') }}</button>
                        <button id="btnSubmitLoad" type="submit" class="btn btn-red-icot" style="display: none">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            {{ __('Obteniendo datos...') }}
                        </button>
                    </div>

                </div>

        </div>

    </div>
    <div class="col-md-12 mb-3 ">
        <table class="table table-bordered services-datatable" style="width:100%;">
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
                    <th>Fecha baja</th>
                    <th>Acciones </th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>

<script type="text/javascript">
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
        data: 'cancellation_date',
        name: 'cancellation_date'
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
        }
        $("#btnClear").on('click', function(e) {
            e.preventDefault();
            clearForms();
        });

        getServiceIncentives();
        $("#targetInputIncentiveFile").on('change', function() {
            document.getElementById("fileuploadurl").value = this.value.replace(/C:\\fakepath\\/i, '');
            $("#importTargetForm").attr('action', '{{ route("target.importIncentive") }}');
            $("#importTargetForm").attr('enctype', "multipart/form-data");

            $('#btnImportIncentives').hide();
            $('#targetInputFileLoad').show();
            $("#importTargetForm").submit();
        });

        $("#btnExportIncentives").on('click', function(e) {
            console.log('obteniendo documento de servicios');
            $('#targetExportFileLoad').show();
            $("#btnExportIncentives").hide();
            exportServicesToExcel();
        });


        $("#btnSubmit").on('click', function(e) {
            e.preventDefault();
            $('#btnSubmit').hide();
            $('#btnSubmitLoad').show();
            $('#btnSubmitLoad').prop('disabled', true);
            getServiceIncentives();
        });

    });

    function exportServicesToExcel() {

        $.ajax({
            url: "{{ route('services.exportServicesIncentivesActives') }}",
            type: 'GET',
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
            success: function(response, textStatus, jqXHR) {
                // if success, HTML response is expected, so replace current
                if (textStatus === 'success') {
                    var link = document.createElement('a');
                    var d = new Date();
                    var m = String(d.getMonth() + 1).padStart(2, '0');
                    var currentDate = d.getDate() + '-' + m + '-' + d.getFullYear();
                    filename = 'servicios-' + currentDate + '.xls';
                    link.href = URL.createObjectURL(response);
                    link.download = filename;
                    link.click();
                    $("#btnExportIncentives").show();
                    $('#targetExportFileLoad').hide();
                }
            },
            error: function(xhr, status, error) {
                var response = JSON.parse(xhr.responseText);

                $('#alertErrorServiceIncentive').text('Ha ocurrido un error al exportar los servicios, contacte con el administrador');
                $('#alertErrorServiceIncentive').show();
                $("#btnExportIncentives").show();
                $('#targetExportFileLoad').hide();
            }

        }).fail(function(jqXHR, textStatus, errorThrown) {
            //alert('Error cargando servicios');
        });
    }

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
                    "url": "{{ asset('dataTables/Spanish.json') }}"
                },
                ajax: {
                    url: '{{ route("services.incentives") }}',
                    type: "POST",
                    data: function(d) {
                        d.centre = $('#centre_id option:selected').val(),
                            d._token = "{{ csrf_token() }}",
                            d.search = $('input[type="search"]').val()
                        d.service = $("#service_id option:selected").val()
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
        table.columns.adjust().draw();
    }



    function destroyIncentive(servicePriceId) {
        $('#alertErrorServiceIncentive').hide();
        $('#alertServiceIncentive').hide();
        params = {};
        params["_token"] = "{{ csrf_token() }}";
        params["serviceprice_id"] = servicePriceId;
        $.ajax({
            url: "{{ route('services.destroyIncentive') }}",
            type: 'post',
            data: params,
            success: function(response, textStatus, jqXHR) {
                // if success, HTML response is expected, so replace current
                if (textStatus === 'success') {
                    $('#alertServiceIncentive').text(response.mensaje);
                    $('#alertServiceIncentive').show();
                    table.ajax.reload();
                }
            },
            error: function(xhr, status, error) {
                var response = JSON.parse(xhr.responseText);

                $('#alertErrorServiceIncentive').text(response.mensaje);
                $('#alertErrorServiceIncentive').show();
                $('#btnSubmitLoad').hide();
                $('#btnSubmit').show();
            }

        }).fail(function(jqXHR, textStatus, errorThrown) {
            //alert('Error cargando servicios');
        });
    }
</script>

@endsection