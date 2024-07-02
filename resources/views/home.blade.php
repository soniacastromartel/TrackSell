@extends('layouts.logged')
@section('content')
    @include('inc.dashboard')

    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function() {
            showWelcomeToast('¡Bienvenido a tu Página de Inicio!');
        });

        $(function() {
            $(".nav-item").each(function() {
                $(this).removeClass("active");
            });
            $('#menuHome').addClass('active');


        });
    </script>

@endsection
