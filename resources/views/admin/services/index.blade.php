@extends('layouts.logged')
@section('content')
    @include('inc.navbar')

    <link rel="stylesheet" href="{{ asset('/css/buttons.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/services.css') }}">
    <div class="content">
        <div class="container-fluid">
            @if ($user->rol_id == 1)
                <div class="row col-md-12 mb-3 ">
                    <div class="col-md-8">
                    </div>
                    <div class="col-md-4 text-right">
                        <a href="{{ route('services.create') }}" id="btnNewCenter" class="header-btn-add"><span
                                class="material-icons">add</span></a>
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
                    // console.log(xhr.responseText);
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
