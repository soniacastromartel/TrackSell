@extends('layouts.app')
@section('content')
  @include('common.alert')

  <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <div class="container-fluid bodyLogin">
        <div class="fadeInDown">
            @if ($message = Session::get('error'))
                <div class="alert alert-danger">
                    <p>{{ $message }}</p>
                </div>
            @endif
            @if ($nDays != '')
                <div class="alert alert-timeout alert-danger animacion" style="position:relative; left:10px;" role="alert">
                    En {{ $nDays }} días llega próximo corte, 20 de {{ $currentMonth }}
                </div>
            @endif
            <div id="formContent" class="col-md-12 formContent" style="background-color: var(--red-icot); display:flex; flex-direction:column; justify-content:center; align-items:center; margin-top:50px;">
                <!-- Tabs Titles -->

                <!-- Icon -->
                <div class="fadeInFirst">
                    <img src="{{ asset('assets/img/LogoICOTblanco.png') }}" id="icon" class="img-responsive" />
                    <img src="{{ asset('assets/img/logoIncentivosBlanco.png') }}" class="img-responsive" />
                    <img src="{{ asset('assets/img/logo-arrow.png') }}" class="img-responsive logo-arrow" />
                </div>

                <!-- Login Form -->
                <form class="formPost" method="POST" action="{{ route('login') }}">
                    @csrf

                    <input id="username" type="text" class="fadeIn second @error('username') is-invalid @enderror" name="username" value="{{ old('username') }}" required autofocus placeholder="Nombre de usuario">
                    @error('username')
                        <span class="text-credenciales text-white align-self-md-center" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror

                    <input id="password" type="password" class="fadeIn third @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="Contraseña">
                    @error('password')
                        <span class="invalid-feedback color tex" role="alert">
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
/* BASIC */
body {
  font-family: "Poppins", sans-serif;
  height: 100vh;
  margin: 0;
  padding: 0;
  overflow: hidden;
}

a {
  display: inline-block;
  text-decoration: none;
  font-weight: 400;
}

h2 {
  text-align: center;
  font-size: 16px;
  font-weight: 600;
  text-transform: uppercase;
  display: inline-block;
  margin: 40px 8px 10px 8px;
  color: #cccccc;
}

/* STRUCTURE */
.bodyLogin {
  display: flex;
  justify-content: center;
  align-items: center;
  width: 100vw;
  height: 100vh;
  margin: 0;
  padding: 0;
  background-image: url(/assets/img/background-login.png);
  background-size: cover;
}

#icon {
  padding: 70px;
}

.formContent {
  background-color: var(--red-icot) !important;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  margin-top: 50px;
  border-radius: 20px;
  box-shadow: 10px 10px 10px rgba(0, 0.5, 0.5, 0.5);
}

.fadeInFirst {
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
}

.formPost {
  width: 250px;
  margin: 20px;
  display: flex;
  flex-direction: column;
  justify-content: center;
}

#formFooter {
  background-color: #f6f6f6;
  border-top: 1px solid #dce8f1;
  padding: 25px;
  text-align: center;
  border-radius: 0 0 10px 10px;
}

#btnSubmit {
  font-weight: 900;
  margin-top: 16px;
}

/* TABS */
h2.inactive {
  color: #cccccc;
}

h2.active {
  color: #0d0d0d;
  border-bottom: 2px solid #ffffff;
}

/* FORM TYPOGRAPHY */
input[type="button"],
input[type="submit"],
input[type="reset"] {
  background-color: var(--red-icot);
  border: none;
  color: white;
  padding: 15px 80px;
  text-align: center;
  text-decoration: none;
  display: inline-block;
  text-transform: uppercase;
  font-size: 13px;
  box-shadow: 0 10px 30px rgba(253, 253, 253, 1);
  border-radius: 5px;
  margin: 5px 20px 40px 20px;
  transition: all 0.3s ease-in-out;
}

input[type="button"]:hover,
input[type="submit"]:hover,
input[type="reset"]:hover {
  background-color: #fff;
  color: var(--red-icot);
  border: 3px solid var(--red-icot);
}

input[type="button"]:active,
input[type="submit"]:active,
input[type="reset"]:active {
  transform: scale(0.95);
}

input[type="text"],
input[type="password"] {
  background-color: #f6f6f6;
  border: none;
  color: #0d0d0d;
  padding: 15px 32px;
  text-align: center;
  font-size: 16px;
  margin: 5px;
  border: 2px solid #f6f6f6;
  transition: all 0.5s ease-in-out;
  border-radius: 5px;
}

input[type="text"]:focus {
  background-color: #fff;
  border-bottom: 2px solid #fafafa;
}

input[type="text"]::placeholder {
  color: #cccccc;
}

/* ANIMATIONS */
.fadeInDown {
  animation: fadeInDown 1s both;
}

@keyframes fadeInDown {
  0% {
    opacity: 0;
    transform: translate3d(0, -100%, 0);
  }
  100% {
    opacity: 1;
    transform: none;
  }
}

.fadeIn {
  opacity: 0;
  animation: fadeIn ease-in 1;
  animation-fill-mode: forwards;
  animation-duration: 1s;
}

.fadeIn.first {
  animation-delay: 0.4s;
}

.fadeIn.second {
  animation-delay: 0.6s;
}

.fadeIn.third {
  animation-delay: 0.8s;
}

.fadeIn.fourth {
  animation-delay: 1s;
}

.underlineHover::after {
  display: block;
  left: 0;
  bottom: -10px;
  width: 0;
  height: 2px;
  background-color: #8d979c;
  content: "";
  transition: width 0.2s;
}

.underlineHover:hover {
  color: #0d0d0d;
}

.underlineHover:hover::after {
  width: 100%;
}

*:focus {
  outline: none;
}

/* Responsive adjustments */
@media (max-width: 1200px) {
  .formContent {
    width: 90%;
    margin-top: 20px;
    padding: 20px;
  }

  #icon {
    width: 300px;
  }

  .formPost {
    width: 80%;
  }
}

@media (max-width: 992px) {
  .formContent {
    width: 80%;
  }

  #icon {
    width: 250px;
  }

  .formPost {
    width: 70%;
  }
}

@media (max-width: 768px) {
  .formContent {
    width: 70%;
  }

  #icon {
    width: 200px;
  }

  .formPost {
    width: 60%;
  }
}

@media (max-width: 576px) {
  .formContent {
    width: 90%;
    padding: 10px;
  }

  #icon {
    width: 150px;
  }

  .formPost {
    width: 90%;
  }

  .logo-arrow {
    display: none;
  }
}

</style>
