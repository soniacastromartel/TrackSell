<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
@extends('layouts.logged')
@section('content')
@include('inc.navbar')
@include('common.alert')

<link rel="stylesheet" href="{{ asset('/css/tracking.css') }}">
<link rel="stylesheet" href="{{ asset('/css/buttons.css') }}">

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
                        <div class="card" style="min-height:442px;margin-top:100px"">
                            <div class="card-header card-header-danger">
                                <h4 class="card-title">Servicios</h5>
                            </div>

                            <div class="card-body">
                                <div class="service-container" >

                                    <div class="date-services-container">
                                        <div>
                                            <label class="label" for="dateFrom" style="padding: 10px" >Fecha desde </label>
                                            <div class="icon-container">
                                                <input type="date" id="date_from" name="date_from" max="3000-12-31" min="1000-01-01" class="form-date">
                                                <span id="icon-date-left" class="material-symbols-outlined"> calendar_month</span>
                                            </div>
                                            </input>
                                          


                                            <label class="label" for="dateTo" style="padding: 10px" >Fecha hasta </label>
                                            <div class="icon-container">
                                                <input type="date" id="date_to" name="date_to" max="3000-12-31" min="1000-01-01" class="form-date">
                                                <span id="icon-date-left" class="material-symbols-outlined"> calendar_month</span>
                                            </div>
                                            </input>
                                           
                                        </div>

                                    </div>

                                    <div class="picker-btn-container">

                                    <div class="picker-container">
                                       
                                            <select class="selectpicker" name="centre_id" id="centre_id" data-size="7" data-style="btn btn-red-icot" title=" Centro" tabindex="-98">
                                                <option>SIN SELECCION </option>
                                                @if ($user->rol_id != 1)
                                                @foreach ($centres as $centre)
                                                <option value="{{ $centre->id }}" selected>{{ $centre->name }}</option>
                                                @endforeach
                                                @endif
                                            </select>
                                            <input type="hidden" name="centre" id="centre" />
                                       
                                        <div class="dropdown bootstrap-select text-uppercase">
                                            <select class="selectpicker" name="service_id" id="service_id" data-size="7" data-style="btn btn-red-icot" title=" Servicio" tabindex="-98">
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
                            
                                    <div class="btn-container-box">
                                        <div class="btn-container">

                                        <button id="btnClear" href="#" class="btn-refresh">
                                            <span id="icon-refresh" class="material-icons">refresh</span> {{ __('Limpiar formulario') }}
                                        </button>

                                        <button id="btnSubmitFind" type="submit" class="btn-search">
                                            <span id="icon-search" class="material-icons">search</span> {{ __('Buscar') }}</button>
                                                <button id="btnSubmitFindLoad" type="submit" class="btn-search" style="display: none">
                                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            
                                        </button>
                                        <button id="btnSubmit" type="submit" class="btn-export">
                                            <span id="icon-export"  class="material-icons">
                                                file_download
                                            </span> {{ __('Exportar') }}</button>
                                        <button id="btnSubmitLoad" type="submit" class="btn-export" style="display: none">
                                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                           
                                        </button>
                                    </div>
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

    function drawTable() {
            $('#centre').val($("#centre_id option:selected").text());
            $('#service').val($("#service_id option:selected").text());


            params = {};
            params["_token"] = "{{ csrf_token() }}";
            params["centre"] = $('#centre').val();
            // params["service"] = $('#service').val();
            params['dateTo'] = $('#date_to').val();
            params['dateFrom'] = $('#date_from').val();

            console.log(params);
            // params["monthYear"] = $('#monthYear').val();

        if ($.fn.dataTable.isDataTable('.services-datatable')) {
            table = $('.services-datatable').DataTable();
        } else {
            table = $('.services-datatable').DataTable({
                // order: [7, "desc"],
                processing: true,
                serverSide: true,
                language: {
                    "url": "{{ asset('dataTables/Spanish.json') }}"
                },
                ajax: {
                    url: '{{route("services.getSaledServices")}}',
                    type: "POST",
                    data: params,
                    dataSrc: function(json) {
                        if (json == null) {
                            return [];
                        } else {
                            return json.data;
                        }
                    }
                },
                columns: columnsFilled,
                search: {
                    "regex": true,
                    "smart": true
                },
                initComplete: function() {
                    this.api().columns().every(function() {
                        var column = this;
                    });
                }
            });
        }
        table.columns.adjust().draw();
    }


    function setDate() {
        var date = new Date();
        var day = date.getDate();
        var month = date.getMonth() + 1;
        var year = date.getFullYear();
        var startDay = 21;


        day = day >= 10 ? day : '0' + day;
        month = month >= 10 ? month : '0' + month;
        var dateTo = year + '-' + month + '-' + day;

        var previousMonth = 0;
        if (month != 1 && day < 21) {
            previousMonth = month - 1;
        } else if (month != 1 && day >= 21) {
            previousMonth = month;
        } else if (month == 1 && day < 21) {
            previousMonth = 12
            year = year - 1;
        } else if (month == 1 && day >= 21) {
            previousMonth = 01;
        }
        // previousMonth= previousMonth >= 10 ? previousMonth : '0' + previousMonth;
        var dateFrom = year + '-' + previousMonth + '-' + startDay;

        document.getElementById("date_from").value = dateFrom;
        document.getElementById("date_to").value = dateTo;

    }
</script>
@endsection

<style>

    .main-panel {
        background-image: url(/assets/img/background_continue.png) !important;
        background-position: center center !important;
        background-size: 1000px;
    }
    
        </style>