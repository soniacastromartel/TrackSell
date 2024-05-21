@extends('layouts.logged')
@section('content')
@include('inc.navbar')
@include('common.alert')


<link rel="stylesheet" href="{{ asset('/css/buttons.css') }}">
<link rel="stylesheet" href="{{ asset('/css/incentives.css') }}">
<div id="alertErrorServiceIncentive" class="alert alert-danger" role="alert" style="display: none">
</div>
<div id="alertServiceIncentive" class="alert alert-warning" role="alert" style="display: none">
</div>


<div class="content">
    <div class="container-fluid">
        <div class="card incentive-logo" style="margin-top:120px ">
            <div class="card-header card-header-danger">
                <h4 class="card-title">Configuración</h4>
            </div>
            <div class="card-body">
                <div class="row col-md-12 mb-3 justify-between">
                    <div class="row col-lg-8 col-md-5">
                        <div class="form-group col-md-4">
                            <div class="dropdown bootstrap-select">
                                <select class="selectpicker" name="centre_id" id="centre_id" data-size="7" data-style="btn btn-red-icot btn-round" title="Centro" tabindex="-98">
                                    @foreach ($centres as $centre)
                                    <option value="{{$centre->id}}">{{$centre->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
    
                        <div class="form-group col-md-4">
                            <div class="dropdown bootstrap-select">
                                <select class="selectpicker" name="service_id" id="service_id" data-size="7" data-style="btn btn-red-icot btn-round" title="Servicio" tabindex="-98">
                                    @foreach ($services as $service)
                                    <option value="{{  $service->id  }}">{{$service->name}}</option>
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
                        <button id="btnSubmitSearch" type="submit" class="btn-search-circle">
                        <span class="material-icons">
                            search
                        </span>
                    </button>
                        <button id="btnSubmitLoadSearch" type="submit" class="btn-search-circle" style="display: none">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
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
                    <!-- <th>Fecha baja</th> -->
                    <th>Acciones </th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>

@include('common.modal')

<script type="text/javascript">

    function confirmRequest(state, id) {
            $("#message-validation").html('Está a punto de eliminar este Incentivo ¿Confirmar?');
            $("#modal-title").html('ELIMINACIÓN');
    
        $("#id").val(id);
        $("#modal-validate").modal('show');
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
        $("#btnConfirmRequest").on('click', function(event) {
            destroyIncentive();
        });

        //al parecer esta función no se está usando . la url da un error.
        getServiceIncentives();
        $("#targetInputIncentiveFile").on('change', function() {
            document.getElementById("fileuploadurl").value = this.value.replace(/C:\\fakepath\\/i, '');
            $("#importTargetForm").attr('action', '{{ route("target.importIncentive") }}');
            $("#importTargetForm").attr('enctype', "multipart/form-data");
            $('#btnImportIncentives').hide();
            $('#targetInputFileLoad').show();
            $("#importTargetForm").submit();
        });

        //FIXME BOTON DE EXPORTAR?? DONDE ESTÁ?
        $("#btnExportIncentives").on('click', function(e) {
            console.log('obteniendo documento de servicios');
            $('#targetExportFileLoad').show();
            $("#btnExportIncentives").hide();
            exportServicesToExcel();
        });


        $("#btnSubmitSearch").on('click', function(e) {
            e.preventDefault();
            $('#btnSubmitSearch').hide();
            $('#btnSubmitLoadSearch').show();
            $('#btnSubmitLoadSearch').prop('disabled', true);
            getServiceIncentives();
        });

    });
 
    //FIXME donde se usa este método? puede ser útil?
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
                $('#alertErrorServiceIncentive').show().delay(2000).slideUp(300);
                $("#btnExportIncentives").show();
                $('#targetExportFileLoad').hide();
            }

        }).fail(function(jqXHR, textStatus, errorThrown) {
            alert('Error cargando servicios');
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
                    decimal: ',',
                    thousands: '.',
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
                        $('#btnSubmitSearch').show();
                        $('#btnSubmitLoadSearch').hide();
                        return json.data;
                    }
                },
                columnDefs: [{
                targets: [1,2,3,4,5,6,7,8],
                    visible: true,
                    className: 'dt-body-center'
                },
                {
                targets:  [2,3,4,5,6,7],
                render: $.fn.dataTable.render.number( '.', ',', 2) //columnDefs number renderer (thousands, decimal, precision, simbolo/moneda)
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



    function destroyIncentive() {
        $('#alertErrorServiceIncentive').hide();
        $('#alertServiceIncentive').hide();
        params = {};
        params["_token"] = "{{ csrf_token() }}";
        params["serviceprice_id"] = $("#id").val();
        $.ajax({
            url: "{{ route('services.destroyIncentive') }}",
            type: 'post',
            data: params,
            success: function(response, textStatus, jqXHR) {
                // if success, HTML response is expected, so replace current
                if (textStatus === 'success') {
                $('#alertServiceIncentive').text(response.mensaje);
                $("#alertServiceIncentive").fadeTo(2000, 500).slideUp(500, function(){
                $("#alertServiceIncentive").alert('close');
});

                    table.ajax.reload();
                }
            },
            complete: function() {
                    $("#modal-validate").modal('hide');
                    table.ajax.reload();
                    },
            error: function(xhr, status, error) {
                var response = JSON.parse(xhr.responseText);

                $('#alertErrorServiceIncentive').text(response.mensaje);
                $('#alertErrorServiceIncentive').show();
                $('#btnSubmitLoadSearch').hide();
                $('#btnSubmitSearch').show();
            }

        }).fail(function(jqXHR, textStatus, errorThrown) {
            alert('Error cargando servicios');
        });
    }
</script>

@endsection