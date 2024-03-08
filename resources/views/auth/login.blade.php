@extends('layouts.app')

@section('content')
@include('common.alert')

<div class="container-login">
    <div class="row">
        <link href="{{ asset('css/login.css') }}" rel="stylesheet">
        <div class="fadeInDown">
            @if ($message = Session::get('error'))
            <div class="alert alert-danger">
                <p>{{ $message }}</p>
            </div>
            @endif
            @if ($nDays != "")
            <div class="alert alert-timeout alert-danger animacion" style="position:relative; left:10px;" role="alert">
                En {{$nDays}} días llega próximo corte, 20 de {{$currentMonth}}
            </div>
            @endif
            <div id="formContent">
                <!-- Tabs Titles -->

                <!-- Icon -->
                <div class="fadeIn first">
                    <img src="{{ asset('assets/img/logoGif5.gif') }}"  id="icon" alt="User Icon"/> 
                    <img src="{{ asset('assets/img/logoIncentivos.png') }}" width="300px"/>
                </div>

                <!-- Login Form -->
                <form class="mt-2" method="POST" action="{{ route('login') }}">
                    @csrf

                    <input id="username" type="text" class="fadeIn second @error('username') is-invalid @enderror" name="username" value="{{ old('username') }}" required autofocus placeholder="Nombre de usuario">
                    @error('username')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror

                    <input id="password" type="password" class="fadeIn third @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="Contraseña">
                    @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror

                    <input type="submit" class="fadeIn fourth btn btn-red-icot" id="btnSubmit" value="Entrar">
                </form>

            </div>
        </div>

    </div>
</div>
@endsection

<style>



    #btnSubmit {
        font-weight: 900;
        margin-top: 16px;
    }

    #formContent{
        
    }

    #icon {
        padding: 10px;
    }
</style>