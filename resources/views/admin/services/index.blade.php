@extends('layouts.logged')
@section('content')
    @include('inc.navbar')

    <link rel="stylesheet" href="{{ asset('/css/buttons.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/services.css') }}">
    <div class="content">
        <div class="container-fluid">
            <div class="card" style="margin-top:120px ">
                <div class="card-header card-header-danger">
                    <h4 class="card-title">Servicios</h4>
                </div>
                <div class="row col-lg-12">
                    <div class="col-md-11 header-logo" style="margin-top:70px;">
                        <a href="{{ route('services.create') }}" id="btnNewService" class="header-btn-add">
                            <span id="icon-send" class="material-symbols-outlined">
                                assignment_add                                    </span><strong>NUEVO SERVICIO</strong></a>
                    </div>
                    <div class=" col-md-1" style="display:flex;justify-content:end;margin-top:100px; ">
                       
                    </div>
                </div>
            </div>
            <table class="table  table-striped table-bordered services-datatable">
                <div class="col-md-2">
                </div>
                <thead class="table-header">
                    <tr>
                        <th>Nombre</th>
                        <th>Categoría</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>


    <script type="text/javascript">
        // $("#id").val(id);

        function confirmRequest(state, id) {
            console.log(id);
            confirmedRequest().then((result) => {
                if (result.isConfirmed) {
                    destroy(id);
                }
            });
        }
        var table;

        $(function() {

            $(".nav-item").each(function() {
                $(this).removeClass("active");
            });
            $('#pagesConfig').addClass('show');
            $('#adminService').addClass('active')

            // $("#btnConfirmRequest").on('click', function(event) {
            //     destroy();
            // });

            table = $('.services-datatable').DataTable({
                processing: true,
                serverSide: true,
                language: {
                    "url": "{{ asset('dataTables/Spanish.json') }}"
                },
                ajax: {
                    url: "{{ route('services.index') }}",
                    data: function(d) {
                        d.search = $('input[type="search"]').val()
                    }
                },
                columnDefs: [{
                    targets: [-1, 0, 1, 2],
                    visible: true,
                    className: 'dt-body-center'
                }],
                columns: [{
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'category',
                        name: 'category'
                    },
                    // {data: 'cancellation_date', name: 'cancellation_date'},
                    {
                        data: 'action',
                        name: 'action',
                        orderable: true,
                        searchable: true
                    },
                ],
                search: {
                    "regex": true,
                    "smart": true
                },
                initComplete: function() {
                    this.api().columns().every(function() {
                        var column = this;
                        var input = document.createElement("input");
                        $(input).appendTo($(column.footer()).empty())
                            .on('change', function() {
                                var val = $.fn.dataTable.util.escapeRegex($(this).val());
                                column
                                    .search(val ? '^' + val + '$' : '', true, false)
                                    .draw();
                            });

                    });
                }
            });
        });

        function destroy(id) {
            console.log(id);
            var params = {};
            params["_token"] = "{{ csrf_token() }}";
            params["id"] = id;

            $.ajax({
           url:  'services/destroy/' + params['id'],
                type: 'get',
                data: params,
                success: function(response) {
                    if (response.success) {
                        table.ajax.reload();
                        Swal.fire({
                            title: '¡Perfecto!',
                            text: response.mensaje,
                            icon: 'success',
                            timer: 4000,
                            showConfirmButton: false,

                        });
                    } else {
                        Swal.fire({
                            title: '¡Error!',
                            text: response.mensaje,
                            icon: 'error',
                            timer: 4000,
                            showConfirmButton: false,
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.log(error);
                    var errorMessage = "Error occurred: " + xhr.responseText;
                    Swal.fire({
                        title: '¡Error!',
                        text: errorMessage,
                        icon: 'error',
                        timer: 4000,
                        showConfirmButton: false,
                    });
                }
            });
        }
    </script>
    
@endsection
