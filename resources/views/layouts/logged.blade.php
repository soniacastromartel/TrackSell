<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">

<head>
    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
    {{-- <script src="{{ asset('js/formChangeDetection.js') }}"></script> --}}
    <script src="{{ asset('js/jquery-ui-1.12.1/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('js/jquery-migrate-3.0.0.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap-material-design.min.js') }}"></script>
    <script src="{{ asset('js/material-dashboard.min.js') }}"></script>
    <script src="{{ asset('js/MonthPicker.min.js') }}"></script>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width,initial-scale=1">

    {{-- stylesheets --}}
    <link rel="shortcut icon" href="{{ asset('assets/img/LogoICOT.png') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">


    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link rel="stylesheet" href="{{ asset('css/material.css') }}">
    <link rel="stylesheet" href="{{ asset('css/material-lite.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/buttons.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/font-google-material-icons.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('css/jquery-ui-1.12.1/jquery-ui.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('css/MonthPicker.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('css/jquery.dataTables.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('css/logged.css') }}" />


    <link rel="stylesheet" href="{{ asset('css/yearpicker.css') }}">
    <script src="{{ asset('js/yearpicker.js') }}" async></script>
    <!-- <script src="{{ asset('js/jquery.min.js') }}" async></script> -->

    <title>{{ config('app.name') }}</title>

@yield('styles')

</head>

<body>
    <div class="wrapper ">
        {{-- @auth --}}
        @include('inc.sidebar')
        @include('common.alert')
        @include('inc.navbar')

        {{-- @endauth --}}
        <div class="main-panel">

            @yield('content')

            <script type="text/javascript" src="{{ asset('js/charts-loader.js') }}"></script>
            <script src="{{ asset('js/jquery.dataTables.min.js') }}"></script>
            <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
            <script src="{{ asset('js/dataTables.bootstrap4.min.js') }}"></script>
            <script src="{{ asset('js/bootstrap-selectpicker.js') }}"></script>
            <script src="{{ asset('js/bootstrap-autocomplete.min.js') }}"></script>
            <script src="{{ asset('js/moment.min.js') }}"></script>

            <script type="text/javascript">
                $('div.alert').delay(2000).slideUp(300);

                $.MonthPicker = {
                    VERSION: '3.0.4', // Added in version 2.4;
                    i18n: {
                        year: 'Año',
                        prevYear: 'Año previo',
                        nextYear: 'Año siguiente',
                        next12Years: 'Próximos 12 años',
                        prev12Years: 'Pasados 12 años',
                        nextLabel: 'Anterior',
                        prevLabel: 'Sigiente',
                        buttonText: '',
                        jumpYears: 'Saltar años',
                        backTo: 'Volver',
                        months: ['Ene.', 'Feb.', 'Mar.', 'Abr.', 'May.', 'Jun.', 'Jul.', 'Ago.', 'Sep.', 'Oct.', 'Nov.', 'Dic.']
                    }
                };

                $.datepicker.regional['es'] = {
                    closeText: 'Cerrar',
                    prevText: '<Ant',
                    nextText: 'Sig>',
                    currentText: 'Hoy',
                    monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre',
                        'Octubre', 'Noviembre', 'Diciembre'
                    ],
                    monthNamesShort: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
                    dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
                    dayNamesShort: ['Dom', 'Lun', 'Mar', 'Mié', 'Juv', 'Vie', 'Sáb'],
                    dayNamesMin: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sá'],
                    weekHeader: 'Sm',
                    dateFormat: 'yy-mm-dd',
                    firstDay: 1,
                    isRTL: false,
                    showMonthAfterYear: false,
                    yearSuffix: ''
                };
            </script>

            {{-- <!-- SweetAlert Loader -->
            <script>
                var isSearchRequest = false; // Flag para controlar si se está haciendo una solicitud de búsqueda

                // Mostrar el loader cuando se inicie una solicitud AJAX
                $(document).ajaxStart(function() {
                    // Solo mostrar el loader si es una solicitud de datos válida (no la de búsqueda en el DataTable)
                    if (!isSearchRequest) {
                        Swal.fire({
                            title: 'Cargando...',
                            html: 'Por favor, espere...',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            },
                        });
                    }
                });

                // Cerrar el loader cuando se detenga la solicitud AJAX
                $(document).ajaxStop(function() {
                    if (Swal.isVisible() && Swal.getTitle().textContent === 'Cargando...') {
                        setTimeout(function() {
                            Swal.close();
                        }, 8000);
                    }
                });

                // Manejar el cambio en el input de búsqueda
                $('input[type="search"]').on('input', function() {
                    var searchValue = $(this).val().trim();

                    // Solo mostrar la búsqueda en el DataTable si tiene más de 2 caracteres
                    if (searchValue.length >= 3) {
                        isSearchRequest = true; // Indicar que es una solicitud de búsqueda
                    } else {
                        isSearchRequest = false; // Desactivar la búsqueda
                    }
                });
                
            </script> --}}


        </div>
    </div>

</body>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</html>
