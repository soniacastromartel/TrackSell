@extends('layouts.logged')

@section('content')

@include('inc.navbar')

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
<div id="alertErrorTrackingDate" class="alert alert-danger" role="alert" style="display: none">
</div>
<div id="alertTrackingDate" class="alert alert-success" role="alert" style="display: none">
</div>

<div class="content">
    <div class="container-fluid">
        <div class="row col-md-12">
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
</div>
<style>
    .col-md-12{
        padding: 0;
    }
    .row{
        justify-content: center;
        margin: 0;
    }
 
      
</style>

<script type="text/javascript">
    var table; 
    $(function () {
        $(".nav-item").each(function(){
            $(this).removeClass("active");
        });
        $("#btnCheckDelete").text('Buscar');
        $('#trackingRemove').addClass('active');
        $('#pagesTracking').addClass('show');

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
                    targets: 9,
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


    function destroy(trackingId) {
            $.ajax({
                    url: 'destroy/' +  trackingId,
                    type: 'get',
                    success: function(response, textStatus, jqXHR) {
                        // if success, HTML response is expected, so replace current
                        if(textStatus === 'success') {
                            $('#alertTrackingDate').text(response.mensaje); 
                            $('#alertTrackingDate').show();
                            table.ajax.reload();
                        }
                    },
                    error: function(xhr, status, error) {
                        var response = JSON.parse(xhr.responseText);
                        $('#alertErrorTrackingDate').text(response.mensaje); 
                        $('#alertErrorTrackingDate').show(); 
                    }

            }).fail(function(jqXHR, textStatus, errorThrown) {
                
                    //alert('Error cargando servicios');
                
            });
        }
</script>
@endsection