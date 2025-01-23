@extends('layouts.logged')
<link rel="stylesheet" href="{{ asset('css/app.css') }}">
<link rel="stylesheet" href="{{ asset('css/material.css') }}">
<link rel="stylesheet" href="{{ asset('css/logged.css') }}">

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
{{-- @extends('layouts.logged') --}}

@section('content')

<link rel="stylesheet" href="{{ asset('/css/buttons.css') }}">


    <div class="content" style="padding-top: 80px;">
        <div class="container-fluid" style="">
            <div class="card">
                <div class="card-header card-header-danger">
                    <h4 class="card-title">Dinámica de Servicios</h4>
                </div>

                <div class="card-body">

                    <div class="informes-container">
                        <div class="date-informes-container">
                            <form id="serviceForm" action="{{ route('calculateServices') }}" method="GET">


                                <input type="hidden" name="centre_id" value="{{ $centre_id }}">
                                <input type="hidden" name="service_id" value="{{ $service_id }}">

                                <label class="label align-self-center" for="dateFrom" style="padding: 10px">Fecha desde
                                </label>

                                <div id="monthYearPickerContainer" style="margin-bottom:15px;">
                                    <input type="date" class="form-date" id="start_date" name="start_date"
                                        value="{{ request('start_date') }}" onchange="this.form.submit()">
                                    <span id="icon-date" class="material-symbols-outlined"> calendar_month</span>
                                </div>
                                </input>

                                <label class="label align-self-center" for="dateTo" style="padding: 10px">Fecha hasta
                                </label>
                                <div id="monthYearPickerContainer" style="margin-bottom:15px;">
                                    <input type="date" class="form-date" id="end_date" name="end_date"
                                        value="{{ request('end_date') }}" onchange="this.form.submit()">
                                    <span id="icon-date" class="material-symbols-outlined"> calendar_month</span>
                                </div>
                                </input>

                            </form>

                        </div>

                        <div class="container ml-6" style="width: 300px">

                            <h3>Centro</h3>

                            <form action="{{ route('calculateServices') }}" method="GET">
                                <div class="select-wrapper">
                                    <span id="icon-select" class="icon-select material-symbols-outlined">
                                        business
                                    </span>
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
                                </div>
                            </form>


                            <h3>Servicios</h3>

                            <form id="centreForm" action="{{ route('calculateServices') }}" method="GET">
                                <div class="select-wrapper">
                                    <span class="icon-select material-icons">medical_services</span>
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
                                </div>
                            </form>

                        </div>

                    </div>

                    <div class="" style="display:flex;justify-content:flex-end;">
                        <button id="btnClear" class="btn-refresh" onclick="resetSelectors()">Refrescar <span
                                id="icon-refresh" class="material-icons">refresh</span></button>
                        <form action="{{ route('export.all-services') }}" method="GET">
                            @csrf
                            <input type="hidden" name="service_id" value="{{ $service_id ?? '' }}">
                            <input type="hidden" name="centre_id" value="{{ $centre_id ?? '' }}">
                            <input type="hidden" name="start_date" id="start_date"
                                value="{{ request('start_date') }}">
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
            {{-- TODOS LOS CENTROS Y SERVICIOS  --}}
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
                        <h4>Ventas de <strong>SERVICIOS</strong> en <strong>TODOS LOS CENTROS</strong> por
                            <strong>CATEGORÍA</strong> de <strong>SERVICIO</strong>
                        </h4>
                        <div>
                            <canvas id="chartServiceCategory"></canvas>
                        </div>
                    </div>
                </div>

                <table class="mt-4 table">
                    <thead>
                        <tr class="row-service">
                            <th>Categoría de Servicio</th>
                            <th>Realizados</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($serviceCategory as $service)
                            <tr>
                                <td>{{ $service->category_service }}</td>
                                <td>{{ $service->cantidad }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="card mt-4">
                    <div class="chart-container">
                        <h4>Ventas de <strong>SERVICIOS</strong> en <strong>TODOS LOS CENTROS</strong> por
                            <strong>CATEGORÍA</strong> de <strong>EMPLEADO</strong>
                        </h4>
                        <div>
                            <canvas id="chartEmployeeCategory"></canvas>
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

                <div class="card mt-4">
                    <div class="chart-container">
                        <h4>Ventas de <strong>SERVICIOS</strong> en <strong>TODOS LOS CENTROS</strong> por
                            <strong>EMPLEADO</strong>
                        </h4>
                        <div>
                            <canvas id="chartTotalEmployee"></canvas>
                        </div>
                    </div>
                </div>

                <table class="mt-4 table">
                    <thead>
                        <tr class="row-service">
                            <th>Empleado</th>
                            <th>Categoría</th>
                            <th>Realizados</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($serviceEmployee as $service)
                            <tr>
                                <td>{{ $service->employee_name }}</td>
                                <td>{{ $service->category_name }}</td>
                                <td>{{ $service->cantidad }}</td>
                                <td>{{ $service->cantidad * $service->price }} €</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
            {{-- SELECCIÓN DE CENTRO Y SERVICIO  --}}
            @if (!empty($service_id) && !empty($centre_id))
                <div class="card mt-4">
                    <div class="chart-container">
                        <h4>Ventas de <strong>{{ $selectedService->name }}</strong> en
                            <strong>{{ $selectedCentre->name }}</strong>
                        </h4>
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

                <div class="card mt-4">
                    <div class="chart-container">
                        <h4>Ventas de <strong>{{ $selectedService->name }}</strong> en
                            <strong>{{ $selectedCentre->name }}</strong>
                            por <strong>CATEGORÍA</strong> de <strong>SERVICIO</strong>
                        </h4>
                        <div>
                            <canvas id="chartServiceCategory"></canvas>
                        </div>
                    </div>
                </div>

                <table class="mt-4 table">
                    <thead>
                        <tr class="row-service">
                            <th>Categoría de Servicio</th>
                            <th>Realizados</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($serviceCategory as $service)
                            <tr>
                                <td>{{ $service->category_service }}</td>
                                <td>{{ $service->cantidad }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="card mt-4">
                    <div class="chart-container">
                        <h4>Ventas de <strong>{{ $selectedService->name }}</strong> en
                            <strong>{{ $selectedCentre->name }}</strong>
                            por <strong>CATEGORÍA</strong> de <strong>EMPLEADO</strong>
                        </h4>
                        </h4>
                        <div>
                            <canvas id="chartEmployeeCategory"></canvas>
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

                <div class="card mt-4">
                    <div class="chart-container">
                        <h4>Ventas de <strong>TODOS</strong> los Servicios en <strong>{{ $selectedCentre->name }}</strong>
                            por <strong>EMPLEADO</strong></h4>
                        <div>
                            <canvas id="chartTotalEmployee"></canvas>
                        </div>
                    </div>
                </div>
                <table class="mt-4 table">
                    <thead>
                        <tr class="row-service">
                            <th>Empleado</th>
                            <th>Categoría</th>
                            <th>Realizados</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    @foreach ($serviceEmployee as $service)
                        <tr>
                            <td>{{ $service->employee_name }}</td>
                            <td>{{ $service->category_name }}</td>
                            <td>{{ $service->cantidad }}</td>
                            <td>{{ $service->cantidad * $service->price }} €</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @endif

            {{-- SELECCIÓN DE CENTRO  --}}

            @if (empty($service_id) && !empty($centre_id))
                <div class="card mt-4">
                    <div class="chart-container">
                        <h4>Ventas de <strong>TODOS</strong> los Servicios en <strong>{{ $selectedCentre->name }}</strong>
                        </h4>
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
                            <th>Realizados</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($servicesCountCentre as $service)
                            <tr>
                                <td>{{ $service->service_name }}</td>
                                <td>{{ $service->total }}</td>
                                <td>{{ $service->price * $service->total }}€</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="card mt-4">
                    <div class="chart-container">
                        <h4>Ventas de <strong>TODOS</strong> los Servicios en <strong>{{ $selectedCentre->name }}</strong>
                            por <strong>CATEGORÍA</strong> de <strong>SERVICIO</strong></h4>
                        <div>
                            <canvas id="chartServiceCategory"></canvas>
                        </div>
                    </div>
                </div>

                <table class="mt-4 table">
                    <thead>
                        <tr class="row-service">
                            <th>Categoría de Servicio</th>
                            <th>Realizados</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($serviceCategory as $service)
                            <tr>
                                <td>{{ $service->category_service }}</td>
                                <td>{{ $service->cantidad }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="card mt-4">
                    <div class="chart-container">
                        <h4>Ventas de <strong>TODOS</strong> los Servicios en <strong>{{ $selectedCentre->name }}</strong>
                            por <strong>CATEGORÍA</strong> de <strong>EMPLEADO</strong></h4>
                        <div>
                            <canvas id="chartEmployeeCategory"></canvas>
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

                <div class="card mt-4">
                    <div class="chart-container">
                        <h4>Ventas de <strong>TODOS</strong> los Servicios en <strong>{{ $selectedCentre->name }}</strong>
                            por <strong>EMPLEADO</strong></h4>
                        <div>
                            <canvas id="chartTotalEmployee"></canvas>
                        </div>
                    </div>
                </div>
                <table class="mt-4 table">
                    <thead>
                        <tr class="row-service">
                            <th>Empleado</th>
                            <th>Categoría</th>
                            <th>Realizados</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    @foreach ($serviceEmployee as $service)
                        <tr>
                            <td>{{ $service->employee_name }}</td>
                            <td>{{ $service->category_name }}</td>
                            <td>{{ $service->cantidad }}</td>
                            <td>{{ $service->cantidad * $service->price }} €</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @endif

            {{-- SELECCION DE SERVICIO  --}}

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
                            <td>{{ $selectedService->name }}</td>
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

                {{-- CENTROS REMCOMENDADOS --}}

                <table class="mt-4 table">
                    <thead>
                        <tr class="row-service" style="background-color: var(--red-icot);color:white;">
                            <th>Recomendado</th>
                            <th>Realizado</th>
                            <th>Recomendaciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($servicesRecommend as $service)
                            <tr>
                                <td>{{ $service->centre_recommendation_name }}</td>
                                <td>{{ $service->centre_name }}</td>
                                <td>{{ $service->cantidad }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="card mt-4">
                    <div class="chart-container">
                        <h4>Ventas de <strong>{{ $selectedService->name }}</strong> en
                            <strong>{{ $selectedCentre->name ?? 'TODOS LOS CENTROS' }}</strong> por
                            <strong>CATEGORÍA</strong> de <strong>SERVICIO</strong>
                        </h4>
                        </h4>
                        <div>
                            <canvas id="chartServiceCategory"></canvas>
                        </div>
                    </div>
                </div>

                <table class="mt-4 table">
                    <thead>
                        <tr class="row-service">
                            <th>Categoría</th>
                            <th>Realizados</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($serviceCategory as $filterCategory)
                            <tr>
                                <td>{{ $filterCategory->category_service }}</td>
                                <td>{{ $filterCategory->cantidad }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="card mt-4">
                    <div class="chart-container">
                        <h4>Ventas de <strong>{{ $selectedService->name }}</strong> en
                            <strong>{{ $selectedCentre->name ?? 'TODOS LOS CENTROS' }}</strong> por
                            <strong>CATEGORÍA</strong> de <strong>EMPLEADO</strong>
                        </h4>
                        </h4>
                        <div>
                            <canvas id="chartEmployeeCategory"></canvas>
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

                <div class="card mt-4">
                    <div class="chart-container">
                        <h4>Ventas de <strong>{{ $selectedService->name }}</strong> en
                            <strong>{{ $selectedCentre->name ?? 'TODOS LOS CENTROS' }}</strong> por
                            <strong>EMPLEADO</strong>
                        </h4>
                        <div>
                            <canvas id="chartTotalEmployee"></canvas>
                        </div>
                    </div>
                </div>
                <table class="mt-4 table">
                    <thead>
                        <tr class="row-service">
                            <th>Empleado</th>
                            <th>Categoría</th>
                            <th>Realizados</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    @foreach ($serviceEmployee as $service)
                        <tr>
                            <td>{{ $service->employee_name }}</td>
                            <td>{{ $service->category_name }}</td>
                            <td>{{ $service->cantidad }}</td>
                            <td>{{ $service->cantidad * $service->price }} €</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @endif

        </div>
        <div class="row h-50 bg-transparent"></div>
    </div>
@endsection

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const chartConfigs = [{
                id: 'chartCentre',
                labels: '@json($labelsCentre)',
                data: '@json($dataCentre)',
                label: 'Cantidad de Servicios'
            },
            {
                id: 'chartService',
                labels: '@json($labelsService)',
                data: '@json($dataService)',
                label: 'Cantidad de Servicios'
            },
            {
                id: 'chartServiceAllTotal',
                labels: '@json($labelsServiceAllTotal)',
                data: '@json($dataServiceAllTotal)',
                label: 'Cantidad de Servicios'
            },
            {
                id: 'chartServiceAll',
                labels: '@json($labelsServiceAll)',
                data: '@json($dataServiceAll)',
                label: 'Cantidad de Servicios'
            },
            {
                id: 'chartCentreService',
                labels: '@json($labelsCentreService)',
                data: '@json($dataCentreService)',
                label: 'Cantidad de Servicios por Centro'
            },
            {
                id: 'chartServiceCategory',
                labels: '@json($labelsServiceCategory)',
                data: '@json($dataServiceCategory)',
                label: 'Cantidad de Servicios'
            },
            {
                id: 'chartEmployeeCategory',
                labels: '@json($labelsEmployeeCategory)',
                data: '@json($dataEmployeeCategory)',
                label: 'Cantidad de Servicios'
            },
            {
                id: 'chartTotalEmployee',
                labels: '@json($labelsTotalEmployee)',
                data: '@json($dataTotalEmployee)',
                label: 'Cantidad de Servicios'
            },
            {
                id: 'chartServiceEmployee',
                labels: '@json($labelsServiceEmployee)',
                data: '@json($dataServiceEmployee)',
                label: 'Cantidad de Servicios'
            }
        ];

        function createChart(chartConfig) {
            try {
                const ctx = document.getElementById(chartConfig.id).getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: JSON.parse(chartConfig.labels),
                        datasets: [{
                            label: chartConfig.label,
                            data: JSON.parse(chartConfig.data),
                            backgroundColor: [
                                'rgba(255, 99, 132, 0.2)',
                                'rgba(54, 162, 235, 0.2)',
                                'rgba(255, 206, 86, 0.2)'
                            ],
                            borderColor: [
                                'rgba(255, 99, 132, 1)',
                                'rgba(54, 162, 235, 1)',
                                'rgba(255, 206, 86, 1)'
                            ],
                            borderWidth: 1,
                            maxBarThickness: 50, // Grosor máximo de las barras
                            minBarLength: 2 // Longitud mínima de las barras
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
                console.error(`Error creating ${chartConfig.id}:`, error);
            }
        }

        chartConfigs.forEach(createChart);
    });


    function resetSelectors() {
        window.location.href = window.location.origin + window.location.pathname;
    }

    document.addEventListener('DOMContentLoaded', function() {
        var btnSubmit = document.getElementById('btnSubmit');
        if (btnSubmit) {
            btnSubmit.addEventListener('click', function() {
                console.log('Fecha de inicio:', document.getElementById('start_date').value);
                console.log('Fecha de fin:', document.getElementById('end_date').value);
            });
        } else {
            console.error('No se encontró el botón de exportar');
        }
    });
</script>

<style>
    #start_date,
    #end_date {
            {
            padding-left: 80px;
            background-color: var(--info) !important;
            color: #fff;
            font-size: larger;
            font-weight: bolder;
            width: 230px;
            height: 45px;
            margin: 20px;
            display: flex;
            border: none;
            border-radius: 50px;
            /* padding: 0 15px; Espaciado interno */
        }
    }

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
