@extends('layouts.logged')
@section('content')
    @include('inc.navbar')
    <link rel="stylesheet" href="{{ asset('/css/buttons.css') }}">

    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card" style="margin-top:100px">
                        <div class="card-header card-header-danger">

                            <h4 class="card-title">Modificar empleado</h4>

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
                                                    <label for="login">Username <span class="obligatory">*</span></label>
                                                    <input type="text" class="form-control px-2" name="username"
                                                        id="username" placeholder=""
                                                        value="{{ isset($employee) ? $employee->username : '' }}" readonly>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="form-group col-md-10 mx-auto">
                                                    <label for="name">Nombre <span class="obligatory">*</span></label>
                                                    <input type="text" class="form-control px-2" name="name"
                                                        id="name" placeholder=""
                                                        value="{{ isset($employee) ? $employee->name : '' }}" readonly>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class=" form-group col-md-10 mx-auto">
                                                    <label for="login">Password <span class="obligatory">*</span></label>
                                                    <input type="password" class="form-control px-2" name="password"
                                                        id="password" placeholder=""
                                                        value="{{ isset($employee) ? $employee->password : '' }}" readonly>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class=" form-group col-md-10 mx-auto">
                                                    <label for="email">Email</label>
                                                    <input type="email" class="form-control px-2" name="email"
                                                        id="email" placeholder=""
                                                        value="{{ isset($employee) ? $employee->email : '' }}">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class=" form-group col-md-10 mx-auto">
                                                    <label for="dni">DNI</label>
                                                    <input type="text" class="form-control px-2" name="dni"
                                                        id="dni" placeholder=""
                                                        value="{{ isset($employee) ? $employee->dni : '' }}">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class=" form-group col-md-10 mx-auto">
                                                    <label for="phone">Teléfono</label>
                                                    <input type="text" class="form-control px-2" name="phone"
                                                        id="phone" placeholder=""
                                                        value="{{ isset($employee) ? $employee->phone : '' }}">
                                                </div>
                                            </div>
                                            <div class="row mt-5">
                                                <div class="col-lg-6">
                                                    <label class="label" for="name">Fecha anterior </label>
                                                    <input type="date" id="date_before" name="date_before"
                                                        max="3000-12-31" min="1000-01-01" value="{{ $dayYesterday }}"
                                                        class="form-control text-center" />
                                                </div>
                                                <div class="col-lg-6">
                                                    <label class="label" for="name">Fecha nueva </label>
                                                    <input type="date" id="date_new" name="date_new" max="3000-12-31"
                                                        min="1000-01-01" value="{{ $dayNow }}"
                                                        class="form-control text-center" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="row">
                                                <div class="form-group col-md-10 mx-auto">
                                                    <label class="label">Tipo de rol <span
                                                            class="obligatory">*</span></label>
                                                    <div class="select-wrapper">
                                                        <span id="icon-select"
                                                            class="icon-select material-symbols-outlined">
                                                            gpp_good
                                                        </span>
                                                        <select class="selectpicker" name="rol_id" id="rol_id"
                                                            data-size="7" data-style="btn btn-red-icot btn-round"
                                                            title="* Seleccione Rol" tabindex="-98">
                                                            @foreach ($roles as $role)
                                                                <option value="{{ $role->id }}"
                                                                    @if (isset($employee) && $role->id == $employee->rol_id) selected="selected" @endif>
                                                                    {{ $role->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="form-group col-md-10 mx-auto">
                                                    <label class="label">Categoría<span
                                                            class="obligatory">*</span></label>
                                                    <div class="select-wrapper">
                                                        <span id="icon-select"
                                                            class="icon-select material-symbols-outlined">
                                                            work
                                                        </span>
                                                        <select class="selectpicker" name="job_id" id="job_id"
                                                            data-size="7" data-style="btn btn-red-icot btn-round"
                                                            title="* Seleccione Categoría" tabindex="-98">
                                                            @foreach ($categories as $category)
                                                                <option value="{{ $category->category_name }}"
                                                                    @if (isset($employee) && $category->category_name == $employee->category) selected="selected" @endif>
                                                                    {{ $category->category_name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="form-group col-md-10 mx-auto">
                                                    <label class="label">¿Motivo de exclusión en el Ranking? <span
                                                            class="obligatory">*</span></label>
                                                    <div class="select-wrapper">
                                                        <span id="icon-select"
                                                            class="icon-select material-symbols-outlined">
                                                            feedback
                                                        </span>
                                                        <select class="selectpicker" name="excludingR" id="excludingR"
                                                            data-size="12" data-style="btn btn-red-icot btn-round">
                                                            <option value="null">NINGUNO</option>
                                                            @foreach ($motives as $row)
                                                                <option value="{{ $loop->index }}"
                                                                    @if (isset($employee) && $employee->excludeRanking == $row) selected="selected" @endif>
                                                                    {{ $row }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mt-4">
                                                <div class="col-md-10 mx-auto">
                                                    <label class="label" style="display: inline;">Centro de trabajo <span
                                                            class="obligatory">*</span></label>
                                                    <!-- Default switch -->
                                                    <div class="custom-control custom-switch"
                                                        style="display: inline; float: right;">
                                                        <input type="checkbox" class="custom-control-input"
                                                            id="manual_centre" name="manual_centre"
                                                            @if (isset($employee) && $employee->force_centre_id === 1) checked @endif>
                                                        <label class="custom-control-label" for="manual_centre">Centro
                                                            manual</label>
                                                    </div>
                                                    <div class="select-wrapper" style="margin-top: 25px;">
                                                        <span id="icon-select"
                                                            class="icon-select material-symbols-outlined"
                                                            style="margin-top:6px;">
                                                            business
                                                        </span>
                                                        <select class="selectpicker mt-3" name="centre_id" id="centre_id"
                                                            data-size="7" data-style="btn btn-red-icot btn-round"
                                                            title="* Seleccione Centro" tabindex="-98">
                                                            @foreach ($centres as $centre)
                                                                <option value="{{ $centre->id }}"
                                                                    @if (isset($employee) && $centre->id == $employee->centre_id) selected="selected" @endif>
                                                                    {{ $centre->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <br class="m-5">
                                    <div class="row mt-5" style="justify-content: center;">
                                        <div class="col-md-11">
                                            <table
                                                class="table table-striped table-bordered employees-history-datatable col-md-12">
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
                                        <div class="col-12 d-flex justify-content-end align-items-center gap-2">
                                            <button id="btnSubmit" type="submit" class="btn-save tooltip-save">
                                                <span class="material-symbols-outlined ">save</span>
                                            </button>

                                            <button id="btnSubmitLoad" type="button" class="btn-save"
                                                style="display: none;">
                                                <span class="spinner-border spinner-border-sm" role="status"
                                                    aria-hidden="true"></span>
                                            </button>

                                            <button id="btnCancel" type="button" class="btn-remove tooltip-remove">
                                                <span class="material-symbols-outlined">person_remove</span>
                                            </button>

                                            <button id="btnBack" type="button" class="btn-return tooltip-back">
                                                <span class="material-symbols-outlined">arrow_back</span>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12 text-right mt-2">
                                            <label id="lbl" class="label">
                                                <span class="obligatory">*</span> Estos campos son requeridos
                                            </label>
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

<style>
    .content {
        background-image: url(/assets/img/background_continue.png) !important;
        background-position: center center !important;
        background-size: 1000px;
        height: 140vh !important;

    }
</style>
