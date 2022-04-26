<div id="alertErrorTrackingDate" class="alert alert-danger" role="alert" style="display: none">
</div>

<div class="row px-5 mt-2">
  <div class="form-group col-lg-2">
    <label id="lbl" for="name">Fecha <span id="obligatory">*</span></label>
    <input type="date" id="tracking_date" name="tracking_date" max="3000-12-31" 
    min="1000-01-01" value="{{ isset($tracking_date) ? $tracking_date : ''}}" class="form-control"/>
  </div>
</div>

<div class="row px-5 mt-2">
  <div class="form-group">
    <label id="lbl" for="name">Tipo de identificación <span id="obligatory">*</span> (Introducir al menos uno) </label>
  </div>
</div>
<div class="row px-5">
  <div class="form-group col-lg-2 ml-4" id='fieldHC'>
    <label id="subLbl" for="name">H.C </label>
    <input type="text" class="form-control" name="hc" id="hc"  placeholder="" value="{{ isset($tracking) ? $tracking->hc : ''}}">
  </div> 
  <div class="form-group col-lg-2 ml-4" id='fieldDNI'>
    <label id="subLbl" for="name">DNI </label>
    <input type="text" class="form-control" name="dni" id="dni"  placeholder="" value="{{ isset($tracking) ? $tracking->dni : ''}}">
  </div>
  <div class="form-group col-lg-2 ml-4" id='fieldPhone'>
    <label id="subLbl" for="name">TELÉFONO  </label>
    <input type="text" class="form-control" name="phone" id="phone"  placeholder="" value="{{ isset($tracking) ? $tracking->phone : ''}}">
  </div> 
</div>

<div class="row px-5 mt-5">
  <div class="form-group col-lg-4">
    <label id="lbl" for="patient_name">Paciente <span id="obligatory">*</span></label>
    <input type="text" class="form-control" name="patient_name" id="patient_name"  placeholder="" value="{{ isset($tracking) ? $tracking->patient_name : ''}}">
  </div> 
</div>

<div class="row px-5">

  <div class="form-group col-md-3">
    <div class="dropdown bootstrap-select">
      <label id="lbl" for="centre_employee_id">Centro prescriptor <span id="obligatory">*</span></label>
      <select class="selectpicker" name="centre_employee_id" id="centre_employee_id" data-size="7" data-style="btn btn-primary btn-round" title="* Seleccione Centro" tabindex="-98">
        
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

  <div class="form-group col-md-4">
    <div class="dropdown bootstrap-select">
        <label id="lbl" for="employee_id">Empleado <span id="obligatory">*</span></label>
        <select class="selectpicker" name="employee_id" id="employee_id" data-size="7" data-style="btn btn-primary btn-round" title=" Seleccione Empleado" tabindex="-98">
            
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
<div class="row px-5">

  <div class="form-group col-md-3">
    <div class="dropdown bootstrap-select">
      <label id="lbl" for="centre_id">Centro realizador <span id="obligatory">*</span></label>
      <select class="selectpicker" name="centre_id" id="centre_id" data-size="7" data-style="btn btn-primary btn-round" title="* Seleccione Centro" tabindex="-98">
        
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

  <div class="form-group col-md-4">
    <div class="dropdown bootstrap-select">
      <label id="lbl" for="service_id">Servicio <span id="obligatory">*</span></label>
      <select class="selectpicker" name="service_id" id="service_id" data-size="7" data-style="btn btn-primary btn-round"
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

  <div class="form-group col-md-2 py-4">
    <label id="lbl" for="quantity" class="px-4 py-3" style="font-size:14px">Cantidad <span id="obligatory">*</span></label>
    <input type="number" class="form-control" name="quantity" id="quantity"  style="display: none" value="{{ isset($tracking) ? $tracking->quantity : 1}}"/>
  </div>
  
</div>

<div class="row px-5">
  <div class="form-group col-md-4">
    <div class="dropdown bootstrap-select">
      <label id="lbl" for="discount">Descuento <span id="obligatory">*</span></label>
      <select class="selectpicker" name="discount" id="discount" data-size="7" data-style="btn btn-primary btn-round" title="SIN DESCUENTO" tabindex="-98">
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

<div class="row px-5">
  <div class="form-group col-md-4">
    <label id="lbl" for="observations">Observaciones</label>
    <textarea class="form-control" id="observations"  name="observations"rows="3">{{ isset($tracking) ? $tracking->observations : ''}}</textarea>
  </div>
</div>
<div class="row mt-2 px-5">
  <div class="col-md-5">
      <button id="btnSubmit" type="button" class="btn btn-fill btn-success">{{ __('Guardar') }}</button>
      <button id="btnSubmitLoad" type="button" class="btn btn-success" style="display: none">
        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
        {{ __('Guardando...') }}
      </button>
      <button id="btnBack" href="/config" class="btn btn-fill btn-danger">
        {{ __('Volver') }}
      </button> 
  </div>
</div>

<div class="row mb-0 px-5">
  <div class="col-md-8">
    <span id="lbl"> * Estos campos son requeridos </span>
  </div>
</div>
<style>
  #lbl{
    color: black;
    font-weight: 600;
    font-family: 'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif;
  }

  #subLbl {
    color: black;
    font-weight: 500;
    font-family: 'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif, Tahoma, Geneva, Verdana, sans-serif;
  }
  #obligatory{
    color: #CC0000;
    font-weight: bold;
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
      $('#service_id').selectpicker('hide');
      $('label[for="service_id"]').hide();
      $('#quantity').hide();
      $('label[for="quantity"]').hide();
    } else {
      $('#quantity').show();
      status = "{{ request()->segment(3)}}";
    }

    function recargarServicios(centre_id) {
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
                
                 alert('Error cargando servicios');
                
            });
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
                
                 alert('Error cargando servicios');
                
            });
    }

    var accion = "{{ collect(request()->segments())[1] }}";
    if ( accion == 'edit') {
      recargarDescuentos($('#service_id').val()); 
    }

    $('#centre_id').change(function(){
        $('#service_id option').hide();
        recargarServicios($(this).val()); 


        $('#service_id').selectpicker('refresh');
        $('#service_id').selectpicker('show');
        $('label[for="service_id"]').show();
    });

    $('#service_id').change(function(){
       var servicio = $('#service_id option:selected').text(); 
       $('#quantity').show();
       $('label[for="quantity"]').show();

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
                        $('#alertErrorTrackingDate').show(); 
                      }
                    
                    // if success, HTML response is expected, so replace current
                },
                error: function(xhr, status, error) {
                  alert('Error de validación de fecha');
                }

            }).fail(function(jqXHR, textStatus, errorThrown) {
                
                 alert('Error de validación de fecha');
        });
    }
    
  });
    
</script> 