@extends('layouts.logged')

@section('content')
@include('inc.navbar')
@include('common.alert')

<link rel="stylesheet" href="{{ asset('/css/buttons.css') }}">
<link rel="stylesheet" href="{{ asset('/css/services.css') }}">
<div class="content">
    <div class="container-fluid">
        @if ($user -> rol_id == 1)
        <div class="row col-md-12 mb-3 ">
            <div class="col-md-8">
            </div>
            <div class="col-md-4 text-right">
                <a href="{{ route('services.create') }}" id="btnNewCenter" class="header-btn-add" ><span class="material-icons">add</span></a>
            </div>
        </div>   
        @endif
        <table class="table  table-striped table-bordered services-datatable">
        <div class="col-md-2">
        </div>
            <thead class="table-header">
                <tr>
                <th>Nombre</th>
                <th>Categoría</th>
                <th>Fecha baja</th>
                <th>Acciones</th>
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
            $("#message-validation").html('Está a punto de eliminar este Servicio ¿Confirmar?');
            $("#modal-title").html('ELIMINACIÓN');
    
        $("#id").val(id);
        $("#modal-validate").modal('show');
    }
    var table;

    $(function () {
        
        $(".nav-item").each(function(){
            $(this).removeClass("active");
        });
        $('#pagesConfig').addClass('show');
        $('#adminService').addClass('active')

        $("#btnConfirmRequest").on('click', function(event) {
            destroy();
        });
        
         table = $('.services-datatable').DataTable({
            processing: true,
            serverSide: true,
            language:{
                "url": "{{ asset('dataTables/Spanish.json') }}"
            },
            ajax: {
                url: "{{ route('services.index') }}",
                data: function (d) {
                    d.search = $('input[type="search"]').val()
                }
            },
            columnDefs: [{
                    targets: [-1,0,1,2],
                     visible: true,
                    className: 'dt-body-center'
                }
            ],
            columns: [ 
                {data: 'name', name: 'name'},
                {data: 'category', name: 'category'},
                {data: 'cancellation_date', name: 'cancellation_date'},
                {
                    data: 'action', 
                    name: 'action', 
                    orderable: true, 
                    searchable: true
                },
            ],
            search: {
                "regex": true,
                "smart":true
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
    });

    function destroy() {
       
       params = {};
       params["_token"] = "{{ csrf_token() }}";
       params["id"] = $("#id").val();

       $.ajax({
           url:  'services/destroy/' + params['id'],
           type: 'get',
           data: params,
           success: function(response, textStatus, jqXHR) {
               // if success, HTML response is expected, so replace current
               if (textStatus === 'success') {
                   $('#alertCentre').text(response.mensaje); 
                   $('#alertCentre').show().delay(2000).slideUp(300);
                   $("#modal-validate").modal('hide');
                   table.ajax.reload();
               }
           },
           complete: function() {
                   $("#modal-validate").modal('hide');
                   table.ajax.reload();
                   },
           error: function(xhr, status, error) {
               var response = JSON.parse(xhr.responseText);
               $('#alertErrorCentre').text(response.mensaje); 
               $('#alertErrorCentre').show().delay(2000).slideUp(300); 
           }

       }).fail(function(jqXHR, textStatus, errorThrown) {
        alert('Error'+jqXHR.responseText);
       });
   }
</script>

@endsection

