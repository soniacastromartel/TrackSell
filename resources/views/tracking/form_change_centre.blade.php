<div class="row px-5" style="display: flex; justify-content: space-between;">
    <div class="col-lg-2">
        <div class="form-group">
            <label for="name">Fecha inicio <span class="obligatory">*</span></label>
            <input type="date" id="start_date" name="start_date" max="3000-12-31" min="1000-01-01" value="" class="form-control"></input>
        </div>
        <div class="form-group py-3">
            <label for="name">Fecha fin <span class="obligatory">*</span></label>
            <input type="date" id="end_date" name="end_date" max="3000-12-31" min="1000-01-01" value="" class="form-control"></input>
        </div>
    </div>
    <div class="form-group col-lg-3">
        <div class="dropdown bootstrap-select">
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

    <div class="form-group col-md-5">
        <div class="row">
            <div class="dropdown bootstrap-select flex-column col-md-7" style="display: flex;">
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
        <div class="form-group">
            <label for="observations" >Observaciones</label>
            <textarea class="form-control" id="observations" name="observations" rows="3"></textarea>
        </div>
    </div>
</div>



<div class="row mt-2 px-5">
    <div class="col-md-5">
        <button id="btnSubmit" type="button" class="btn btn-fill btn-success">{{ __('Enviar') }}</button>
        <button id="btnSubmitLoad" type="button" class="btn btn-success" style="display: none">
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            {{ __('Enviando...') }}
        </button>
        <button id="btnBack" href="/config" class="btn btn-fill btn-danger">
            {{ __('Volver') }}
        </button>
    </div>
</div>

<div class="row mb-0 px-5">
  <div class="col-md-8">
    <label class="label"><span class="obligatory">*</span> Estos campos son requeridos</label>
  </div>
</div>


<script type="text/javascript">
    $(function() {

        $("#btnSubmit").on('click', function(e) {
            $('#btnSubmit').hide()
            $('form#createRequestChangeCentre').submit();
        });

    });
</script>