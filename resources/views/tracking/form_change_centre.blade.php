<div class="row px-5" style="display: flex; justify-content: space-between; margin-left: 80px; margin-top: 20px;">
    <div class="col-lg-3" >
        <div class="form-group" style= "margin-bottom: 30px;">
            <label for="name"  class="label">Fecha inicio <span class="obligatory">*</span></label>
            <input type="date" id="start_date" name="start_date" max="3000-12-31" min="1000-01-01" value="" class="form-control"></input>
        </div>
        <div class="form-group py-3">
            <label for="name" class="label">Fecha fin <span class="obligatory">*</span></label>
            <input type="date" id="end_date" name="end_date" max="3000-12-31" min="1000-01-01" value="" class="form-control"></input>
        </div>
    </div>
    <div class="form-group col-lg-3" style="margin-left:80px;padding-right: 80px;">
        <div class="dropdown bootstrap-select" style= "margin-bottom: 30px;">
            <label class="label" for="centre_origin_id">Centro origen <span class="obligatory">*</span></label>
            <select class="selectpicker" name="centre_origin_id" id="centre_origin_id" data-size="7" data-style="btn btn-red-icot btn-round" title="* Seleccione Centro" tabindex="-98">
                @foreach ($centres as $centre)
                <option value="{{$centre->id}}" selected="selected">{{$centre->name}}</option>
                @endforeach
            </select>
        </div>
        <div class="dropdown bootstrap-select mt-2">
            <label class="label" for="centre_destination_id">Centro destino <span class="obligatory">*</span></label>
            <select class="selectpicker" name="centre_destination_id" id="centre_destination_id" data-size="7" data-style="btn btn-red-icot btn-round" title="* Seleccione Centro" tabindex="-98">
                @foreach ($centres as $centre)
                <option value="{{$centre->id}}" selected="selected">{{$centre->name}}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="form-group col-md-5" style="margin-left:50px;padding-right: 50px;">
        <div class="row" >
            <div class="dropdown bootstrap-select flex-column col-md-7" style="display: flex;margin-bottom: 30px;">
                <label class="label" for="employee_id">Empleado <span class="obligatory">*</span></label>
                <select class="selectpicker pl-0" name="employee_id" id="employee_id" data-size="7" data-style="btn btn-red-icot btn-round" title=" Seleccione Empleado" tabindex="-98">
    
                    @foreach ($employees as $employee)
                    <option value="{{$employee->id}}" @if (isset($tracking) && $employee->id == $tracking->employee_id )
                        selected="selected"
                        @endif
                        >{{$employee->name}}</option>
                    @endforeach
                </select>
                <input type="hidden" name="employee" id="employee" />
            </div>
        </div>
        <div class="form-group col-md-9"  >
            <label class="label"for="observations" >Observaciones</label>
            <input type="text" class="form-control" id="observations" name="observations"  style="padding: 10px; max-width:95%;margin-left:5px; font-weight:900;"/>
        </div>
    </div>
</div>



<div class="row mt-2 px-5">
    <div class="col-md-5" style= "margin-left: 50px;margin-top: 20px;">
        <button id="btnSubmit" type="button" class="btn btn-fill btn-success">{{ __('Enviar') }}</button>
        <button id="btnSubmitLoad" type="button" class="btn btn-success" style="display: none">
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            {{ __('Enviando...') }}
        </button>
        <button id="btnBack" href="/config" class="btn btn-fill btn-red-icot">
            {{ __('Volver') }}
        </button>
    </div>
</div>

<div class="row mb-0 px-5" style= "margin-left: 50px;">
  <div class="col-md-8">
    <label id="lbl"><span class="obligatory">*</span> Estos campos son requeridos</label>
  </div>
</div>

<style>
  #lbl {
        color: black;
        font-weight: 600;
        font-family: 'Helvetica', 'Arial', sans-serif;
        margin-top: 25px;
        font-size:12px;
      }

</style>


<script type="text/javascript">
    $(function() {

        $("#btnSubmit").on('click', function(e) {
            $('#btnSubmit').hide()
            $('form#createRequestChangeCentre').submit();
        });

    });
</script>