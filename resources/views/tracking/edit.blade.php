@extends('layouts.logged')

@section('content')

@include('inc.navbar')
@include('common.alert')

<div class="content">
    <div class="container-fluid" style="margin-top:100px">
        <div class="row">
            <div class="col-md-12">
                <div class="card ">
                    <div class="card-header card-header-info card-header-text">
                        <div class="card-text">
                            <h4 class="card-title">Editar</h4>
                        </div>
                    </div>
                    <div class="card-body ">
                        <form id="editTracking" action="{{ route('tracking.update', [$state, $tracking->id]) }}" method="POST">
                        @csrf
                        @method('PUT')
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
        $(".nav-item").each(function() {
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
