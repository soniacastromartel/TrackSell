<!DOCTYPE html>
<html>
<link rel="stylesheet" href="{{ asset('/css/buttons.css') }}">
<link rel="stylesheet" href="{{ asset('/css/dinamic.css') }}">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/material.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/logged.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/navbar.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    @extends('layouts.logged')

    @section('content')
        <div class="container-fluid" style="margin-top: 120px;">
            
                <div class="card">
                    <div class="card-header card-header-danger">
                        <h4 class="card-title">Dinámica de Servicios</h4>
                      </div>

                       <div class="card-body">
                           
                    <div class="informes-container" >
                        <div class="date-informes-container">
                            <form id="serviceForm" action="{{ route('calculateServices') }}" method="GET">
                            
                   
                                    <input type="hidden" name="centre_id" value="{{ $centre_id }}">
                                    <input type="hidden" name="service_id" value="{{ $service_id }}">

                                    <label class="label  align-self-center " for="dateFrom" style="padding: 10px">Fecha desde </label>
                                    <div class="icon-container">
                                        <input type="date" class="form-date" id="start_date" name="start_date"
                                            value="{{ request('start_date') }}" onchange="this.form.submit()">
                                        <span id="icon-date-left" class="material-symbols-outlined"> calendar_month</span>
                                    </div>
                                    </input>

                                    <label class="label align-self-center" for="dateTo" style="padding: 10px">Fecha hasta </label>
                                    <div class="icon-container">
                                        <input type="date" class="form-date" id="end_date" name="end_date"
                                            value="{{ request('end_date') }}" onchange="this.form.submit()">
                                        <span id="icon-date-left" class="material-symbols-outlined"> calendar_month</span>
                                    </div>
                                    </input>

                             </form>
                              


                            
                        </div>

                        <div class="container ml-5" style="width: 300px" >
                            
                                <h3>Centro</h3>
                             
                                <form action="{{ route('calculateServices') }}" method="GET">
                                    <input type="hidden" name="service_id" value="{{ $service_id }}">
                                    <select class="selectpicker" data-style="btn btn-red-icot btn-round" id="centre_id"
                                        name="centre_id" onchange="this.form.submit()">
                                        <option value="">TODOS</option>
                                        @foreach ($centres as $centre)
                                            <option value="{{ $centre->id }}"
                                                {{ $centre_id == $centre->id ? 'selected' : '' }}>{{ $centre->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </form>
            
                       
                                <h3>Servicios</h3>
                               
                                <form id="centreForm" action="{{ route('calculateServices') }}" method="GET">
                                    <input type="hidden" name="centre_id" value="{{ $centre_id }}">
                                    <select class="selectpicker" data-style="btn btn-red-icot btn-round" id="service_id"
                                        name="service_id" onchange="this.form.submit()">
                                        <option value="">TODOS</option>
                                        @foreach ($services as $service)
                                            <option value="{{ $service->id }}"
                                                {{ $service_id == $service->id ? 'selected' : '' }}>{{ $service->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </form>

                        </div>
                            
                    </div>
      
                    <div class="" style="display:flex;justify-content:flex-end;">
                        <button id="btnClear" class="btn-refresh" onclick="resetSelectors()">Limpiar Formulario <span
                                id="icon-refresh" class="material-icons">refresh</span></button>
                        <form action="{{ route('export.all-services') }}" method="GET">
                            @csrf
                            <input type="hidden" name="service_id" value="{{ $service_id ?? '' }}">
                            <input type="hidden" name="centre_id" value="{{ $centre_id ?? '' }}">
                            <input type="hidden" name="start_date" id="start_date" value="{{ request('start_date') }}">
                            <input type="hidden" name="end_date" id="end_date" value="{{ request('end_date') }}">
                            <button id="btnSubmit" type="submit" class="btn-export">Exportar <span id="icon-export"
                                    class="material-icons">file_download</span></button>
                            <button id="btnSubmitLoad" type="submit" style="display: none;">
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            </button>
                        </form>
                    </div>
            </div>
        </div>
        @if (empty($service_id) && empty($centre_id))
        <div class="card mt-4">
            <div class="chart-container">
                <h4>Ventas de <strong>SERVICIOS</strong> en <strong>TODOS LOS CENTROS</strong></h4>
                <div>
                    <canvas id="chartServiceAll"></canvas>
                </div>
            </div>
        </div>
        <table class="mt-4 table">
            <thead>
                @if (request('start_date') && request('end_date'))
                    <tr class="row-service" style="background-color: var(--red-icot);color:white;">
                        <th colspan="5">Fecha : {{ request('start_date') }} / {{ request('end_date') }}</th>
                    </tr>
                @endif
                <tr class="row-service">
                    <th>Servicios</th>
                    <th>Realizados</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($servicesCount as $service)
                    <tr>
                        <td>{{ $service->service_name }}</td>
                        <td>{{ $service->cantidad }}</td>
                        <td>{{ $service->price * $service->cantidad }}€</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="card mt-4">
            <div class="chart-container">
                <h4>Ventas de <strong>SERVICIOS</strong> en <strong>TODOS LOS CENTROS</strong> por <strong>CATEGORÍA</strong> de <strong>EMPLEADO</strong></h4>
                <div>
                    <canvas id="chartServiceCategory"></canvas>
                </div>
            </div>
        </div>

        <table class="mt-4 table">
            <thead>
                <tr class="row-service">
                    <th>Categoría de Empleado</th>
                    <th>Realizados</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($serviceEmployeeCategory as $service)
                    <tr>
                        <td>{{ $service->category_name }}</td>
                        <td>{{ $service->cantidad }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        
        <table class="mt-4 table">
            <thead>
                <tr class="row-service">
                    <th>Categoría de Servicio</th>
                    <th>Realizados</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($serviceEmployeeCategory as $service)
                    <tr>
                        <td>{{ $service->category_service }}</td>
                        <td>{{ $service->cantidad }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        
    @endif
            @if (!empty($service_id) && !empty($centre_id))
                <div class="card mt-4">
                    <div class="chart-container">
                        <h4>Servicio <strong>{{ $selectedService->name }}</strong> en
                            <strong>{{ $selectedCentre->name }}</strong></h4>
                        <div>
                            <canvas id="chartCentreService"></canvas>
                        </div>
                    </div>
                </div>
                <table class="mt-4 table">
                    <thead>
                        @if (request('start_date') && request('end_date'))
                            <tr class="row-service" style="background-color: var(--red-icot);color:white;">
                                <th colspan="5">Fecha : {{ request('start_date') }} / {{ request('end_date') }}</th>
                            </tr>
                        @endif
                        <tr class="row-service">
                            <th>Servicios</th>
                            <th>Centro</th>
                            <th>Realizados</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{ $selectedService->name }}</td>
                            <td>{{ $selectedCentre->name }}</td>
                            <td>{{ $totalServices }}</td>
                            <td>{{ $grandTotal }}€</td>
                        </tr>
                    </tbody>
                </table>
          
            @endif

          

            @if (empty($service_id) && !empty($centre_id))
                <div class="card mt-4">
                    <div class="chart-container">
                        <h4><strong>TODOS</strong> los Servicios en <strong>{{ $selectedCentre->name }}</strong></h4>
                        <div>
                            <canvas id="chartCentre"></canvas>
                        </div>
                    </div>
                </div>
                <table class="mt-4 table">
                    <thead>
                        @if (request('start_date') && request('end_date'))
                            <tr class="row-service" style="background-color: var(--red-icot);color:white;">
                                <th colspan="5">Fecha : {{ request('start_date') }} / {{ request('end_date') }}</th>
                            </tr>
                        @endif
                        <tr class="row-service" style="background-color: var(--red-icot);color:white;">
                            <th>Servicios</th>
                            <th>Precio</th>
                            <th>Realizados</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($servicesCountCentre as $service)
                            <tr>
                                <td>{{ $service->service_name }}</td>
                                <td>{{ $service->price }}€</td>
                                <td>{{ $service->total }}</td>
                                <td>{{ $service->price * $service->total }}€</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif

            @if (!empty($service_id) && empty($centre_id))
                <div class="card mt-4">
                    <div class="chart-container">
                        <h4>Ventas de <strong>{{ $selectedService->name }}</strong> en <strong>TODOS LOS CENTROS</strong>
                        </h4>
                        <div>
                            <canvas id="chartServiceAllTotal"></canvas>
                        </div>
                    </div>
                </div>
                <h4>Ventas de <strong>{{ $selectedService->name }}</strong> en <strong>TODOS LOS CENTROS</strong></h4>
                <table class="mt-4 table">
                    <thead>
                        @if (request('start_date') && request('end_date'))
                            <tr class="row-service" style="background-color: var(--red-icot);color:white;">
                                <th colspan="5">Fecha : {{ request('start_date') }} / {{ request('end_date') }}</th>
                            </tr>
                        @endif
                        <tr class="row-service" style="background-color: var(--red-icot);color:white;">
                            <th>Servicio</th>
                            <th>Realizados</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="total-services-row" style="background-color:rgb(212, 209, 209)">
                            <td>{{ $servicesCount->first()->service_name }}</td>
                            <td>{{ $totalServices }}</td>
                            <td>{{ $grandTotal }}€</td>
                        </tr>
                    </tbody>
                </table>
                <div class="card mt-4">
                    <div class="chart-container">
                        <h4>Ventas de <strong>{{ $selectedService->name }}</strong> por <strong>CENTROS</strong></h4>
                        <div>
                            <canvas id="chartService"></canvas>
                        </div>
                    </div>
                </div>
                <table class="mt-4 table">
                    <thead>
                        <tr class="row-service" style="background-color: var(--red-icot);color:white;">
                            <th>Centros</th>
                            <th>Realizados</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($serviceByCentre as $service)
                            <tr>
                                <td>{{ $service->centre_name }}</td>
                                <td>{{ $service->cantidad }}</td>
                                <td>{{ $service->price * $service->cantidad }}€</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif

            {{-- GRÁFICA VENTAS POR CATEGORÍA --}}

            @if (!empty($service_id))
                <div class="card mt-4">
                    <div class="chart-container">
                        <h4>Ventas de <strong>{{ $selectedService->name }}</strong> por <strong>CATEGORÍA</strong></h4>
                        <div>
                            <canvas id="chartServiceCategory"></canvas>
                        </div>
                    </div>
                </div>
            @endif

            @if ((!empty($service_id) && empty($centre_id)) || (!empty($service_id) && !empty($centre_id)))
                <h4>Ventas Servicio <strong>{{ $selectedService->name }}</strong> por <strong>CATEGORÍA</strong></h4>
                <table class="mt-4 table">
                    <thead>
                        <tr class="row-service" style="background-color: var(--red-icot);color:white;">
                            <th>Categoría</th>
                            <th>Realizados</th>

                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($serviceEmployeeCategory as $filterCategory)
                            <tr>
                                <td>{{ $filterCategory->category_name }}</td>
                                <td>{{ $filterCategory->cantidad }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif

            @if ((!empty($service_id) && !empty($centre_id)) || (!empty($service_id) && empty($centre_id)))
                <div class="card mt-4">
                    <div class="chart-container">
                        <h4>Ventas de <strong>{{ $selectedService->name }}</strong> por <strong>EMPLEADO</strong></h4>
                        <div>
                            <canvas id="chartServiceEmployee"></canvas>
                        </div>
                    </div>
                </div>
                <h4>Ventas por empleado de <strong>{{ $selectedService->name }}</strong> en
                    <strong>{{ $selectedCentre->name ?? 'TODOS LOS CENTROS' }}</strong></h4>
                <table class="mt-4 table">
                    <thead>
                        <tr class="row-service" style="background-color: var(--red-icot);color:white;">
                            <th>Empleado</th>
                            <th>Categoría</th>
                            <th>Centro</th>
                            <th>Realizados</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($servicesCount as $desgloseService)
                            @if ($desgloseService->employee_rol_id != 8)
                                <tr>
                                    <td>{{ $desgloseService->employee_name }}</td>
                                    <td>{{ $desgloseService->category_name }}</td>
                                    <td>{{ $desgloseService->centre_name }}</td>
                                    <td>{{ $desgloseService->cantidad }}</td>

                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    @endsection
</body>

</html>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        const labelsCentre = JSON.parse('@json($labelsCentre)');
        const dataCentre = JSON.parse('@json($dataCentre)');
        const labelsService = JSON.parse('@json($labelsService)');
        const dataService = JSON.parse('@json($dataService)');
        const labelsServiceAll = JSON.parse('@json($labelsServiceAll)');
        const dataServiceAll = JSON.parse('@json($dataServiceAll)');
        const labelsCentreService = JSON.parse('@json($labelsCentreService)');
        const dataCentreService = JSON.parse('@json($dataCentreService)');
        const dataTotalService = JSON.parse('@json($dataTotalService)');
        const labelsServiceCategory = JSON.parse('@json($labelsServiceCategory)');
        const dataServiceCategory = JSON.parse('@json($dataServiceCategory)');
        const labelsServiceEmployee = JSON.parse('@json($labelsServiceEmployee)');
        const dataServiceEmployee = JSON.parse('@json($dataServiceEmployee)');
        const labelsServiceAllTotal = JSON.parse('@json($labelsServiceAllTotal)');
        const dataServiceAllTotal = JSON.parse('@json($dataServiceAllTotal)');

        try {
            var ctxCentre = document.getElementById('chartCentre').getContext('2d');
            var chartCentre = new Chart(ctxCentre, {
                type: 'bar',
                data: {
                    labels: labelsCentre,
                    datasets: [{
                        label: 'Cantidad de Servicios',
                        data: dataCentre,
                        backgroundColor: ['rgba(255, 99, 132, 0.2)', 'rgba(54, 162, 235, 0.2)',
                            'rgba(255, 206, 86, 0.2)'
                        ],
                        borderColor: ['rgba(255, 99, 132, 1)', 'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        } catch (error) {
            console.error('Error creating chartCentre:', error);
        }

        try {
            var ctxService = document.getElementById('chartService').getContext('2d');
            var chartService = new Chart(ctxService, {
                type: 'bar',
                data: {
                    labels: labelsService,
                    datasets: [{
                        label: 'Cantidad de Servicios',
                        data: dataService,
                        backgroundColor: ['rgba(255, 99, 132, 0.2)', 'rgba(54, 162, 235, 0.2)',
                            'rgba(255, 206, 86, 0.2)'
                        ],
                        borderColor: ['rgba(255, 99, 132, 1)', 'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        } catch (error) {
            console.error('Error creating chartService:', error);
        }

        try {
            var ctxServiceAllTotal = document.getElementById('chartServiceAllTotal').getContext('2d');
            var chartServiceAllTotal = new Chart(ctxServiceAllTotal, {
                type: 'bar',
                data: {
                    labels: labelsServiceAllTotal,
                    datasets: [{
                        label: 'Cantidad de Servicios',
                        data: dataServiceAllTotal,
                        backgroundColor: ['rgba(255, 99, 132, 0.2)', 'rgba(54, 162, 235, 0.2)',
                            'rgba(255, 206, 86, 0.2)'
                        ],
                        borderColor: ['rgba(255, 99, 132, 1)', 'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        } catch (error) {
            console.error('Error creating chartServiceAllTotal:', error);
        }

        try {
            var ctxServiceAll = document.getElementById('chartServiceAll').getContext('2d');
            var chartServiceAll = new Chart(ctxServiceAll, {
                type: 'bar',
                data: {
                    labels: labelsServiceAll,
                    datasets: [{
                        label: 'Cantidad de Servicios',
                        data: dataServiceAll,
                        backgroundColor: ['rgba(255, 99, 132, 0.2)', 'rgba(54, 162, 235, 0.2)',
                            'rgba(255, 206, 86, 0.2)'
                        ],
                        borderColor: ['rgba(255, 99, 132, 1)', 'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        } catch (error) {
            console.error('Error creating chartServiceAll:', error);
        }

        try {
            var ctxCentreService = document.getElementById('chartCentreService').getContext('2d');
            var chartCentreService = new Chart(ctxCentreService, {
                type: 'bar',
                data: {
                    labels: labelsCentreService,
                    datasets: [{
                        label: 'Cantidad de Servicios por Centro',
                        data: dataCentreService,
                        backgroundColor: ['rgba(255, 99, 132, 0.2)'],
                        borderColor: ['rgba(255, 99, 132, 1)'],
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        } catch (error) {
            console.error('Error creating chartCentreService:', error);
        }

        try {
            var ctxServiceCategory = document.getElementById('chartServiceCategory').getContext('2d');
            var chartServiceCategory = new Chart(ctxServiceCategory, {
                type: 'bar',
                data: {
                    labels: labelsServiceCategory,
                    datasets: [{
                        label: 'Cantidad de Servicios',
                        data: dataServiceCategory,
                        backgroundColor: ['rgba(255, 99, 132, 0.2)', 'rgba(54, 162, 235, 0.2)',
                            'rgba(255, 206, 86, 0.2)'
                        ],
                        borderColor: ['rgba(255, 99, 132, 1)', 'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        } catch (error) {
            console.error('Error creating chartServiceCategory:', error);
        }

        try {
            var ctxServiceEmployee = document.getElementById('chartServiceEmployee').getContext('2d');
            var chartServiceEmployee = new Chart(ctxServiceEmployee, {
                type: 'bar',
                data: {
                    labels: labelsServiceEmployee,
                    datasets: [{
                        label: 'Cantidad de Servicios',
                        data: dataServiceEmployee,
                        backgroundColor: ['rgba(255, 99, 132, 0.2)', 'rgba(54, 162, 235, 0.2)',
                            'rgba(255, 206, 86, 0.2)'
                        ],
                        borderColor: ['rgba(255, 99, 132, 1)', 'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        } catch (error) {
            console.error('Error creating chartServiceEmployee:', error);
        }
    });

    function resetSelectors() {
        window.location.href = window.location.origin + window.location.pathname;
    }

    document.addEventListener('DOMContentLoaded', function() {
        var btnSubmit = document.getElementById('btnSubmit');
        if (btnSubmit) {
            btnSubmit.addEventListener('click', function() {
                console.log('El botón de exportar fue clickeado.');
                console.log('Fecha de inicio:', document.getElementById('start_date').value);
                console.log('Fecha de fin:', document.getElementById('end_date').value);
            });
        } else {
            console.error('No se encontró el botón de exportar');
        }
    });
</script>
<style>
    body {
        background-image: url(/assets/img/background_continue.png) !important;
        background-position: center center !important;
        background-size: 1000px;
        
    }


    .table tr:nth-child(odd) {
        background-color: #d8d5d5ec;
    }

    .table tr:nth-child(even) {
        background-color: #f7f3f3ec;
    }

   



    .row-service {
        background-color: var(--red-icot) !important;
        color: white;
    }

    th {
        font-weight: bold !important;
    }

    .chart-container {
        background-color: white;
        border-radius: 5px;
        padding: 2%;
    }
</style>
