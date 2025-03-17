@extends('layouts.logged')
@section('content')
@include('inc.navbar')

<div class="content">
    <div class="container-fluid" style="margin-top:50px">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card ">
                    <div class="card-header card-header-danger">
                        <h4 class="card-title">Modificar servicio</h4>
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


<style>
    .upload-box {
      background: var(--white);
      outline: none;
      border-radius: 30px;

    }

    ::-webkit-file-upload-button {
      font-family: 'Helvetica', 'Arial', sans-serif;
      color: var(--white);
      background: var(--red-icot);
      padding: 8px 23px;
      border: none;
      border-radius: 30px;
      outline: none;
      text-transform: uppercase;
      font-size: 0.75rem;
      font-weight: 600;
      cursor: pointer;
      border: 2px solid var(--red-icot);


    }

    ::-webkit-file-upload-button:hover {
      background: var(--white);
      color: var(--red-icot);
      border: 2px solid var(--red-icot);

    }


    ul.demo {
      list-style-type: circle;
      margin: 0;
      margin-left: 10px;
      text-align: left !important;
      font-size: 12px;
      font-weight: 600;
      font-family: 'Helvetica', 'Arial', sans-serif;

    }

    #serviceImg {
      border: 5px solid var(--red-icot);
      border-radius: 16px;
      margin-bottom: 16px;
    }

    #obligatory {
      color: #CC0000;
      font-weight: bold;
    }
    .btn-save{
            position: relative;   
            background-color: green;
            border: none;
            color: white;
            cursor: pointer;
            border-radius: 50%;
            width: 40px; 
            height: 40px;
            padding: 0; 
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0px 8px 15px rgba(0, 0, 0, 0.1);
            margin: 10px;
            }

            .btn-save::after {
            position: absolute;
            bottom: 100%; 
            left: 50%; 
            transform: translateX(-50%); 
            white-space: nowrap; 
            visibility: hidden; 
            opacity: 0;
            transition: opacity 0.2s, visibility 0.2s; 
            border-radius: 4px;
            font-size: 12px;
            }
            
            .btn-save:hover {
            background-color: white; 
            color: green;   
            }
            
            .btn-save:hover::after {
            visibility: visible;
            opacity: 1;
            }

            .btn-return{
                position: relative;   
                background-color:var(--red-icot);
                border: none;
                color: white;
                cursor: pointer;
                border-radius: 50%;
                width: 40px; 
                height: 40px;
                padding: 0; 
                display: inline-flex;
                align-items: center;
                justify-content: center;
                box-shadow: 0px 8px 15px rgba(0, 0, 0, 0.1);
                margin: 10px;
                }
    
                .btn-return::after {
                position: absolute;
                bottom: 100%; 
                left: 50%; 
                transform: translateX(-50%); 
                white-space: nowrap; 
                visibility: hidden; 
                opacity: 0;
                transition: opacity 0.2s, visibility 0.2s; 
                border-radius: 4px;
                font-size: 12px;
                }
                
                .btn-return:hover {
                background-color: white; 
                color:var(--red-icot);   
                }
                
                .btn-return:hover::after {
                visibility: visible;
                opacity: 1;
                }
  </style>