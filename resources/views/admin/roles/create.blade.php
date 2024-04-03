@extends('layouts.logged')
@section('content')
@include('inc.navbar')
@include('common.alert')

<link rel="stylesheet" href="{{ asset('/css/buttons.css') }}">
<link rel="stylesheet" href="{{ asset('/css/roles.css') }}">

<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card" style="margin-top: 100px">
                    <div class="card-header card-header-info card-header-text">
                      <div class="card-text">
                        <h4 class="card-title">Nuevo rol</h4>
                      </div>
                    </div>
                    <div class="card-body ">
                        <form id="createRole" action="{{ route('roles.store') }}" method="POST">
                        
                            @csrf
                            @method('POST')
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
            $("#createRole").submit();
        });
        $("#btnBack").on('click', function(){
            window.location.href = "{{ route('roles.index') }}"; 
            return false;
        });
    });
</script>
@endsection