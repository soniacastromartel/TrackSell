@extends('layouts.app')
@section('content')
@include('common.alert')


        <div class="bodyLogin">

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
            <div id="formContent" style="background-color: var(--red-icot);display:flex;flex-direction:column; justify-content:center; align-items:center; margin-top:50px;">
                <!-- Tabs Titles -->

                <!-- Icon -->
                <div class="fadeInFirst">
                    <img src="{{ asset('assets/img/LogoICOTblanco.png') }}"  id="icon" /> 
                    <img src="{{ asset('assets/img/logoIncentivosBlanco.png') }}" width="300px"/>
                </div>

                <!-- Login Form -->
                <form class="mt-2" method="POST" action="{{ route('login') }}" style="width: 350px">
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

                    <input type="submit" class="fadeIn fourth btn btn-red-icot" id="btnSubmit" value="Entrar" style="border: 1px solid white">
                </form>

            </div>
        </div>

    </div>

@endsection

<style>
    #btnSubmit {
        font-weight: 900;
        margin-top: 16px;
    }
  .bodyLogin {
    display: flex;
    justify-content: center;
    width: 100vw !important; 
    height:100vh !important; 
    background-image: url(/assets/img/background-login.png);
  

}

    #icon {
        padding: 80px;
    }
</style>