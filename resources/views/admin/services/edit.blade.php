@extends('layouts.logged')

@section('content')

@include('inc.navbar')
@include('common.alert')

<div class="content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card ">
                    <div class="card-header card-header-info card-header-text">
                      <div class="card-text">
                        <h4 class="card-title">Modificar servicio</h4>
                      </div>
                    </div>
                    <div class="card-body ">
                        <form id="editService" action="{{ route('services.update', $service->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
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
            $("#editService").submit();
        });
        $("#btnBack").on('click', function(){
            window.location.href = "{{ route('services.index') }}"; 
            return false;
        });
    });

</script>
@endsection