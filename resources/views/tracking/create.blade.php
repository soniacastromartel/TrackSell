@extends('layouts.logged')

@section('content')

@include('inc.navbar')
@include('common.alert')
<link rel="stylesheet" href="{{ asset('/css/buttons.css') }}">

<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card " style="margin-top: 120px">
                    <div class="card-header card-header-danger">
                     
                        <h4 class="card-title">Nuevo seguimiento</h4>
                 
                    </div>
                    <div class="card-body ">
                        <form id="createTracking" action="{{ route('tracking.store') }}" method="POST">
                        
                        @csrf
                        @method('POST')
                        @include('tracking.form')
                        </form>
                    </div>
                </div>
            </div>    
        </div>
    </div>
</div>

<script type="text/javascript">
    $(function () {
        $(".nav-item").each(function(){
            $(this).removeClass("active");
        });
        $('#pagesTracking').addClass('show');
        $('#trackingStarted').addClass('active');
        
        $("#btnBack").on('click', function(){
            window.history.back();
            return false;
        });
    });
</script>

@endsection
