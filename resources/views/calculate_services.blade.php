
@extends('layouts.logged')
@section('content')
@include('inc.navbar')
@include('common.alert')

<div id="alertServicesCalculate" class="alert alert-danger" role="alert" style="display: none">
</div>

<div class="content">
    <div class="container-fluid">
        <div class="col-lg-12">


            <form id="servicesForm" method="POST">
                @csrf
                @method('POST')
                <div class="row">
                    <div class="col-lg-12 mt-2">
                        <div class="card" style="min-height:442px;">
                            <div class="card-header card-header-danger">
                                <h4 class="card-title">Servicios</h5>
                            </div>

                            <div class="card-body" style="margin-top: 30px;margin-bottom: 30px;">
                                <div class="row" style="justify-content: space-evenly;">

                                    <div class="form-group col-md-3">
                                        <div>
                                            <label class="label" for="dateFrom">Fecha desde </label>
                                            <div class=" input-group " style="margin-bottom: 50px;">
                                                <input type="date" id="date_from" name="date_from" max="3000-12-31" min="1000-01-01" class="form-control"></input>
                                            </div>


                                            <label class="label" for="dateTo">Fecha hasta </label>
                                            <div class=" input-group ">
                                                <input type="date" id="date_to" name="date_to" max="3000-12-31" min="1000-01-01" class="form-control"></input>
                                            </div>
                                        </div>

                                    </div>

                                    <div class="form-group col-md-4" style="justify-content: right;">
                                        <div class="dropdown bootstrap-select" style="margin-bottom: 50px;">
                                            <select class="selectpicker" name="centre_id" id="centre_id" data-size="7" data-style="btn btn-red-icot btn-round" title=" Seleccione Centro" tabindex="-98">
                                                <option>SIN SELECCION </option>
                                             
                                                @foreach ($centres as $centre)
                                                <option value="{{ $centre->id }}" selected>{{ $centre->name }}</option>
                                                @endforeach
                                         
                                            </select>
                                            <input type="hidden" name="centre" id="centre" />
                                        </div>
                                        <div class="dropdown bootstrap-select text-uppercase">
                                            <select class="selectpicker" name="service_id" id="service_id" data-size="7" data-style="btn btn-red-icot btn-round" title=" Seleccione Servicio" tabindex="-98">
                                                <option>TODOS</option>
                                                @foreach ($services as $service)
                                                <option value="{{ $service->id }}">
                                                    {{ $service -> name }}
                                                </option>
                                                @endforeach

                                            </select>
                                            <input type="hidden" name="service" id="service" />
                                        </div>



                                    </div>
                                </div>
                                <div class="row mt-5 px-5" style="justify-content: right; margin-right: 80px;">
                                    <div class="">
                                        <button id="btnClear" href="#" class="btn btn-fill btn-warning">
                                            <span class="material-icons mr-1">
                                                clear_all
                                            </span> {{ __('Limpiar formulario') }}
                                        </button>
                                        <button id="btnSubmitFind" type="submit" class="btn btn-fill btn-success"><span class="material-icons">
                                                search</span> {{ __('Buscar') }}</button>
                                                <button id="btnSubmitFindLoad" type="submit" class="btn btn-success" style="display: none">
                                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                            {{ __('Obteniendo datos...') }}
                                        </button>
                                        <button id="btnSubmit" type="submit" class="btn btn-dark-black"><span class="material-icons">
                                                file_download
                                            </span> {{ __('Exportar') }}</button>
                                        <button id="btnSubmitLoad" type="submit" class="btn btn-dark-black" style="display: none">
                                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                            {{ __('Exportando datos...') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>
            </form>

        


        </div>

        <div class="row" id="servicesData">
            <div class="col-lg-12">
                <table class="table table-striped table-bordered services-datatable col-lg-12">
                    <thead class="table-header">
                        <tr>
                            <th>Centro</th>
                            <th>Servicio</th>
                            <th>Precio</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</div>

<script type="text/javascript">

var table;
var columnsFilled = [];
columnsFilled.push({
    data: 'centre_name', // Nombre del centro
    name: 'centre_name',
    searchable: true
});


columnsFilled.push({
    data: 'service',
    name: 'service',
    searchable: true
});
columnsFilled.push({
    data: 'price',
    name: 'price',
    searchable: true
});
columnsFilled.push({
    name: 'total',
    data: 'total'
});


$(function() {
    setDate();

    $(".nav-item").each(function() {
        $(this).removeClass("active");
    });
    $('#servicesData').hide();

    // Buscar
    $("#btnSubmitFind").on('click', function(e) {
        e.preventDefault();
        $('#btnSubmitFind').hide();
        $('#btnSubmitFindLoad').show();
        $('#btnSubmitFindLoad').prop('disabled', true);
        drawTable();
        $('#servicesData').show();
    });

});


 function clearForms() {
            setDate();
            $('select#centre_id').val('');
            $('select#service_id').val('');
            $('select#centre_id').selectpicker("refresh");
            $('select#employee_id').selectpicker("refresh");
            $('select#service_id').selectpicker("refresh");
            $('input[type="search"]').val('');
            table.search('').draw();
            table.ajax.reload();
        }
        
        $("#btnClear").on('click', function(e) {

            e.preventDefault();
            clearForms();
        });
 
        function drawTable() {
    var centreId = $('#centre').val(); // Cambiado para usar el valor del ID
    var serviceId = $('#service_id').val(); // Cambiado para usar el valor del ID

    // Esta configuración reemplaza a 'params' para ajustarse a la espera de DataTables
    var ajaxData = function(d) {
        d._token = "{{ csrf_token() }}";
        d.centre_id = centreId; 
        d.service_id = serviceId; 
        d.dateTo = $('#date_to').val();
        d.dateFrom = $('#date_from').val();
    };

    if (!$.fn.dataTable.isDataTable('.services-datatable')) {
        table = $('.services-datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{route("services.getSalesServices")}}',
                type: "POST",
                data: ajaxData // Aquí usamos la función definida
            },
            columns: columnsFilled, // Asegúrate de que esta configuración coincide con los datos devueltos
            search: {
                "regex": true,
                "smart": true
            },
            language: {
                "url": "{{ asset('dataTables/Spanish.json') }}" 
            }
        });
    } else {
        table.ajax.reload();
    }
}




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
@endsection