<div id="alertErrorTrackingDate" class="alert alert-danger" role="alert" style="display: none">
</div>

<div class="row px-5 mt-2">
  <div class="form-group col-lg-2 mx-auto">
    <div class="form-group mb-3">
      <label clafor="name">Fecha <span class="obligatory">*</span></label>
    </div>
    <input type="date" id="tracking_date" name="tracking_date" max="3000-12-31" 
    min="1000-01-01" value="{{ isset($tracking_date) ? $tracking_date : ''}}" class="form-control"/>
  </div>
  <div class="col-lg-2"></div>
  <div class="col-lg-6 mx-auto">
    <div class="form-group pb-1">
      <label class="label mb-3 text-center" style="width: 100%;" for="name">Tipo de identificación <span class="obligatory">*</span> (Selecciona al menos uno) </label>
    </div>
    <div class="row justify-content-between">
      <div class="form-group col-lg-3 m-0" id='fieldHC'>
        <label class="m-0" id="subLabel" for="name"><strong>H.C.</strong></label>
        <input type="text" class="form-control m-0" name="hc" id="hc"  placeholder="" value="{{ isset($tracking) ? $tracking->hc : ''}}">
      </div> 
    <div class="form-group col-lg-4 m-0" id='fieldDNI'>
      <label class="m-0" id="subLabel" for="name"><strong>DNI</strong> </label>
      <input type="text" class="form-control" name="dni" id="dni"  placeholder="" value="{{ isset($tracking) ? $tracking->dni : ''}}">
    </div>
    <div class="form-group col-lg-4 m-0" id='fieldPhone'>
      <label class="m-0" id="subLabel" for="name"><strong>TELÉFONO</strong>  </label>
      <input type="text" class="form-control" name="phone" id="phone"  placeholder="" value="{{ isset($tracking) ? $tracking->phone : ''}}">
    </div>  
    </div>
  </div>
</div>


<div class="row px-5 mt-2 mb-5">
  <div class="form-group col-lg-4 mx-auto">
    <label class="label" for="patient_name">Paciente <span class="obligatory">*</span></label>
    <input type="text" class="form-control" name="patient_name" id="patient_name"  placeholder="" value="{{ isset($tracking) ? $tracking->patient_name : ''}}">
  </div>
  <div class="form-group col-lg-6 mx-auto">
  <label class="label" for="observations">Observaciones</label>
    <input type="text" class="form-control" name="observations" id="observations"  placeholder="" value="{{ isset($tracking) ? $tracking->observations : ''}}">
  </div>
</div>


<div class="row px-5 justify-content-between">

  <div class="form-group col-md-3">
    <div class="dropdown bootstrap-select">
      <label class="label" for="centre_employee_id">Centro prescriptor <span class="obligatory">*</span></label>
      <select class="selectpicker" name="centre_employee_id" id="centre_employee_id" data-size="7" data-style="btn btn-red-icot btn-round" title="* Seleccione Centro" tabindex="-98">
        
        @foreach ($centres as  $centre)
        <option value="{{$centre->id}}" 
          @if (isset($tracking) && $centre->id == $tracking->centre_employee_id )
                selected="selected"
          @endif
          >{{$centre->name}}</option>
        @endforeach
      </select>
    </div>
  </div>

  <div class="form-group col-md-3">
    <div class="dropdown bootstrap-select">
      <label class="label" for="centre_id">Centro realizador <span class="obligatory">*</span></label>
      <select class="selectpicker" name="centre_id" id="centre_id" data-size="7" data-style="btn btn-red-icot btn-round" title="* Seleccione Centro" tabindex="-98">
        
        @foreach ($centres as  $centre)
        <option value="{{$centre->id}}" 
          @if (isset($tracking) && $centre->id == $tracking->centre_id )
                selected="selected"
          @endif
          >{{$centre->name}}</option>
        @endforeach
      </select>
    </div>
  </div> 

  <div class="form-group col-md-3">
    <div class="dropdown bootstrap-select">
        <label class="label" for="employee_id">Empleado <span class="obligatory">*</span></label>
        <select class="selectpicker" name="employee_id" id="employee_id" data-size="7" data-style="btn btn-red-icot btn-round" title=" Seleccione Empleado" tabindex="-98">
            
            @foreach ($employees as  $employee)
            <option value="{{$employee->id}}"  data-tokens="{{$employee->name}}"  
            @if (isset($tracking) && $employee->id == $tracking->employee_id )
              selected="selected"
            @endif
            >{{$employee->name}}</option>
            @endforeach
        </select>
        <input type="hidden" name="employee" id="employee"/>
    </div>
  </div>
  
  
