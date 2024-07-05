@extends('layouts.logged')
@section('content')
    @include('inc.navbar')

    <link rel="stylesheet" href="{{ asset('/css/buttons.css') }}">

    <div class="content">
        <div class="container-fluid">
            <div class="row col-md-12 mb-3">
                <div class="col-md-8">
                </div>
                <div class="col-md-4 text-right" id="blockNewTracking">
                    <a id="btnSyncA3" class="btn-sincro-all"><span class="material-icons">
                            sync
                        </span> </a>
                    <button id="btnSubmitLoadA3" type="submit" class="btn-sincro-all" style="display: none">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
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
                    {
                        data: 'action',
                        name: 'action',
                        orderable: true,
                        searchable: true
                    },

                ],
                createdRow: function(row, data, dataIndex) {
                    var tooltipMessage = "";
                    var resetDateTimeString, resetDate, currentDate = new Date();
                    var differenceInHours;

                    function calculateDifference(resetDateTimeString) {
                        resetDate = new Date(resetDateTimeString.split(" GMT")[0]);
                        return Math.abs(currentDate - resetDate) / (3600000);
                    }
                    if (parseInt(data.count_access) === 3) {
                        $(row).addClass('user-bloqued');
                        tooltipMessage = "Usuario bloqueado";
                    } else if (parseInt(data.pending_password) === 0 && data.updated_at) {
                        differenceInHours = calculateDifference(data.updated_at);
                        if (differenceInHours <= 24) {
                            $(row).addClass('user-updated-pass');
                            tooltipMessage = "Asignación de nueva contraseña en las últimas 24h";
                        }
                    } else if (parseInt(data.count_access) === 0 && data.updated_at) {
                        differenceInHours = calculateDifference(data.updated_at);
                        if (differenceInHours <= 24) {
                            $(row).addClass('user-updated-acc');
                            tooltipMessage = "Reseteo de acceso en las últimas 24h";
                        }
                    }
                    if (!$(row).hasClass('user-updated-pass')) {
                        $(row).removeClass('user-updated-pass');
                    }
                    if (!$(row).hasClass('user-updated-acc')) {
                        $(row).removeClass('user-updated-acc');
                    }
                    if (tooltipMessage) {
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
                    console.log(response);
                    if (textStatus === 'success') {
                        showToast(response.success?'success' :'error', response.mensaje);
                        table.ajax.reload();
                        $('#btnResetAccess' + employeeId + ' .material-icons').show();
                        $('#btnResetAccess' + employeeId + ' .spinner-border').hide();
                    }
                },
                error: function(xhr, status, error) {
                    var response = JSON.parse(xhr.responseText);
                    showToast('error', response);
                    $('#btnResetAccess' + employeeId + ' .material-icons').show();
                    $('#btnResetAccess' + employeeId + ' .spinner-border').hide();
                }

            }).fail(function(jqXHR, textStatus, errorThrown) {
                console.log(jqXHR.responseText);
                showToast('error', jqXHR.responseText);
                alert('Error Reseteando Acceso');
                $('#btnResetAccess' + employeeId + ' .material-icons').show();
                $('#btnResetAccess' + employeeId + ' .spinner-border').hide();
            });
        }

        function resetPassword(employeeId, back) {
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
                    if (textStatus === 'success') {
                        showToast(response.success?'success' :'error', response.mensaje);
                        table.ajax.reload();
                        $('#btnResetPass' + employeeId + ' .material-icons').show();
                        $('#btnResetPass' + employeeId + ' .spinner-border').hide();
                    }
                },
                error: function(xhr, status, error) {
                    var response = JSON.parse(xhr.responseText);
                    showToast('error', response);
                    $('#btnResetPass' + employeeId + ' .material-icons').show();
                    $('#btnResetPass' + employeeId + ' .spinner-border').hide();

                }

            }).fail(function(jqXHR, textStatus, errorThrown) {
                alert('Error Reseteando Contraseña');
                showToast('error', jqXHR.responseText);
                $('#btnResetPass' + employeeId + ' .material-icons').show();
                $('#btnResetPass' + employeeId + ' .spinner-border').hide();
            });
        }

        function denyAccess(employeeId, back) {
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
                    if (textStatus === 'success') {
                        showToast('error', response.mensaje);
                        table.ajax.reload();
                        $('#btnDenyAccess' + employeeId + ' .material-icons').show();
                        $('#btnDenyAccess' + employeeId + ' .spinner-border').hide();
                    }
                },
                error: function(xhr, status, error) {
                    var response = JSON.parse(xhr.responseText);
                    showToast('error', response);
                    $('#btnDenyAccess' + employeeId + ' .material-icons').show();
                    $('#btnDenyAccess' + employeeId + ' .spinner-border').hide();
                }


            }).fail(function(jqXHR, textStatus, errorThrown) {
                alert('Error Bloqueando Acceso');
                showToast('error', jqXHR.responseText);
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
                $('#btnSubmitLoadA3').show();

            } else {
                $('#btnSyncA3_' + employeeId + ' .material-icons').hide();
                $('#btnSyncA3_' + employeeId + ' .spinner-border').show();
            }
            $.ajax({
                url: "{{ route('employees.syncA3') }}",
                type: 'post',
                data: params,
                success: function(response, textStatus, jqXHR) {
                    console.log(respone);
                    if (textStatus === 'success') {
                        if (type == 'only') {
                            $('#btnSyncA3_' + employeeId + ' .material-icons').show();
                            $('#btnSyncA3_' + employeeId + ' .spinner-border').hide();
                        } else {
                            $('#btnSubmitLoadA3').hide();
                            $('#btnSyncA3').show();
                        }
                        showToast('success', response.mensaje);
                        table.ajax.reload();
                    }
                },
                error: function(xhr, status, error) {
                    var response = JSON.parse(xhr.responseText);
                    showToast('error', response);
                    window.location = response.url;
                }

            }).fail(function(jqXHR, textStatus, errorThrown) {
                showToast('error', jqXHR.responseText);
                $('#btnSyncA3_' + employeeId + ' .material-icons').show();
                $('#btnSyncA3_' + employeeId + ' .spinner-border').hide();
            });
        }


       
    </script>
@endsection

<style>
    .content {
        background-image: url(/assets/img/background_continue.png) !important;
        background-position: center center !important;
        background-size: 1000px;
        height: 220vh !important;

    }

    td.upper {
        text-transform: lowercase;
    }

    .user-updated-pass:hover::after,
    .user-updated-acc:hover::after,
    .user-bloqued:hover::after {
        content: attr(data-tooltip);
        /* Inserta el texto del tooltip */
        position: absolute;
        bottom: 100%;
        left: 50%;
        transform: translateX(-50%);
        white-space: nowrap;
        visibility: hidden;
        opacity: 0;
        transition: opacity 0.2s, visibility 0.2s;
        background-color: black;
        color: white;
        padding: 5px 10px;
        border-radius: 4px;
        font-size: 12px;
    }

    .user-updated-pass:hover::after,
    .user-updated-acc:hover::after,
    .user-bloqued:hover::after {
        visibility: visible;
        opacity: 1;
    }

  /* ROW RED FOR BLOQUED USER */
  
  .user-bloqued {
    color: red ;
}

/* ROW GREEN FOR UPDATED USER */
.user-updated-acc,.user-updated-pass{
color: green ;
}

    </style>
