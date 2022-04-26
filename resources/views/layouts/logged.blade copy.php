<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Scripts -->
    <script src="{{asset('js/app.js')}}"></script>
    <script src = "https://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
 
    <script src="{{asset('js/bootstrap-material-design.min.js')}}"></script>
    <script src="{{asset('js/material-dashboard.min.js')}}"></script>
    <script src="{{asset('js/MonthPicker.min.js')}}"></script>
    
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="{{ asset('assets/img/LogoICOT.png') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    
    <link rel="stylesheet" href="{{ asset('css/material.css') }}">
    <link rel="stylesheet" href="{{ asset('css/material-lite.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/font-google-material-icons.css') }}" />
    <link rel="stylesheet" type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/themes/base/jquery-ui.css"/>
    <link rel="stylesheet" type="text/css" href="{{ asset('css/MonthPicker.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('css/jquery.dataTables.min.css') }}" />
    {{-- <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.22/css/dataTables.bootstrap4.min.css"/> --}}
    {{-- <link rel="stylesheet" type="text/css" href="https://code.jquery.com/ui/1.11.3/themes/smoothness/jquery-ui.css"/> --}}
    {{-- <link rel="stylesheet" href="//cdn.jsdelivr.net/chartist.js/latest/chartist.min.css"> --}}
    
    <style>
        .material-icons {
            font-family: 'Material Icons';
            font-weight: normal;
            font-style: normal;
            font-size: 24px;
            line-height: 1;
            letter-spacing: normal;
            text-transform: none;
            display: inline-block;
            white-space: nowrap;
            word-wrap: normal;
            direction: ltr;
            -moz-font-feature-settings: 'liga';
            -moz-osx-font-smoothing: grayscale;
            }
        /* these styles will animate bootstrap alerts. */
        .alert{
            z-index: 99;
            top: 60px;
            right:18px;
            min-width:30%;
            position: fixed;
            animation: slide 0.5s forwards;
        }
        @keyframes slide {
            100% { top: 30px; }
        }
        @media screen and (max-width: 668px) {
            .alert{ /* center the alert on small screens */
                left: 10px;
                right: 10px; 
            }
        }
    </style>
    <title>{{config('app.name')}}</title>
</head>
<body>
    <div class="wrapper ">
    
        {{-- @auth --}}
        @include('inc.sidebar')
        {{-- @endauth --}}
        <div class="main-panel">
            @yield('content')

            <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

             {{--<script src="{{asset('js/material-dashboard.min.js')}}"></script>
            <script src = "https://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
            <script src="{{asset('js/bootstrap-material-design.min.js')}}"></script>
            <script src="{{asset('js/MonthPicker.min.js')}}"></script>

            <script src="{{ asset('js/jquery.dataTables.min.js') }}"></script>
            <script src="{{ asset('js/dataTables.bootstrap4.min.js') }}"></script>
            <script src="{{asset('js/bootstrap-selectpicker.js')}}"></script> --}}
            
            
            {{-- <script src="{{asset('js/datepicker-es.js')}}"></script>  --}}
            {{-- <script src="{{asset('js/bootstrap-autocomplete.min.js')}}"></script> --}}
            
            {{-- <script src = "https://code.jquery.com/jquery-1.10.2.js"></script>  --}}
            {{-- <script src="https://code.jquery.com/jquery-migrate-3.0.0.min.js"></script>
            <script src = "https://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
            {{-- <script src = "http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/jquery-ui.min.js"></script> --}}
            {{-- <script src="{{ asset('js/jquery.dataTables.min.js') }}"></script>
            <script src="{{ asset('js/dataTables.bootstrap4.min.js') }}"></script>
            <script src="{{asset('js/bootstrap-selectpicker.js')}}"></script> --}}
            
            
            {{-- <script src="{{asset('js/datepicker-es.js')}}"></script>  --}}
            {{-- <script src="{{asset('js/bootstrap-autocomplete.min.js')}}"></script>
            <script src="{{asset('js/MonthPicker.min.js')}}"></script>
            <script src="{{asset('js/bootstrap-material-design.min.js')}}"></script> --}} 

            {{-- <script src="//cdn.jsdelivr.net/chartist.js/latest/chartist.min.js"> --}}
            {{-- <script src = "https://code.jquery.com/jquery-1.10.2.js"></script>  --}}
            {{-- <script src="https://code.jquery.com/jquery-migrate-3.0.0.min.js"></script> --}}
            
            {{-- <script src = "http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/jquery-ui.min.js"></script> --}}
            <script src="{{ asset('js/jquery.dataTables.min.js') }}"></script>
            <script src="{{ asset('js/dataTables.bootstrap4.min.js') }}"></script>
            <script src="{{asset('js/bootstrap-selectpicker.js')}}"></script>
            
            {{-- <script src="{{asset('js/datepicker-es.js')}}"></script>  --}}
            <script src="{{asset('js/bootstrap-autocomplete.min.js')}}"></script>
            
            
            <script type="text/javascript">
                $(function () {
                    function ocultarAlert(e) {
                        $(e).fadeOut('fast'); 
                    }
                    function timeOutAlert(e){
                        setTimeout( 
                            ocultarAlert.bind(null, e )
                        , 3000);
                    }
                    $('.alert-success').each(function( ) {
                        timeOutAlert(this); 
                    });

                    $('.alert-danger').each(function( ) {
                        timeOutAlert(this); 
                    });
                });
                
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
                </script>
      
            {{-- <script src="{{asset('js/bootstrap-material-design.min.js')}}"></script> --}}
            {{-- <script src="{{asset('js/material-dashboard.min.js')}}"></script> --}}
        </div>
    </div>    
    
</body>
</html>