</div>
<div id ="serviceRow" class="row px-5 justify-content-between">

  <div class="form-group col-md-3">
    <div class="dropdown bootstrap-select">
      <label class="label" for="service_id">Servicio <span class="obligatory">*</span></label>
      <select class="selectpicker" name="service_id" id="service_id" data-size="7" data-style="btn btn-red-icot btn-round"
       title="* Seleccione Servicio" tabindex="-98" {{ $disabledService === true ? 'disabled' : ''}}>
        @foreach ($services as  $service)
         <option value="{{  $service->id  }}" 
          @if (isset($tracking) &&  $service->id ==  $tracking->service_id )
                selected="selected"
          @endif
          >{{$service->name}}</option>
        @endforeach
      </select>
      <input type="hidden" id='service_name' name="service_name" />
    </div>
  </div>

  <div id="quantityContainer" class="form-group col-md-2 py-3">
    <label class="label" for="quantity" class="px-4 py-3" style="font-size:14px">Cantidad <span class="obligatory">*</span></label>
    <input type="number" class="form-control" name="quantity" id="quantity" value="{{ isset($tracking) ? $tracking->quantity : 1}}"/>
  </div>

  <div class="form-group col-md-3">
    <div class="dropdown bootstrap-select">
      <label class="label" for="discount">Descuento <span class="obligatory">*</span></label>
      <select class="selectpicker" name="discount" id="discount" data-size="7" data-style="btn btn-red-icot btn-round" title="SIN DESCUENTO" tabindex="-98">
        <option value="-1">SIN DESCUENTO </option>
        @if (isset($tracking))
          
          @foreach ($discounts as  $discount)
            <option value="{{  $discount->type  }}" 
              @if ($tracking->discount ==  $discount->type )
              selected="selected"
              @endif
            >{{$discount->name}}</option>
          @endforeach
        @endif
      </select>
    </div>
  </div>
</div>
<div id="containerBtns">
  <div class="float-left">
  <button id="btnClear" href="#" class="btn btn-fill btn-warning">
    <span class="material-icons">clear_all</span>{{ __('Limpiar formulario') }}
  </button>
</div>
  <div class="float-right">
      <button id="btnSubmit" type="button" class="btn btn-fill btn-success"> <span class="material-icons mr-1">
                            save
                            </span> {{ __('Guardar') }}</button>
      <button id="btnSubmitLoad" type="button" class="btn btn-success" style="display: none">
        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
        {{ __('Guardando...') }}
      </button>
      <button id="btnBack" href="/config" class="btn btn-fill btn-red-icot">
      <span class="material-icons">
                            arrow_back
                            </span> {{ __('Volver') }}
      </button> 
      <br>
      <label id="lbl" class="float-right mr-4"><span class="obligatory">*</span> Estos campos son requeridos</label>
  </div>
</div>

<style>
  #subLabel{
    color: var(--red-icot);
    font-weight: bold !important;
    font-size: 11px;
  }
  #lbl {
        color: black;
        font-weight: 800;
        font-family: 'Helvetica', 'Arial', sans-serif;
        margin-top: 25px;
        font-size:12px;
      }

    #containerBtns{
      margin-top: 40px;
    }
</style>

