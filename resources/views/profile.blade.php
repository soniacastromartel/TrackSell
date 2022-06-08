@extends('layouts.logged')

@section('content')

@include('inc.navbar')
@include('common.alert')

    <div class="content">
        <div>
            <div class="card">
                <div class="card-header card-header-danger">
                    <h4 class="card-title">Perfil Usuarios</h4>
                </div>
                <div class="col-lg-10 mx-auto">
                    <div class="card-body">
                        <div class="col-11 mx-auto">
                        <form id=editEmployee action="{{ route('editProfile', $employee->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-md-4">
                                    <label for="name" class="label">Nombre * </label>
                                    <input type="text" class="form-control" name="name" id="name" placeholder=""
                                        value="{{ isset($employee) ? $employee->name : '' }}" readonly>
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="login" class="label">Login </label>
                                    <input type="text" class="form-control" name="username" id="username"
                                        placeholder="" value="{{ isset($employee) ? $employee->username : '' }}"
                                        readonly>
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="login" class="label">Password </label>
                                    <input type="password" class="form-control" name="password" id="password"
                                        placeholder="" value="{{ isset($employee) ? $employee->password : '' }}"
                                        readonly>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <!--- Accion solo permitida para admin -->
                                <div class="col-md-2">
                                    <select class="selectpicker" name="rol_id" id="rol_id" data-size="7"
                                        data-style="btn btn-red-icot btn-round" title="* Seleccione Rol" tabindex="-98"
                                        @if (isset($employee) && $employee->rol_id != 1) disabled="disabled" @endif>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->id }}"
                                                @if (isset($employee) && $role->id == $employee->rol_id) selected="selected" @endif>
                                                {{ $role->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-2">
                                    <select class="selectpicker" name="centre_id" id="centre_id" data-size="7"
                                        data-style="btn btn-red-icot btn-round" title="* Seleccione Centro"
                                        tabindex="-98">
                                        @foreach ($centres as $centre)
                                            <option value="{{ $centre->id }}"
                                                @if (isset($employee) && $centre->id == $employee->centre_id) selected="selected" @endif>
                                                {{ $centre->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                
                            </div>
                            <div class="row mt-2 text-right">
                                    <div class="col-md-12">
                                        <button id="btnSubmit" type="submit" class="btn btn-success">
                                        <span class="material-icons mr-2">save</span>{{ __('Guardar') }}
                                        </button>
                                        <button id="btnSubmitLoad" type="submit" class="btn btn-success"
                                            style="display: none">
                                            <span class="spinner-border spinner-border-sm" role="status"
                                                aria-hidden="true"></span>{{ __('Guardando...') }}
                                        </button>
                                        <button id="btnBack" href="/config" class="btn btn-red-icot">
                                        <span class="material-icons">arrow_back</span> {{ __('Volver') }}
                                        </button>
                                    </div>
                                </div>
                        </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        $(function() {

            $(".nav-item").each(function() {
                $(this).removeClass("active");
            });
            $('#profileEmployee').addClass('show');
            $('#menuProfile').addClass('active');


            $("#btnSubmit").on('click', function() {
                $('#btnSubmit').hide();
                $('#btnSubmitLoad').show();
                $('#btnSubmitLoad').prop('disabled', true);
                $("#editEmployee").submit(function() {
                    $('<input />').attr('type', 'hidden')
                        .attr('id', 'rol_id_hidden')
                        .attr('value', $("#rol_id_hidden option:selected").val())
                        .appendTo('#editEmployee');
                });
            });
            $("#btnBack").on('click', function() {
                window.location.href = "{{ route('home') }}";
                return false;
            });
        });
    </script>
@endsection