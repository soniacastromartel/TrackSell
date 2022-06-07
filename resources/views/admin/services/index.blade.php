@extends('layouts.logged')

@section('content')
@include('inc.navbar')
@include('common.alert')

<div class="content">
    <div class="container-fluid">
        @if ($user -> rol_id == 1)
        <div class="row col-md-12 mb-3 ">
            <div class="col-md-8">
            </div>
            <div class="col-md-4 text-right">
                <a href="{{ route('services.create') }}" id="btnNewCenter" class="btn btn-red-icot btn-lg" ><span class="material-icons">
                            add_circle</span> Nuevo</a>
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


<script type="text/javascript">
    $(function () {
        
        $(".nav-item").each(function(){
            $(this).removeClass("active");
        });
        $('#pagesConfig').addClass('show');
        $('#adminService').addClass('active')
        
        var table = $('.services-datatable').DataTable({
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
</script>

@endsection