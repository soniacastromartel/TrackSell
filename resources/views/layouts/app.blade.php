<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Scripts -->
    <script src="{{asset('js/app.js')}}"></script>
    
    
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="shortcut icon" href="{{ asset('assets/img/LogoICOT.png') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/logged.css') }}" />
    
    {{-- <link rel="stylesheet" href="{{ asset('css/material.css') }}"> --}}
    {{-- <link rel="stylesheet" href="{{ asset('css/mdb.min.css') }}"> --}}
    
    <title>{{config('app.name')}}</title>
    
</head>
<body>
    <div class="wrapper ">
        
        <div class="main-panel">
            @yield('content')
            <script src="{{ asset('js/jquery.dataTables.min.js') }}"></script>
            <script src="{{ asset('js/dataTables.bootstrap4.min.js') }}"></script>
            <script src="{{asset('js/bootstrap-selectpicker.js')}}"></script>
            <script src="{{asset('js/bootstrap-material-design.min.js')}}"></script>
            {{-- <script src="{{asset('js/mdb.min.js')}}"></script> --}}

            {{-- <script type="text/javascript">
                $(function () {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                });
            </script> --}}

            {{--<script src="{{asset('js/material-dashboard.min.js')}}"></script> --}}
        </div>
    </div>    
    
</body>
</html>