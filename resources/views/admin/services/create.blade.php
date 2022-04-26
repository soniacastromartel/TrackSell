@extends('layouts.logged')

@section('content')

@include('inc.navbar')

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

<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card ">
                    <div class="card-header card-header-info card-header-text">
                      <div class="card-text">
                        <h4 class="card-title">Nuevo servicio</h4>
                      </div>
                    </div>
                    <div class="card-body ">
                        <form id="createService" action="{{ route('services.store') }}" method="POST" enctype="multipart/form-data">
            
                            @csrf
            
                            @method('POST')
                            @include('admin.services.form')
                        
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
        $('#adminService').addClass('active')

        $("#btnSubmit").on('click', function(){
            $("#createService").submit();
        });
        $("#btnBack").on('click', function(){
            window.location.href = "{{ route('services.index') }}"; 
            return false;
        });
    });

</script>
@endsection