@extends('layouts.logged')
@section('content')
    <link rel="stylesheet" href="{{ asset('/css/buttons.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/roles.css') }}">


    <div class="content">
        <div class="container-fluid">
            <div class="card" style="margin-top:120px ">
                <div class="card-header card-header-danger">
                    <h4 class="card-title">Roles</h4>
                </div>
                <div class="row col-lg-12">
                    <div class="col-md-11 header-logo" style="margin-top:70px;">
                        <a href="{{ route('roles.create') }}" id="btnNewRole" class="header-btn-add"><span id="icon-send"
                                class="material-icons">add_moderator</span><strong>NUEVO ROL</strong> </a>
                    </div>
                    <div class=" col-md-1" style="display:flex;justify-content:end;margin-top:100px; ">

                    </div>
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
        function confirmRequest(state, id) {
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
            $('#adminRole').addClass('active');

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

        function destroy(id) {
            var params = {};
            params["_token"] = "{{ csrf_token() }}";
            params["id"] = id;

            $.ajax({
                url: 'roles/destroy/' + params['id'],
                type: 'get',
                data: params,
                beforeSend: function() {
                    Swal.showLoading();
                },
                success: function(response) {
                    if (response.success) {
                        Swal.hideLoading();
                        table.ajax.reload();
                        showAlert('success', response.mensaje);
                    } else {
                        Swal.hideLoading();
                        showAlert('error', response.mensaje);
                    }
                },
                error: function(xhr, status, error) {
                    Swal.hideLoading();
                    var errorMessage = "Error occurred: " + xhr.responseText;
                    showAlert('error', errorMessage);
                }
            });
        }
    </script>
@endsection
