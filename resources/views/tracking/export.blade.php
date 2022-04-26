@extends('layouts.logged')

@section('content')

@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
@include('inc.navbar')

<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">

                <div class="card ">
                    <div class="card-header card-header-info card-header-text">
                      <div class="card-text">
                        <h4 class="card-title">Informes</h4>
                      </div>
                    </div>
                    <div class="card-body">

                        <form id="exportTracking" action="{{ route('tracking.export') }}" method="POST">
                        
                        @csrf
                        @method('PUT')
                            <div class="row px-5">
                                <div class="col-md-2">
                                    <label for="name">Fecha desde  </label>
                                    <input type="date" id="date_from" name="date_from" max="3000-12-31" 
                                    min="1000-01-01" class="form-control"></input>
                                </div>
                                <div class="col-md-2">
                                    <label for="name">Fecha hasta  </label>
                                    <input type="date" id="date_to" name="date_to" max="3000-12-31" 
                                    min="1000-01-01" class="form-control"></input>
                                </div>
                            </div>
                            <div class="row ml-5 mt-2">
                                <div class="col-md-4">
                                    
                                    <label class="col-form-label label-checkbox">Elija un estado:</label>
                                    <div class="form-group checkbox-radios">
                                        <div class="form-check">
                                            <label class="form-check-label">
                                                <input class="form-check-input" type="radio" name="trackingState" value="service" checked=""> Realizados
                                                <span class="circle">
                                                <span class="check"></span>
                                                </span>
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <label class="form-check-label">
                                                <input class="form-check-input" type="radio" name="trackingState" value="invoiced"> Facturados
                                                <span class="circle">
                                                <span class="check"></span>
                                                </span>
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <label class="form-check-label">
                                                <input class="form-check-input" type="radio" name="trackingState" value="validation"> Validados
                                                <span class="circle">
                                                <span class="check"></span>
                                                </span>
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <label class="form-check-label">
                                                <input class="form-check-input" type="radio" name="trackingState" value="cancellation"> Eliminados
                                                <span class="circle">
                                                <span class="check"></span>
                                                </span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="row px-5">
                                        <div class="form-group col-md-6">
                                            <div class="dropdown bootstrap-select">
                                                <select class="selectpicker" name="centre_id" id="centre_id" data-size="7" data-style="btn btn-primary btn-round" title=" Seleccione Centro" tabindex="-98">
                                                    
                                                    @foreach ($centres as  $centre)
                                                    <option value="{{$centre->id}}" 
                                                    @if (isset($tracking) && $centre->id == $tracking->centre_id )
                                                            selected="selected"
                                                    @endif
                                                    >{{$centre->name}}</option>
                                                    @endforeach
                                                </select>
                                                <input type="hidden" name="centre" id="centre"/>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <div class="dropdown bootstrap-select">
                                                <select class="selectpicker" name="employee_id" id="employee_id" data-size="7" data-style="btn btn-primary btn-round" title=" Seleccione Empleado" tabindex="-98">
                                                    
                                                    @foreach ($employees as  $employee)
                                                    <option value="{{$employee->id}}" >{{$employee->name}}</option>
                                                    @endforeach
                                                </select>
                                                <input type="hidden" name="employee" id="employee"/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row px-5">
                                        <div class="form-group col-md-6">
                                            <div class="dropdown bootstrap-select">
                                                <select class="selectpicker" name="service_id" id="service_id" data-size="7" data-style="btn btn-primary btn-round" title=" Seleccione Servicio" tabindex="-98">
                                                    
                                                    @foreach ($services as  $service)
                                                    <option value="{{$service->id}}" 
                                                    @if (isset($tracking) && $service->id == $tracking->service_id )
                                                            selected="selected"
                                                    @endif
                                                    >{{$service->name}}</option>
                                                    @endforeach
                                                </select>
                                                <input type="hidden" name="service" id="service"/>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <div class="dropdown bootstrap-select">
                                            <select class="selectpicker" name="patient_name" id="patient_name" data-size="7" data-style="btn btn-primary btn-round" title=" Seleccione Paciente" tabindex="-98">
                                                
                                                @foreach ($patients as  $patient)
                                                <option value="{{$patient->patient_name}}">{{$patient->patient_name}}</option>
                                                @endforeach
                                            </select>
                                        
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-2 px-5">
                                <div class="col-md-5">
                                    <button id="btnClear" href="#" class="btn btn-fill btn-warning">
                                        {{ __('Limpiar formulario') }}
                                        </button> 
                                    <button id="btnSubmit" type="submit" class="btn btn-fill btn-success">{{ __('Imprimir') }}</button>
                                    <button id="btnSubmitLoad" type="submit" class="btn btn-success" style="display: none">
                                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                        {{ __('Exportando datos...') }}
                                    </button>
                                </div>
                            </div>
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
        $('#pagesReport').addClass('show');
        $('#exportRecommendation').addClass('active');

        function clearForms()
        {
            $('select').val('');
            $('select').selectpicker("refresh");
            $("input[name=trackingState][value='service']").prop("checked",true);
        }

        $("#btnSubmit").on('click', function(e){

            e.preventDefault(); 
            $('#btnSubmit').hide();
            $('#btnSubmitLoad').show();
            $('#btnSubmitLoad').prop('disabled', true);
            $('#centre').val($( "#centre_id option:selected" ).text());
            $('#employee').val($( "#employee_id option:selected").text());
            $('#service').val($( "#service_id option:selected" ).text());

            params = {};
            params["_token"]        = "{{ csrf_token() }}";
            params["centre"]        = $('#centre').val();
            params["employee"]      = $('#employee').val();
            params["service"]       = $('#service').val();
            params["trackingState"] = $('input[name="trackingState"]:checked').val();
            params["date_from"]     = $('#date_from').val(); 
            params["date_to"]       = $('#date_to').val();
            
            $.ajax({
                url: $("#exportTracking").attr('action'),
                type: 'post',
                data: params,
                // dataType: 'binary',
                xhrFields: {
                    'responseType': 'blob'
                },
                 success: function(data, textStatus, jqXHR) {
                     // if success, HTML response is expected, so replace current
                     if(textStatus === 'success') {

                         $('#btnSubmitLoad').hide();
                         $('#btnSubmit').show();
                         

                        var link = document.createElement('a'),
                        filename = 'tracking.xls';
                        link.href = URL.createObjectURL(data);
                        link.download = filename;
                        link.click();                      
                    }
                }
            }).fail(function(jqXHR, textStatus, errorThrown) {
                //console.log('fail submit');
                
            });
        });

        $("#btnClear").on('click', function(e){
            e.preventDefault();
            clearForms();
        });
    });
</script>

@endsection