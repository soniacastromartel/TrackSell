@extends('layouts.logged')

@section('content')
@include('inc.navbar')
@include('common.alert')

<div class="content">
    <div class="container-fluid" style="margin-top:50px">
        <div class="row">
            <div class="col-md-12">
                <div class="card ">
                    <div class="card-header card-header-info card-header-text">
                      <div class="card-text">
                        <h4 class="card-title">Modificar centro</h4>
                      </div>
                    </div>
                    <div class="card-body ">
                        <form id="editCentre" action="{{ route('centres.update', $centre->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        @include('admin.centres.form')
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
        $('#adminCentre').addClass('active')

        $("#btnSubmit").on('click', function(){
            $('#btnSubmit').hide();
            $('#btnSubmitLoad').show();
            $('#btnSubmitLoad').prop('disabled', true);
            $("#editCentre").submit();
        });
        $("#btnBack").on('click', function(){
            window.location.href = "{{ route('centres.index') }}"; 
            return false;
        });
    });

</script>
@endsection