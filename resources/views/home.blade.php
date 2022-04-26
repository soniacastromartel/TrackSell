@extends('layouts.logged')


@section('content')

<div class="alert alert-success" role="alert">
  <span> Bienvenido {{$user->username}} a su zona de empleado</span> 
</div>

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

@include('inc.navbar')

@include('inc.dashboard')

<script type="text/javascript">

   $(function () {
        $(".nav-item").each(function(){
            $(this).removeClass("active");
        });
        $('#menuHome').addClass('active');
        

   });
</script>
@endsection