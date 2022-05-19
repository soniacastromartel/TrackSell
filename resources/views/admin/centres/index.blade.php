@extends('layouts.logged')

@section('content')
@if ($message = Session::get('success'))

    <div class="alert alert-success">

        <p>{{ $message }}</p>

    </div>

@endif  
@if ($message = Session::get('error'))

    <div class="alert alert-danger">

        <p>{{ $message }}</p>

    </div>

@endif
@include('inc.navbar')

<div class="content">
    <div class="container-fluid">
        @if ($user -> rol_id == 1)
        <div class="row col-md-12 mb-3 ">
            <div class="col-md-8">
            </div>
            <div class="col-md-4 text-right">
                <a href="{{ route('centres.create') }}" id="btnNewCenter" class="btn btn-red-icot btn-lg" > Nuevo</a>
            </div>
        </div>    
        @endif
        <table class="table table-bordered centres-datatable">
            <thead class="table-header">
                <tr>
                <th>Centro</th>
                <th>Dirección</th>
                <th>Teléfono</th>
                <th>Email</th>
                <th>Horario</th>
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
        $('#adminCentre').addClass('active');
        
        var table = $('.centres-datatable').DataTable({
            processing: true,
            serverSide: true,
            language:{
                "url": "{{ asset('dataTables/Spanish.json') }}"
            },
            ajax: {
                url: "{{ route('centres.index') }}",
                data: function (d) {
                    d.search = $('input[type="search"]').val()
                }
            },
            columns: [ 
                {data: 'name', name: 'name'},
                {data: 'address', name: 'address'},
                {data: 'phone', name: 'phone'},
                {data: 'email', name: 'email'},
                {data: 'timetable', name: 'timetable'},
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