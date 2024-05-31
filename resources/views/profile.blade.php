@extends('layouts.logged')
@section('content')
@include('inc.navbar')
@include('common.alert')
<link rel="stylesheet" href="{{ asset('/css/buttons.css') }}">


    <div class="content" style="display:flex; justify-content:center" >
       
                <div class="card" style="width:40%; margin-top:100px;">
                    <div class="card-header card-header-danger">
                        <h4 class="card-title">Perfil Usuarios</h4>
                    </div>
                    <div class="card-body">
                        <div class="col-lg-10 mx-auto">
                        <form id=editEmployee action="{{ route('editProfile', $employee->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-lg-10 mx-auto">
                                    <label for="name" class="label">Nombre </label>
                                    <input type="text" class="form-control" name="name" id="name" placeholder=""
                                        value="{{ isset($employee) ? $employee->name : '' }}" readonly>
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-lg-10 mx-auto">
                                    <label for="login" class="label">Login </label>
                                    <input type="text" class="form-control" name="username" id="username"
                                        placeholder="" value="{{ isset($employee) ? $employee->username : '' }}"
                                        readonly>
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-lg-10 mx-auto">
                                    <label for="login" class="label">Password </label>
                                    <input type="password" class="form-control" name="password" id="password"
                                        placeholder="" value="{{ isset($employee) ? $employee->password : '' }}"
                                        readonly>
                                </div>
                            </div>
                            <div class="col-lg-10 mx-auto" style="margin-top:50px;">
                                <!--- Accion solo permitida para admin -->
                                <div class="row">
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

                                <div class="row">
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
                            <div class="row md-3 text-right">
                                    <div class="col-md-12">
                                        <button id="btnSubmitSave" type="submit" class="btn-save">
                                        <span class="material-icons">save</span>
                                        </button>
                                        <button id="btnSubmitLoadSave" type="submit" class="btn-save"
                                            style="display: none"Save>
                                            <span class="spinner-border spinner-border-sm" role="status"
                                                aria-hidden="true"></span>
                                        </button>
                                        <button id="btnBack" href="/config" class="btn-return">
                                        <span class="material-icons">arrow_back</span> 
                                        </button>
                                    </div>
                                </div>
                        </form>
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


            $("#btnSubmitSave").on('click', function() {
                $('#btnSubmitSave').hide();
                $('#btnSubmitLoadSave').show();
                $('#btnSubmitLoadSave').prop('disabled', true);
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

<style>
    .main-panel {
    background-image: url(/assets/img/background_continue.png) !important;
    background-position: center center !important;
    background-size: 1000px;
 
}



    </style>