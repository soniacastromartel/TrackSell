@extends('layouts.logged')
@section('content')
@include('inc.navbar')
@include('common.alert')

<link rel="stylesheet" href="{{ asset('/css/buttons.css') }}">
<link rel="stylesheet" href="{{ asset('/css/tracking.css') }}">
<link rel="stylesheet" href="{{ asset('/css/modal.css') }}">

<div id="alertErrorTrackingDate" class="alert alert-danger" role="alert" style="display: none">
</div>
<div id="alertTrackingDate" class="alert alert-success" role="alert" style="display: none">
</div>

<div class="content">
    <div class="container-fluid" >
        <div class="row ">
            <div class="col-lg-12 mb-5" style="margin-top: 120px">
                <div class="card " style="min-height: 200px">
                    <div class="card-header card-header-danger">
                        <h4 class="card-title">Borrar seguimiento</h4>
                    </div>
                    <div class="d-flex justify-content-end">
                     <img src="/assets/img/deleteServices.png" width="300">
                    </div>
                </div>
            </div>
        </div>

                <table class="table  table-striped table-bordered  tracking-delete-datatable">
                    <thead  class="table-header">
                        <th>Centro Prescriptor</th>
                        <th>Empleado </th>
                        <th>H.C.</th>
                        <th>Paciente</th>
                        <th>Servicio</th>
                        <th>Cita</th>
                        <th>Fecha Servicio</th>
                        <th>Fecha Facturación</th>
                        <th>Fecha Validación</th>
                        <th>Acciones</th>
                    </thead>
                    <tbody>
                    </tbody>
                </table>  
      
    </div>
</div>

<div class="modal" tabindex="-1" role="dialog" id="modal-validate">
    <input type="hidden" id="id" />
    <input type="hidden" id="validateVal" />
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-title"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <p id="message-validation" class="px-4 text-center"  style= "margin-bottom: 20px;"></p>
                <label class="label" for="reason">Motivo: </label>
                <input type="text" name="reason" id="reason" style= "margin-bottom: 20px;"placeholder="Escriba el motivo..." 
                value="{{ isset($tracking) ? $tracking->cancellation_reason : ''}}" oninput="checkReason()"></input>
            </div>

            <div class="modal-footer center">
                <button id="btnConfirmRequest" type="button" class="btn btn-red-icot btn-round" disabled>SI</button>
                <button id="btnCancelRequest" type="button" class="btn btn-default btn-round" data-dismiss="modal" disabled>NO</button>
                </p>
            </div>

        </div>
    </div>
</div>

<style>
    .col-md-12{
        padding: 0;
    }
    .row{
        justify-content: center;
        margin: 0;
    }
    table.dataTable.dataTable_width_auto {
        width: 100%;
    }
 
      
</style>

<script type="text/javascript">

function confirmRequest(state, id) {
        if (state == 0) {
            $("#message-validation").html('Está a punto de eliminar este Seguimiento ¿Confirmar?');
            $("#modal-title").html('ELIMINACIÓN');
        }
        $("#validateVal").val(state);
        $("#id").val(id);
        $("#modal-validate").modal('show');
        checkReason();
    }

    function checkReason() {
        const reason = $('#reason').val().trim();
        const isReasonFilled = reason.length > 0;
        $('#btnConfirmRequest').prop('disabled', !isReasonFilled);
        $('#btnCancelRequest').prop('disabled', !isReasonFilled);
    }

    var table; 
    $(function () {
        $(".nav-item").each(function(){
            $(this).removeClass("active");
        });
        $("#btnCheckDelete").text('Buscar');
        $('#trackingRemove').addClass('active');
        $('#pagesTracking').addClass('show');

        $("#btnConfirmRequest").on('click', function(event) {
            destroy();
        });

        loadDeleteTable();

        function loadDeleteTable() {
            var url = "{{ route('tracking.searchDelete') }}";
            //var params = getParams();
            table = $('.tracking-delete-datatable').DataTable({
                processing: true,
                serverSide: true,
                language:{
                    "url": "{{ asset('dataTables/Spanish.json') }}"
                },
                ajax: {
                    url: url,
                    type: "POST",
                    data: function(d) {
                        d._token = "{{ csrf_token() }}",
                            d.search = $('input[type="search"]').val()

                    }
                },
                columnDefs: [{
                    targets: [-1,0,1,2,3,4,5,6,7,8],
                    visible: true,
                    className: 'dt-body-center'
                }
            ],
                columns: [ 
                    {data: 'centre', name: 'centre'},
                    {data: 'employee', name: 'employee'},
                    {data: 'hc', name: 'hc'},
                    {data: 'patient_name', name: 'patient_name'},
                    {data: 'service', name: 'service'},
                    {data: 'apointment_date', name: 'apointment_date'},
                    {data: 'service_date', name: 'service_date'},
                    {data: 'invoiced_date', name: 'invoiced_date'},
                    {data: 'validation_date', name: 'validation_date'},
                    {data: 'action', name: 'action', searchable: true}
                ],
                search: {
                    "regex": true,
                    "smart":true,
                },
                initComplete: function () {
                    this.api().columns().every(function () {
                        var column = this;
                        var input = document.createElement("input");
                        $(input).appendTo($(column.footer()).empty())
                        .on('change', function () {
                            var val = $.fn.dataTable.util.escapeRegex($(this).val());
                            column
                                    .search( val ? '^'+val+'$' : '', true, false )
                                    .draw();
                        });  
                    });
                }
            });
        }
    });

    function destroy() {
        trackingId= $("#id").val();
        var reason = $("#reason").val().trim();
        if (!reason.trim()) {
            alert('Debe escribir un motivo antes de confirmar.');
            return;
        }
        console.log($("#reason").val());
        console.log($("#id").val());

        var params = {
            reason: reason,
            id: trackingId
        };

            $.ajax({
                url: 'destroy/' + trackingId,
                    type: 'get',
                    data: params,
                    success: function(response, textStatus, jqXHR) {
                        // if success, HTML response is expected, so replace current
                        if(textStatus === 'success') {
                            $('#alertTrackingDate').text(response.mensaje); 
                            $('#alertTrackingDate').show().delay(2000).slideUp(300);
                            table.ajax.reload();
                        }
                    },
                    complete: function() {
                    $("#modal-validate").modal('hide');
                    table.ajax.reload();
                    },
                    error: function(xhr, status, error) {
                        var response = JSON.parse(xhr.responseText);
                        $('#alertErrorTrackingDate').text(response.mensaje); 
                        $('#alertErrorTrackingDate').show().delay(2000).slideUp(300); 
                    }
            }).fail(function(jqXHR, textStatus, errorThrown) {
                alert('Error'+jqXHR.responseText);
            });
        }
</script>
@endsection





