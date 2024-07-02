@extends('layouts.logged')
@section('content')
    <link rel="stylesheet" href="{{ asset('/css/buttons.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/roles.css') }}">


    <div class="content">
        <div class="container-fluid">
            <div class="row col-md-12 mb-3">
                <div class="col-md-8">
                </div>
                <div class="col-md-4 text-right">
                    <a href="{{ route('roles.create') }}" id="btnNewCenter" class="header-btn-add"><span
                            class="material-icons">
                            add</span></a>
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
            $("#id").val(id);
            Swal.fire({
                title: '¿Está seguro?',
                text: "Está a punto de eliminar este Rol. ¡No podrás revertir esto!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminarlo!',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    destroy();
                }
            });
        }
        var table;
        $(function() {

            $(".nav-item").each(function() {
                $(this).removeClass("active");
            });
            $('#pagesConfig').addClass('show');
            $('#adminRole').addClass('active');
            // $("#btnConfirmRequest").on('click', function(event) {
            //     destroy();
            // });

            table = $('.roles-datatable').DataTable({
                processing: true,
                serverSide: true,
                language: {
                    "url": "{{ asset('dataTables/Spanish.json') }}"
                },
                ajax: {
                    url: "{{ route('roles.index') }}",
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
                        data: 'description',
                        name: 'description'
                    },
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

        function destroy() {
            var params = {};
            params["_token"] = "{{ csrf_token() }}";
            params["id"] = $("#id").val();

            $.ajax({
                url: 'roles/destroy/' + params['id'],
                type: 'get',
                data: params,
                success: function(response) {
                    if (response.success) {
                        $("#modal-validate").modal('hide');
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
