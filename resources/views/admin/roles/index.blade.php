@extends('layouts.logged')

@section('content')
@include('inc.navbar')
@include('common.alert')

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

<script type="text/javascript">
    $(function () {
        
        $(".nav-item").each(function(){
            $(this).removeClass("active");
        });
        $('#pagesConfig').addClass('show');
        $('#adminRole').addClass('active');
        
        var table = $('.roles-datatable').DataTable({
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
    
</script>

@endsection