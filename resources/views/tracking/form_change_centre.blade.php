<link rel="stylesheet"
    href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
<link rel="stylesheet" href="{{ asset('/css/tracking.css') }}">
<link rel="stylesheet" href="{{ asset('/css/buttons.css') }}">


<div class=" solicitud-container">

    <div class="date-solicitud-container">

        <label for="name" class="label" style="padding: 10px">Fecha inicio</label>
        <div class="icon-container">
            <input type="date" id="date_from" name="date_from" max="3000-12-31" min="1000-01-01"class="form-date">
            <span id="icon-date-left" class="material-symbols-outlined"> calendar_month</span>
        </div>
        </input>

        <label for="name" class="label" style="padding: 10px">Fecha fin</label>
        <div class="icon-container">
            <input type="date" id="date_to" name="date_to" max="3000-12-31" min="1000-01-01" class="form-date">
            <span id="icon-date-left" class="material-symbols-outlined"> calendar_month</span>
        </div>
        </input>
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

        <button id="btnClear" href="#" class="btn-refresh">Limpiar Formulario
            <span id=icon-refresh class="material-icons">refresh</span>
        </button>

        <button id="btnSubmit" type="button" class="btn-send">
            <span id=icon-send class="material-icons">publish</span>{{ __('Registrar cambio') }}</button>
        <button id="btnSubmitLoad" type="button" class="btn btn-fill btn-success" style="display: none">
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
        </button>
        <div class="btn-back-container">
            <button id="btnBack" href="/config" class="btn-return">
                <span class="material-icons">arrow_back</span>
            </button>

        </div>
    </div>

</div>



<script type="text/javascript">
    // setDate();
    $(function() {
        $("#btnSubmit").on('click', function(e) {
            console.log( $('#centre_origin_id').selectpicker('val'));
            console.log($('#centre_destination_id').selectpicker('val'));
            console.log($("#employee_id option:selected").text());
            console.log($('#date_from').val());
            console.log($('#date_to').val());
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
            setDate();
            $('#centre_origin_id').selectpicker('val', '');
            $('#centre_destination_id').selectpicker('val', '');
            $('#employee_id').selectpicker('val', '');
            $('#date_from').val('');
            $('#date_to').val('');
        }
    });

    function setDate() {
        var date = new Date();
        var day = date.getDate();
        var month = date.getMonth() + 1;
        var year = date.getFullYear();
        var startDay = 20;

        day = day < 10 ? '0' + day : day;
        month = month < 10 ? '0' + month : month;

        var dateTo = year + '-' + month + '-' + day;

        var previousMonth = month;
        var previousYear = year;

        if (month === '01' && day < 21) {
            previousMonth = '12';
            previousYear = year - 1;

        } else {
            previousMonth = parseInt(month, 10);
            previousMonth = (day < 21) ? previousMonth - 1 : previousMonth;
            previousMonth = previousMonth < 10 ? '0' + previousMonth : previousMonth.toString();
        }

        var dateFrom = previousYear + '-' + previousMonth + '-' + startDay;
        document.getElementById("date_from").value = dateFrom;
        document.getElementById("date_to").value = dateTo;
    }
</script>
