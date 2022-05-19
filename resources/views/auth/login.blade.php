@extends('layouts.app')

@section('content')

  @if (session('success'))
      <div class="alert alert-success" role="alert">
          {{ session('success') }}
      </div>
  @endif
  
  @if (session('error'))
      <div class="alert alert-danger" role="alert">
          {{ session('error') }}
      </div>
  @endif

<div class="container">
    <div class="row justify-content-center">
<!-- Styles -->
    <link href="{{ asset('css/login.css') }}" rel="stylesheet">
            <div class="wrapper fadeInDown">
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
                     <img src="{{ asset('assets/img/background_pdi.png') }}" width="100px" id="icon" alt="User Icon" /> <br>
                    </div>

                    <!-- Login Form -->
                    <form class="mt-2" method="POST" action="{{ route('login') }}">
                        @csrf

                        <input id="username" type="text" class="fadeIn second @error('username') is-invalid @enderror" name="username" value="{{ old('username') }}" required  autofocus placeholder="Nombre de usuario">
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

                        <input type="submit" class="fadeIn fourth" value="Entrar">
                    </form>
   
                </div>
            </div>
            
    </div>
</div>
@endsection