<script type="text/javascript">
  $(function () {

    $("#btnSubmit").on('click', function(e){
          $('#btnSubmit').hide()
          if (action == 'create') {
            checkFecha();
          } else {
            $('form#editTracking').submit();
          }
    });
    var status = 'started_date';  
    var action = "{{ request()->segment(2)}}";
    if (action == 'create') {
      $('#serviceRow').hide();
    } else {
      $('#serviceRow').show();
      status = "{{ request()->segment(3)}}";
    }

    function clearForms() {
            $('select').val('');
            $('input').val('');
            $('select').selectpicker("refresh");

        }
        
        $("#btnClear").on('click', function(e) {
            e.preventDefault();
            clearForms();
        });

    function loadServices(centre_id) {
        $.ajax({
                url: "/tracking/refreshServices/" +  centre_id ,
                type: 'get',
                success: function(data, textStatus, jqXHR) {
                    // if success, HTML response is expected, so replace current
                    if(textStatus === 'success') {
                      data = JSON.parse(data);
                      $("#service_id").empty();
                      $.each(data.data, function(index, value) {
                        $("#service_id").append('<option value="'+value.id+'">'+value.name+'</option>');
                      }); 
                      $('#service_id').selectpicker('refresh');        

                    }
                },
                error: function(xhr, status, error) {
                    var response = JSON.parse(xhr.responseText);
                    alert(response.errors); 
                    $('#btnSubmitLoad').hide();
                    $('#btnSubmit').show();
                }

            }).fail(function(jqXHR, textStatus, errorThrown) {
              alert('Error'+jqXHR.responseText);
                
            });
    }

    function resetDiscounts(){
      $("#discount").empty();
      $("#discount").append('<option value="-1">SIN DESCUENTO </option>');
      $('#discount').selectpicker('refresh');        

    }

    function recargarDescuentos(service_id) {

        var centreId = $('#centre_id').val();
        $.ajax({
                url: "/tracking/refreshDiscount/" +  service_id + "/" + centreId,
                type: 'get',
                success: function(data, textStatus, jqXHR) {
                    // if success, HTML response is expected, so replace current
                    if(textStatus === 'success') {
                      data = JSON.parse(data);
                      $("#discount").empty();
                      $("#discount").append('<option value="-1">SIN DESCUENTO </option>');
                      $.each(data.data, function(index, value) {
                        $("#discount").append('<option value="'+value.type+'">'+value.name+'</option>');
                      }); 
                      $('#discount').selectpicker('refresh');              
                    }
                },
                error: function(xhr, status, error) {
                    // var response = JSON.parse(xhr.responseText);
                    // alert(response.errors); 
                    // $('#btnSubmitLoad').hide();
                    // $('#btnSubmit').show();
                }

            }).fail(function(jqXHR, textStatus, errorThrown) { 
                 alert('Error'+jqXHR.responseText);
                
            });
    }

    var accion = "{{ collect(request()->segments())[1] }}";
    if ( accion == 'edit') {
      recargarDescuentos($('#service_id').val()); 
    }

    $('#centre_id').change(function(){
        $('#service_id option').hide();
        loadServices($(this).val()); 
        resetDiscounts();
        $('#serviceRow').show();

    });

    $('#service_id').change(function(){
       var servicio = $('#service_id option:selected').text(); 

       recargarDescuentos($(this).val()); 
    });

    function checkFecha(){
        var trackingDate = $('#tracking_date').val();
        $.ajax({
                url: "/tracking/checkDate/" +  trackingDate + '/' + status ,
                type: 'get',
                async: false, //solution
                success: function(data, textStatus, jqXHR) {
                      
                      if (data.result == "ok") {
                        var service_name = $('#service_id option:selected').text(); 
                        $('#service_name').val(service_name);
                        $('#btnSubmitLoad').show();
                        $('#btnSubmitLoad').prop('disabled', true);
                        $('#alertErrorTrackingDate').hide(); 
                        if (action == 'create') {
                          $('form#createTracking').submit(); 
                        } else {
                          $('form#editTracking').submit(); 
                        }
                      } else {
                        $('#btnSubmitLoad').hide();
                        $('#btnSubmit').show();
                        $('#alertErrorTrackingDate').text(data.message); 
                        $('#alertErrorTrackingDate').show().delay(2000).slideUp(300); 
                      }
                    
                    // if success, HTML response is expected, so replace current
                },
                error: function(xhr, status, error) {
                  alert('Error de validación de fecha');
                }

            }).fail(function(jqXHR, textStatus, errorThrown) {
              alert('Error'+jqXHR.responseText);
        });
    }
    
  });
    
</script> 