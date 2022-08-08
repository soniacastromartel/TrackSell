@extends('layouts.logged')

@section('content')
@include('inc.navbar')
@include('common.alert')


<div class="alert alert-danger" id="alertErrorChangeEmployee" role="alert" style="display: none">
</div>
<div class="alert alert-success" id="alertChangeEmployee" role="alert" style="display: none">
</div>


<div class="content">
    <div class="container-fluid">
        <div class="row col-md-12 mb-3 ">
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
        <table class="table table-striped table-bordered employees-datatable col-md-12">
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

<style>
    #category {
        text-transform: lowercase;
    }

    #btnSyncA3 {
        font-weight: 900;
        font-size: large;
        background-color: #eeeeee;
        color: var(--red-icot);
    }
    td.upper {
        text-transform: lowercase;

    }
</style>

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
                processing: true,
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
                columnDefs: [
                    {
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
                        if(data != null) {
                           
                                return data.toUpperCase();

                                // return data.split(' ')[1];
                        } else {
                            return data;
                        }
                     // TODO ARREGLAR AQUÍ LA CAPITALIZAION DE CATEGORÍAS
                        
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


    function denyAccess(employeeId, back) {
        $('#alertChangeEmployee').hide();
        $('#alertErrorChangeEmployee').hide();
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
                }
            },
            error: function(xhr, status, error) {
                var response = JSON.parse(xhr.responseText);
                $('#alertErrorChangeEmployee').text(response.mensaje);
                $('#alertErrorChangeEmployee').show().delay(2000).slideUp(300)();
            }

        }).fail(function(jqXHR, textStatus, errorThrown) {
            alert('Error cargando servicios');
        });
    }

    function resetAccessApp(employeeId, back) {
        $('#alertChangeEmployee').hide();
        $('#alertErrorChangeEmployee').hide();
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
                }
            },
            error: function(xhr, status, error) {
                var response = JSON.parse(xhr.responseText);
                $('#alertErrorChangeEmployee').text(response.mensaje);
                $('#alertErrorChangeEmployee').show().delay(2000).slideUp(300);
            }

        }).fail(function(jqXHR, textStatus, errorThrown) {
            alert('Error cargando servicios');
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
            $('#btnSyncA3_' + employeeId).hide();
            $('#btnSubmitLoad_' + employeeId).show();
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
                        $('#btnSyncA3_' + employeeId).show();
                        $('#btnSubmitLoad_' + employeeId).hide();
                    } else {
                        $('#btnSubmitLoad').hide();
                        $('#btnSyncA3').show();
                    }
                    table.ajax.reload();
                }
            },
            error: function(xhr, status, error) {
                var response = JSON.parse(xhr.responseText);
                window.location = response.url;
            }

        }).fail(function(jqXHR, textStatus, errorThrown) {
            alert('Error'+jqXHR.responseText);
        });
    }
</script>
@endsection