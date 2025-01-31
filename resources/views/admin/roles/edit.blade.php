@extends('layouts.logged')

@section('content')

@include('inc.navbar')
@include('common.alert')

<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card ">
                    <div class="card-header card-header-info card-header-text">
                      <div class="card-text">
                        <h4 class="card-title">Modificar rol</h4>
                      </div>
                    </div>
                    <div class="card-body ">
                        <form id="editRole" action="{{ route('roles.update', $role->id) }}" method="POST">
                        
                            @csrf

                            @method('PUT')
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
            $("#editRole").submit();
        });
        $("#btnBack").on('click', function(){
            window.location.href = "{{ route('roles.index') }}"; 
            return false;
        });
    });
</script>
@endsection