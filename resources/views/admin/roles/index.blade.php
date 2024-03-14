@extends('layouts.logged')
@section('content')
@include('inc.navbar')
@include('common.alert')

<link rel="stylesheet" href="{{ asset('/css/background.css') }}">

<div id="alertErrorRole" class="alert alert-danger" role="alert" style="display: none">
</div>
<div id="alertRole" class="alert alert-success" role="alert" style="display: none">
</div>

<div class="content">
    <div class="container-fluid">
        <div class="row col-md-12 mb-3 ">
            <div class="col-md-8">
            </div>
            <div class="col-md-4 text-right">
                <a href="{{ route('roles.create') }}" id="btnNewCenter" class="btn btn-red-icot btn-lg" ><span class="material-icons">
                            add_circle</span> Nuevo</a>
            </div>
        </div>     
        <table class="table table-striped table-bordered roles-datatable">
            <thead class="table-header">
                <tr>
                    <th>Nombre</th>
                    <th>Descripcion</th>
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
            $("#message-validation").html('Está a punto de eliminar este Rol ¿Confirmar?');
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
        $('#adminRole').addClass('active');
        $("#btnConfirmRequest").on('click', function(event) {
            destroy();
        });
        
         table = $('.roles-datatable').DataTable({
            processing: true,
            serverSide: true,
            language:{
                "url": "{{ asset('dataTables/Spanish.json') }}"
            },
            ajax: {
                url: "{{ route('roles.index') }}",
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
                {data: 'description', name: 'description'},
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
           url:  'roles/destroy/' + params['id'],
           type: 'get',
           data: params,
           success: function(response, textStatus, jqXHR) {
               // if success, HTML response is expected, so replace current
               if (textStatus === 'success') {
                   $('#alertRole').text(response.mensaje); 
                   $('#alertRole').show().delay(2000).slideUp(300);
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
               $('#alertErrorRole').text(response.mensaje); 
               $('#alertErrorRole').show().delay(2000).slideUp(300); 
           }

       }).fail(function(jqXHR, textStatus, errorThrown) {
        alert('Error'+jqXHR.responseText);
       });
   }

    
</script>

@endsection