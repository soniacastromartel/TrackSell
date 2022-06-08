<div class="row px-5" style="display: flex; justify-content: left; margin-top: 20px;">
    <div class="col-lg-3 mx-auto">
        <div class="form-group" style="margin-bottom: 30px;">
            <label for="name" class="label">Fecha inicio <span class="obligatory">*</span></label>
            <input type="date" id="start_date" name="start_date" max="3000-12-31" min="1000-01-01" value="" class="form-control text-center"></input>
        </div>
        <div class="form-group py-3" style="margin-bottom: 30px;">
            <label for="name" class="label">Fecha fin <span class="obligatory">*</span></label>
            <input type="date" id="end_date" name="end_date" max="3000-12-31" min="1000-01-01" value="" class="form-control text-center"></input>
        </div>
        <div class="form-group" style="margin-top: 30px;">
            <label class="label" for="observations">Observaciones</label>
            <input type="text" class="form-control" id="observations" name="observations" />
        </div>
    </div>
    <div class="form-group col-lg-3 mx-auto">
        <div class="dropdown bootstrap-select" style="margin-bottom: 30px;">
            <label class="label" for="centre_origin_id">Centro origen <span class="obligatory">*</span></label>
            <select class="selectpicker" name="centre_origin_id" id="centre_origin_id" data-size="7" data-style="btn btn-red-icot btn-round" title="Seleccione Centro Origen" tabindex="-98">
                @foreach ($centres as $centre)
                <option value="{{$centre->id}}" @if (isset($employee) && $centre->id == $employee->centre_id )
                    selected="selected"
                    @endif>{{$centre->name}}</option>
                @endforeach
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
    </div>

    <div class="col-lg-12">
        <div class="mt-4">
            <div id="containerClear" style="display: inline-block;">
                <button id="btnClear" href="#" class="btn btn-lg-fill btn-warning">
                <span class="material-icons mr-1">clear_all</span>{{ __('Limpiar formulario') }}
                </button>
            </div>
            
            <div class="containerBtns" style="float: right;">
                <button id="btnSubmit" type="button" class="btn btn-fill btn-success">
                <span class="material-icons mr-1">publish</span>{{ __('Enviar') }}</button>
                <button id="btnSubmitLoad" type="button" class="btn btn-fill btn-success" style="display: none">
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    {{ __('Enviando...') }}
                </button>
                <button id="btnBack" href="/config" class="btn btn-fill btn-red-icot">
                <span class="material-icons mr-1">arrow_back</span>{{ __('Volver') }}
                </button>
                <div class="text-right mr-3 mt-2">
                    <label id="lbl"><span class="obligatory mt-3 ml-2">*</span> Estos campos son requeridos</label> 
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    #lbl {
        color: black;
        font-weight: 600;
        font-family: 'Helvetica', 'Arial', sans-serif;
        font-size: 12px;
    }

    #containerClear{
        margin-left: 195px;
    }
    .containerBtns{
        margin-right: 185px;
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