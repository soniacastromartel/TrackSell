@extends('layouts.logged')


@section('content')

<div class="alert alert-success" role="alert">
  <span> Bienvenido {{$user->username}} a su zona de empleado</span> 
</div>


@include('inc.navbar')
@include('inc.dashboard')
@include('common.alert')

<script type="text/javascript">

   $(function () {
        $(".nav-item").each(function(){
            $(this).removeClass("active");
        });
        $('#menuHome').addClass('active');
        

   });
</script>
@endsection