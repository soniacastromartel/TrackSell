<link rel="stylesheet" href="{{ asset('/css/tracking.css') }}">

<div class="request-container">

    <div class="col-md-2">

        <label for="name" class="label">Fecha inicio</label>
        <input type="date" id="start_date" name="start_date" max="3000-12-31" min="1000-01-01" value=""
            class="form-control"></input>

        <label for="name" class="label">Fecha fin</label>
        <input type="date" id="end_date" name="end_date" max="3000-12-31" min="1000-01-01" value=""
            class="form-control"></input>

        <label class="label" for="observations">Observaciones</label>
        <input type="text" class="form-control" id="observations" name="observations" />

    </div>

    <div class="picker-container">

        <select class="selectpicker" name="centre_origin_id" id="centre_origin_id" data-size="7"
            data-style="btn btn-red-icot" title="Centro Origen" tabindex="-98">
            @foreach ($centres as $centre)
                <option value="{{ $centre->id }}" @if (isset($employee) && $centre->id == $employee->centre_id) selected="selected" @endif>
                    {{ $centre->name }}</option>
            @endforeach
        </select>

        <select class="selectpicker" name="centre_destination_id" id="centre_destination_id" data-size="7"
            data-style="btn btn-red-icot" title="Centro Destino" tabindex="-98">
            @foreach ($centres as $centre)
                <option value="{{ $centre->id }}" @if (isset($employee) && $centre->id == $employee->centre_id) selected="selected" @endif>
                    {{ $centre->name }}</option>
            @endforeach
        </select>

        <select class="selectpicker pl-0" name="employee_id" id="employee_id" data-size="7"
            data-style="btn btn-red-icot" title=" Empleado" tabindex="-98">

            @foreach ($employees as $employee)
                <option value="{{ $employee->id }}" @if (isset($tracking) && $employee->id == $tracking->employee_id) selected="selected" @endif>
                    {{ $employee->name }}</option>
            @endforeach
        </select>
        <input type="hidden" name="employee" id="employee" />

    </div>

    <div class="btn-container-box">

        <div class="btn-container">

            <button id="btnClear" href="#" class="btn btn-lg-fill btn-warning">
                <span class="material-icons mr-1">clear_all</span>{{ __('Limpiar formulario') }}
            </button>

            <button id="btnSubmit" type="button" class="btn btn-fill btn-success">
                <span class="material-icons mr-1">publish</span>{{ __('Enviar') }}</button>
            <button id="btnSubmitLoad" type="button" class="btn btn-fill btn-success" style="display: none">
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                {{ __('Enviando...') }}
            </button>
            <button id="btnBack" href="/config" class="btn btn-fill btn-red-icot">
                <span class="material-icons mr-1">arrow_back</span>{{ __('Volver') }}
            </button>
        </div>
    </div>
</div>


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
            $('#centre_origin_id').selectpicker('val', '');
            $('#centre_destination_id').selectpicker('val', '');
            $('#employee_id').selectpicker('val', '');
            $('#start_date').val('');
            $('#end_date').val('');
            $('#observations').val('');
        }

    });
</script>
