@extends('layouts.logged')
@section('content')
    @include('inc.navbar')
    <link rel="stylesheet" href="{{ asset('/css/buttons.css') }}">
    <div class="content">
        <div class="container-fluid">
            <div class="card" style="margin-top:120px ">
                <div class="card-header card-header-danger">
                    <h4 class="card-title">Centros</h4>
                </div>
                <div class="row col-lg-12">
                    <div class="col-md-11 header-logo" style="margin-top:70px;">
                        <a href="{{ route('centres.create') }}" id="btnAdd" class="header-btn-add">
                            <span id="icon-send" class="material-symbols-outlined">
                                domain_add
                            </span><strong>NUEVO CENTRO</strong></a>
                    </div>
                    <div class=" col-md-1" style="display:flex;justify-content:end;margin-top:100px; ">

                    </div>
                </div>
            </div>

            <table id="centres-datatable"
                class="table  table-striped table-bordered dataTable_width_auto centres-datatable">
                <thead class="table-header">
                    <tr>
                        <th>Centro</th>
                        <th>Dirección</th>
                        <th>Teléfono</th>
                        <th>Email</th>
                        <th>Horario</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>


    <style>
        .header-logo {
            background-image: url(/assets/img/centres.jpg);
            background-repeat: no-repeat;
            background-size: contain;
            background-position-x: right;
            min-width: 280px;
            min-height: 130px;
        }

        .content {
            background-image: url(/assets/img/background_continue.png) !important;
            background-position: center center !important;
            background-size: 1000px;
            height: 160vh !important;

        }
    </style>

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
            $('#adminCentre').addClass('active');


            table = $('.centres-datatable').DataTable({
                processing: true,
                serverSide: true,
                language: {
                    "url": "{{ asset('dataTables/Spanish.json') }}"
                },
                ajax: {
                    url: "{{ route('centres.index') }}",
                    data: function(d) {
                        d.search = $('input[type="search"]').val()
                    }
                },
                columnDefs: [{
                        targets: [-1, 0, 1, 2, 3, 4],
                        visible: true,
                        className: 'dt-body-center'
                    },
                    {
                        targets: 3,
                        render: function(data, type, row) {
                            if (data != null) {
                                return data.split(';', 1);

                            }
                            return ' ';
                        }
                    },
                    {
                        width: "5%",
                        targets: 0
                    },
                    {
                        width: "30%",
                        targets: 1
                    },
                    {
                        width: "10%",
                        targets: 2
                    },
                    {
                        width: "10%",
                        targets: 3
                    },
                    {
                        width: "10%",
                        targets: 4
                    },
                    {
                        width: "20%",
                        targets: 5
                    }
                ],
                columns: [{
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'address',
                        name: 'address'
                    },
                    {
                        data: 'phone',
                        name: 'phone'
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'timetable',
                        name: 'timetable'
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
            params = {};
            params["_token"] = "{{ csrf_token() }}";
            params["id"] = id;

            $.ajax({
                url: 'centres/destroy/' + params['id'],
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
