@extends('layouts.logged')

@section('content')
@include('inc.navbar')
@include('common.alert')


<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card ">
                    <div class="card-header card-header-info card-header-text">
                        <div class="card-text">
                            <h4 class="card-title">Modificar empleado</h4>
                        </div>
                    </div>
                    <div class="card-body px-6">
                        <form id="editEmployee" action="{{ route('employees.update', $employee->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <form id=editEmployee action="{{ route('editProfile', $employee->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="row" style="justify-content: space-around;">
                                    <div class="col-md-4">
                                            <div class="row">
                                                <div class="form-group col-md-10 mx-auto">
                                                    <label for="login">Login <span class="obligatory">*</span></label>
                                                    <input type="text" class="form-control px-2" name="username" id="username" placeholder="" value="{{ isset($employee) ? $employee->username : ''}}" readonly>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="form-group col-md-10 mx-auto">
                                                    <label for="name">Nombre <span class="obligatory">*</span></label>
                                                    <input type="text" class="form-control px-2" name="name" id="name" placeholder="" value="{{ isset($employee) ? $employee->name : ''}}" readonly>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class=" form-group col-md-10 mx-auto">
                                                    <label for="login">Password <span class="obligatory">*</span></label>
                                                    <input type="password" class="form-control px-2" name="password" id="password" placeholder="" value="{{ isset($employee) ? $employee->password : ''}}" readonly>
                                                </div>
                                            </div>                                        
                                        <div class="row mt-5">
                                                <div class="col-lg-6">
                                                    <label class="label" for="name">Fecha anterior </label>
                                                    <input type="date" id="date_before" name="date_before" max="3000-12-31" min="1000-01-01" value="{{$dayYesterday}}" class="form-control text-center" />
                                                </div>
                                                <div class="col-lg-6">
                                                    <label class="label" for="name">Fecha nueva </label>
                                                    <input type="date" id="date_new" name="date_new" max="3000-12-31" min="1000-01-01" value="{{$dayNow}}" class="form-control text-center" />
                                                </div>
                                            </div>                    
                                    </div>
                                    <div class="col-md-4">
                                        <div class="row">
                                            <div class="form-group col-md-10 mx-auto">
                                                <label class="label">Tipo de rol <span class="obligatory">*</span></label>
                                                <select class="selectpicker" name="rol_id" id="rol_id" data-size="7" data-style="btn btn-red-icot btn-round" title="* Seleccione Rol" tabindex="-98">
                                                    @foreach ($roles as $role)
                                                    <option value="{{$role->id}}" @if (isset($employee) && $role->id == $employee->rol_id )
                                                        selected="selected"
                                                        @endif
                                                        >{{$role->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-10 mx-auto">
                                                <label class="label">¿Motivo de exclusión en el Ranking? <span class="obligatory">*</span></label>
                                                <select class="selectpicker" name="excludingR" id="excludingR" data-size="12" data-style="btn btn-red-icot btn-round">
                                                    <option value="null">NINGUNO</option>
                                                    @foreach($motives as $row)
                                                    <option value="{{$loop->index}}" @if (isset($employee) && $employee->excludeRanking == $row)
                                                        selected="selected"
                                                        @endif>{{ $row }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row mt-4">
                                            <div class="col-md-10 mx-auto">
                                                <label class="label" style="display: inline;">Centro de trabajo <span class="obligatory">*</span></label>
                                                <!-- Default switch -->
                                                <div class="custom-control custom-switch" style="display: inline; float: right;">
                                                    <input type="checkbox" class="custom-control-input" id="manual_centre" name="manual_centre" @if (isset($employee) && $employee->force_centre_id === 1)
                                                    checked
                                                    @endif
                                                    >
                                                    <label class="custom-control-label" for="manual_centre">Centro manual</label>
                                                </div>
                                                <select class="selectpicker mt-3" name="centre_id" id="centre_id" data-size="7" data-style="btn btn-red-icot btn-round" title="* Seleccione Centro" tabindex="-98">
                                                    @foreach ($centres as $centre)
                                                    <option value="{{$centre->id}}" @if (isset($employee) && $centre->id == $employee->centre_id )
                                                        selected="selected"
                                                        @endif
                                                        >{{$centre->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <br class="m-5">
                                <div class="row mt-5" style="justify-content: center;">
                                    <div class="col-md-11">
                                        <table class="table table-striped table-bordered employees-history-datatable col-md-12">
                                            <thead class="table-header">
                                                <tr>
                                                    <th>Nombre</th>
                                                    <th>Centro</th>
                                                    <th>Fecha</th>
                                                    <th>Rol</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-md-5" style="margin-left: 55px;">
                                        <button id="btnSubmit" type="submit" class="btn btn-fill btn-success"> <span class="material-icons">
                            save
                            </span> {{ __('Guardar') }}</button>
                                        <button id="btnSubmitLoad" type="submit" class="btn btn-success" style="display: none">
                                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                            {{ __('Guardando...') }}
                                        </button>
                                        <button id="btnBack" href="#" class="btn btn-fill btn-red-icot">
                                        <span class="material-icons">
                            arrow_back
                            </span> {{ __('Volver') }}
                                        </button>
                                        <div class="mt-2 mr-3">
                                            <label id="lbl" class="label"><span class="obligatory">*</span> Estos campos son requeridos</label>
                                        </div>
                                    </div>
                                </div>
                            </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
#lbl {
    color: black;
    font-weight: 600;
    font-family: 'Helvetica', 'Arial', sans-serif;
    font-size:12px;
}

    td {
font-weight: bold;
/* text-align: center; */
}
</style>

<script type="text/javascript">
    $(function() {

        $(".nav-item").each(function() {
            $(this).removeClass("active");
        });
        $('#pagesConfig').addClass('show');
        $('#adminUser').addClass('active');

        $("#btnSubmit").on('click', function() {
            $('#btnSubmit').hide();
            $('#btnSubmitLoad').show();
            $('#btnSubmitLoad').prop('disabled', true);
            $("#editEmployee").submit();
        });
        $("#btnBack").on('click', function() {
            window.location.href = "{{ route('employees.index') }}";
            return false;
        });

        var table = $('.employees-history-datatable').DataTable({
            processing: true,
            serverSide: true,
            language: {
                "url": "{{ asset('dataTables/Spanish.json') }}"
            },
            ajax: "{{ route('employees.history.index', $employee->id) }}",
            columns: [{
                    data: 'employee',
                    name: 'employee'
                },
                {
                    data: 'centre',
                    name: 'centre'
                },
                {
                    data: 'fecha',
                    name: 'fecha'
                },
                {
                    data: 'role',
                    name: 'role'
                }

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
                            //column.search(val ? val : '', true, false).draw();
                            column
                                .search(val ? '^' + val + '$' : '', true, false)
                                .draw();
                        });

                });
            }
        });

    });
</script>
@endsection