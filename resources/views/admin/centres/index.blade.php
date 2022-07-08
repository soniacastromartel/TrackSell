@extends('layouts.logged')

@section('content')

@include('inc.navbar')
@include('common.alert')

<div id="alertErrorCentre" class="alert alert-danger" role="alert" style="display: none">
</div>
<div id="alertCentre" class="alert alert-success" role="alert" style="display: none">
</div>

<div class="content">
    <div class="container-fluid">
        @if ($user -> rol_id == 1)
        <div class="row col-md-12 mb-3 ">
            <div class="col-md-8">
            </div>
            <div class="col-md-4 text-right">
                <a href="{{ route('centres.create') }}" id="btnNewCenter" class="btn btn-red-icot btn-lg"><span class="material-icons mr-1">
                            add_circle</span> Nuevo</a>
            </div>
        </div>
        @endif
        <table id="centres-datatable" class="table  table-striped table-bordered dataTable_width_auto centres-datatable">
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

@include('common.modal')

<style>

    table.dataTable.dataTable_width_auto {
        /* width: 100%; */
    }
</style>

<script type="text/javascript">
 function confirmRequest(state, id) {
            $("#message-validation").html('Está a punto de eliminar este Centro ¿Confirmar?');
            $("#modal-title").html('ELIMINACIÓN');
    
        $("#id").val(id);
        $("#modal-validate").modal('show');
    }
    var table;

    $(function() {

        $(".nav-item").each(function() {
            $(this).removeClass("active");
        });
        $('#pagesConfig').addClass('show');
        $('#adminCentre').addClass('active');

        $("#btnConfirmRequest").on('click', function(event) {
            destroy();
        });

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
            columnDefs: [
                {
                    targets: [-1,0,1,2,3,4],
                    visible: true,
                    className: 'dt-body-center'
                },
                {
                    targets: 3,
                   render: function(data, type, row) {
                    if (data!=null ){
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
                    width: "10%",
                    targets: 5
                },
                {
                    width: "20%",
                    targets: 6
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
                    data: 'cancellation_date',
                    name: 'cancellation_date'
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
       
        params = {};
        params["_token"] = "{{ csrf_token() }}";
        params["id"] = $("#id").val();

        $.ajax({
            url:  'centres/destroy/' + params['id'],
            type: 'get',
            data: params,
            success: function(response, textStatus, jqXHR) {
                // if success, HTML response is expected, so replace current
                if (textStatus === 'success') {
                    $('#alertCentre').text(response.mensaje); 
                    $('#alertCentre').show().delay(2000).slideUp(300);
                    $("#modal-validate").modal('hide');
                    table.ajax.reload();
                }
            },
            complete: function() {
                    $("#modal-validate").modal('hide');
                    table.ajax.reload();
                    },
            error: function(xhr, status, error) {
                var response = JSON.parse(xhr.responseText);
                $('#alertErrorCentre').text(response.mensaje); 
                $('#alertErrorCentre').show().delay(2000).slideUp(300); 
            }

        }).fail(function(jqXHR, textStatus, errorThrown) {
            alert('Error cargando centros');
        });
    }

</script>

@endsection