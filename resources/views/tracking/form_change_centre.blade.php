<div class="row px-5" style="display: flex; justify-content: left; margin-left: 150px; margin-top: 20px;margin-bottom: 20px;">
    <div class="col-lg-4">
        <div class="form-group" style="margin-bottom: 30px;">
            <label for="name" class="label">Fecha inicio <span class="obligatory">*</span></label>
            <input type="date" id="start_date" name="start_date" max="3000-12-31" min="1000-01-01" value="" class="form-control"></input>
        </div>
        <div class="form-group py-3" style="margin-bottom: 30px;">
            <label for="name" class="label">Fecha fin <span class="obligatory">*</span></label>
            <input type="date" id="end_date" name="end_date" max="3000-12-31" min="1000-01-01" value="" class="form-control text-center"></input>
        </div>
        <div class="form-group" style="margin-top: 30px;">
            <label class="label" for="observations">Observaciones</label>
            <input type="text" class="form-control" id="observations" name="observations" />
        </div>
        <div class="form-group" style="margin-top: 30px;">
            <label class="label" for="observations">Observaciones</label>
            <input type="text" class="form-control" id="observations" name="observations" />
        </div>

        <div class="row" style="margin-top: 125px;">
            <button id="btnSubmit" type="button" class="btn btn-fill btn-success"><span class="material-icons">
                            publish</span>  {{ __('Enviar') }}</button>
            <button id="btnSubmitLoad" type="button" class="btn btn-dark-black" style="display: none">
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                {{ __('Enviando...') }}
            </button>
            <button id="btnBack" href="/config" class="btn btn-fill btn-red-icot">
            <span class="material-icons">
                            arrow_back
                            </span> {{ __('Volver') }}
            </button>

        </div>
    </div>
    <div class="col-sm-2">
    </div>
    <div class="form-group col-lg-3 mx-auto">
        <div class="dropdown bootstrap-select" style="margin-bottom: 30px;">
            <label class="label" for="centre_origin_id">Centro origen <span class="obligatory">*</span></label>
            <select class="selectpicker" name="centre_origin_id" id="centre_origin_id" data-size="7" data-style="btn btn-red-icot btn-round" title="Seleccione Centro Origen" tabindex="-98">
                <option value="{{$centre->id}}" @if (isset($employee) && $centre->id == $employee->centre_id )
            </select>
        </div>
        <div class="dropdown bootstrap-select mt-2">
            <label class="label" for="centre_destination_id">Centro destino <span class="obligatory">*</span></label>
            <select class="selectpicker" name="centre_destination_id" id="centre_destination_id" data-size="7" data-style="btn btn-red-icot btn-round" title="Seleccione Centro Destino" tabindex="-98">
                @foreach ($centres as $centre)
                <option value="{{$centre->id}}" @if (isset($employee) && $centre->id == $employee->centre_id )
                  selected="selected"
                  @endif>{{$centre->name}}</option>
                @endforeach
            </select>
        </div>
        <div class="dropdown bootstrap-select" style="margin-top: 30px;">
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
        <div style="margin-left: 220px;margin-top: 100px;">
            <button id="btnClear" href="#" class="btn btn-fill btn-warning">
            <span class="material-icons">
                            clear_all
                            </span>     {{ __('Limpiar formulario') }}
            </button>
        </div>
    </div>

</div>

<div class="row mb-0 px-5">
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
        font-size: 12px;
    }
</style>


<script type="text/javascript">
    $(function() {

        $("#btnSubmit").on('click', function(e) {
            $('#btnSubmit').hide();
            $('#btnSubmitLoad').show();
            $('#btnSubmitLoad').prop('disabled', true);
            $('form#createRequestChangeCentre').submit();
        });

        $("#btnClear").on('click', function(e) {
            e.preventDefault();
            clearForms();
        });

        function clearForms() {
            $('#centre_origin_id').selectpicker('val','');
            $('#centre_destination_id').selectpicker('val','');
            $('#employee_id').selectpicker('val','');
            $('#start_date').val('');
            $('#end_date').val('');
            $('#observations').val('');
     
        }

    });
</script>