@extends('layouts.logged')
@section('content')
@include('inc.navbar')

<link rel="stylesheet" href="{{ asset('/css/buttons.css') }}">
<link rel="stylesheet" href="{{ asset('/css/roles.css') }}">

<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card" style="margin-top: 120px">
                    <div class="card-header card-header-danger">
                        <h4 class="card-title">{{ isset($role) ? 'Modificar rol' : 'Nuevo rol' }}</h4>
                    </div>
                    <div class="card-body ">
                        <form id="roleForm" action="{{ isset($role) ? route('roles.update', $role->id) : route('roles.store') }}" method="POST">
                            @csrf
                            @if(isset($role))
                                @method('PUT')
                            @else
                                @method('POST')
                            @endif
                            @include('admin.roles.form')
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
        $('#pagesConfig').addClass('show');
        $('#adminRole').addClass('active');
        
        $("#btnSubmit").on('click', function(){
            $('#btnSubmit').hide();
            $('#btnSubmitLoad').show();
            $('#btnSubmitLoad').prop('disabled', true);
            $("#roleForm").submit();
        });

        $("#btnBack").on('click', function(){
            window.location.href = "{{ route('roles.index') }}"; 
            return false;
        });
    });
</script>
@endsection
