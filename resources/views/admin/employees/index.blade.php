@extends('layouts.logged')
@section('content')
    @include('inc.navbar')
    @include('common.alert')

    <link rel="stylesheet" href="{{ asset('/css/employee.css') }}">

    <div class="alert alert-danger" id="alertErrorChangeEmployee" role="alert" style="display: none">
    </div>
    <div class="alert alert-success" id="alertChangeEmployee" role="alert" style="display: none">
    </div>

    <div class="content">
        <div class="container-fluid">
            <div class="row col-md-12 mb-3">
                <div class="col-md-8">
                </div>
                <div class="col-md-4 text-right" id="blockNewTracking">
                    <a id="btnSyncA3" class="btn btn-raised"><span class="material-icons mr-1">
                            sync
                        </span> Sincronizar A3</a>
                    <button id="btnSubmitLoad" type="submit" class="btn btn-raised" style="display: none">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        {{ __('Realizando sincronización...') }}
                    </button>
                </div>
            </div>
            <table class="table-striped table-bordered employees-datatable col-md-12 table">
                <thead class="table-header">
                    <tr>
                        <th>NIF</th>
                        <th>Nombre</th>
                        <th>Login</th>
                        <th>Centro</th>
                        <th>Categoría</th>
                        <!-- <th>Permisos</th> -->
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>


    <script type="text/javascript">
        var table
        $(function() {

            $(".nav-item").each(function() {
                $(this).removeClass("active");
            });
            $('#pagesConfig').addClass('show');
            $('#adminUser').addClass('active');

            table = $('.employees-datatable').DataTable({
                order: [
                    [1, "asc"]
                ],
                processing: false,
                responsive: true,
                serverSide: true,
                language: {
                    "url": "{{ asset('dataTables/Spanish.json') }}"
                },
                ajax: {
                    url: "{{ route('employees.index') }}",
                    data: function(d) {
                        //d.status = $('#status').val(),
                        d.search = $('input[type="search"]').val()
                    }
                },
                columnDefs: [{
                        targets: '_all',
                        visible: true,
                        className: 'dt-body-center'
                    },
                    {
                        targets: 4,
                        className: 'upper'
                    },
                    {
                        targets: 4,
                        data: "category",
                        render: function(data, type, row) {
                            if (data != null) {

                                return data.toUpperCase();

                                // return data.split(' ')[1];
                            } else {
                                return data;
                            }

                        }
                    }
                ],
                columns: [{
                        data: 'dni',
                        name: 'dni'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'username',
                        name: 'username'
                    },
                    {
                        data: 'centre',
                        name: 'centre'
                    },
                    {
                        data: 'category',
                        name: 'category'
                    },
                    // {
                    //     data: 'role',
                    //     name: 'role'
                    // },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: true,
                        searchable: true
                    },

                ],
                createdRow: function(row, data, dataIndex) {
                    console.log(data);
                    var tooltipMessage = "";
                    if (parseInt(data.count_access) === 3) {
                        $(row).addClass('user-bloqued');
                        tooltipMessage = "Usuario bloqueado";
                    } else if (parseInt(data.pending_password) === 0) {
                        if (data.updated_at) {
                            var resetDateTimeString = data.updated_at.split(" GMT")[0];
                            var resetDate = new Date(resetDateTimeString);
                            var currentDate = new Date();
                            var differenceInHours = Math.abs(currentDate - resetDate) / (1000 * 60 * 60);
                            if (differenceInHours <= 24) {
                                $(row).addClass('user-updated-pass');
                                tooltipMessage = "Asignación de nueva contraseña en las últimas 24h";
                            } else {
                                $(row).removeClass('user-updated-pass');
                            }
                        }
                    } else if (parseInt(data.count_access) === 0) {
                        if (data.updated_at) {
                            var resetDateTimeString = data.updated_at.split(" GMT")[0];
                            var resetDate = new Date(resetDateTimeString);
                            var currentDate = new Date();
                            var differenceInHours = Math.abs(currentDate - resetDate) / (1000 * 60 * 60);
                            if (differenceInHours <= 24) {
                                $(row).addClass('user-updated-acc');
                                tooltipMessage = "Reseteo de acceso en las últimas 24h";
                        } else {
                            $(row).removeClass('user-updated-acc');
                        }
                    } else {
                        $(row).removeClass('user-updated-acc');
                    }
                }
                if(tooltipMessage) {
                     $(row).attr('data-tooltip', tooltipMessage);
                    }
                  },


                search: {
                    "regex": true,
                    "smart": true,
                },
                initComplete: function() {
                    this.api().columns().every(function() {
                        var column = this;
                        var input = document.createElement("input");
                        $(input).appendTo($(column.footer()).empty())
                            .on('change', function() {
                                var val = $.fn.dataTable.util.escapeRegex($(this).val());
                                //column.search(val ? val : '', true, false).draw();
                                column
                                    .search(val ? '^' + val + '$' : '', true, false)
                                    .draw();
                            });

                    });
                }
            });

            $("#btnSyncA3").on('click', function() {
                syncA3(null, 'full');
            });
        });


        function resetAccessApp(employeeId, back) {
            $('#alertChangeEmployee').hide();
            $('#alertErrorChangeEmployee').hide();
            $('#btnResetAccess' + employeeId + ' .material-icons').hide();
            $('#btnResetAccess' + employeeId + ' .spinner-border').show();

            params = {};
            params["_token"] = "{{ csrf_token() }}";
            params["employee_id"] = employeeId;
            $.ajax({
                url: "{{ route('employees.resetAccessApp') }}",
                type: 'post',
                data: params,
                success: function(response, textStatus, jqXHR) {
                    // if success, HTML response is expected, so replace current
                    if (textStatus === 'success') {
                        $('#alertChangeEmployee').text(response.mensaje);
                        $('#alertChangeEmployee').show().delay(2000).slideUp(300);
                        table.ajax.reload();
                        $('#btnResetAccess' + employeeId + ' .material-icons').show();
                        $('#btnResetAccess' + employeeId + ' .spinner-border').hide();
                    }
                },
                error: function(xhr, status, error) {
                    var response = JSON.parse(xhr.responseText);
                    $('#alertErrorChangeEmployee').text(response.mensaje);
                    $('#alertErrorChangeEmployee').show().delay(2000).slideUp(300);
                    $('#btnResetAccess' + employeeId + ' .material-icons').show();
                    $('#btnResetAccess' + employeeId + ' .spinner-border').hide();
                }

            }).fail(function(jqXHR, textStatus, errorThrown) {
                alert('Error cargando servicios');
                $('#btnResetAccess' + employeeId + ' .material-icons').show();
                $('#btnResetAccess' + employeeId + ' .spinner-border').hide();
            });
        }

        function resetPassword(employeeId, back) {
            $('#alertChangeEmployee').hide();
            $('#alertErrorChangeEmployee').hide();
            $('#btnResetPass' + employeeId + ' .material-icons').hide();
            $('#btnResetPass' + employeeId + ' .spinner-border').show();
            params = {};
            params["_token"] = "{{ csrf_token() }}";
            params["employee_id"] = employeeId;
            $.ajax({
                url: "{{ route('employees.resetPassword') }}",
                type: 'post',
                data: params,
                success: function(response, textStatus, jqXHR) {
                    // if success, HTML response is expected, so replace current
                    if (textStatus === 'success') {
                        $('#alertChangeEmployee').text(response.mensaje);
                        $('#alertChangeEmployee').show().delay(2000).slideUp(300);
                        table.ajax.reload();
                        $('#btnResetPass' + employeeId + ' .material-icons').show();
                        $('#btnResetPass' + employeeId + ' .spinner-border').hide();
                    }
                },
                error: function(xhr, status, error) {
                    var response = JSON.parse(xhr.responseText);
                    $('#alertErrorChangeEmployee').text(response.mensaje);
                    $('#alertErrorChangeEmployee').show().delay(2000).slideUp(300);
                    $('#btnResetPass' + employeeId + ' .material-icons').show();
                    $('#btnResetPass' + employeeId + ' .spinner-border').hide();

                }

            }).fail(function(jqXHR, textStatus, errorThrown) {
                alert('Error cargando servicios');
                $('#btnResetPass' + employeeId + ' .material-icons').show();
                $('#btnResetPass' + employeeId + ' .spinner-border').hide();
            });
        }

        function denyAccess(employeeId, back) {
            $('#alertChangeEmployee').hide();
            $('#alertErrorChangeEmployee').hide();
            $('#btnDenyAccess' + employeeId + ' .material-icons').hide();
            $('#btnDenyAccess' + employeeId + ' .spinner-border').show();
            params = {};
            params["_token"] = "{{ csrf_token() }}";
            params["employee_id"] = employeeId;
            $.ajax({
                url: "{{ route('employees.denyAccessApp') }}",
                type: 'post',
                data: params,
                success: function(response, textStatus, jqXHR) {
                    // if success, HTML response is expected, so replace current
                    if (textStatus === 'success') {
                        $('#alertChangeEmployee').text(response.mensaje);
                        $('#alertChangeEmployee').show().delay(2000).slideUp(300);
                        table.ajax.reload();
                        $('#btnDenyAccess' + employeeId + ' .material-icons').show();
                        $('#btnDenyAccess' + employeeId + ' .spinner-border').hide();
                    }
                },
                error: function(xhr, status, error) {
                    var response = JSON.parse(xhr.responseText);
                    $('#alertErrorChangeEmployee').text(response.mensaje);
                    $('#alertErrorChangeEmployee').show().delay(2000).slideUp(300)();
                    $('#btnDenyAccess' + employeeId + ' .material-icons').show();
                    $('#btnDenyAccess' + employeeId + ' .spinner-border').hide();
                }


            }).fail(function(jqXHR, textStatus, errorThrown) {
                alert('Error cargando servicios');
                $('#btnDenyAccess' + employeeId + ' .material-icons').show();
                $('#btnDenyAccess' + employeeId + ' .spinner-border').hide();
            });

        }

        function syncA3(employeeId, type) {
            params = {};
            params["_token"] = "{{ csrf_token() }}";
            params["employee_id"] = employeeId;
            params["type"] = type;

            if (employeeId == null) {
                $('#btnSyncA3').hide();
                $('#btnSubmitLoad').show();

            } else {
                $('#btnSyncA3_' + employeeId + ' .material-icons').hide();
                $('#btnSyncA3_' + employeeId + ' .spinner-border').show();
            }
            $('#alertChangeEmployee').hide();
            $('#alertErrorChangeEmployee').hide();
            $.ajax({
                url: "{{ route('employees.syncA3') }}",
                type: 'post',
                data: params,
                success: function(response, textStatus, jqXHR) {
                    // if success, HTML response is expected, so replace current
                    if (textStatus === 'success') {
                        if (type == 'only') {
                            $('#btnSyncA3_' + employeeId + ' .material-icons').show();
                            $('#btnSyncA3_' + employeeId + ' .spinner-border').hide();
                        } else {
                            $('#btnSubmitLoad').hide();
                            $('#btnSyncA3').show();
                        }
                        table.ajax.reload();
                    }
                },
                error: function(xhr, status, error) {
                    var response = JSON.parse(xhr.responseText);
                    timeOutAlert($('#alertErrorChangeEmployee'), response);
                    window.location = response.url;
                }

            }).fail(function(jqXHR, textStatus, errorThrown) {
                timeOutAlert($('#alertErrorChangeEmployee'), jqXHR.responseText);
            });
        }


        function timeOutAlert($alert, $message) {
            $alert.text($message);
            $alert.show().delay(2000).slideUp(300);
        }
    </script>
@endsection
