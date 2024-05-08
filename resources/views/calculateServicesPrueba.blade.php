<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>PDI</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/logged.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <div class="container-calculate" style="margin:20px;">
        <div class="card"
            style="display: flex; flex-direction:row; justify-content:space-evenly; margin:50px; padding:50px;">

            <div class="row-centre-service">
                <div class="centre-container">
                    <h1>Centro</h1>
                    <form action="{{ route('calculateServicesPrueba') }}" method="GET">
                        <input type="hidden" name="service_id" value="{{ $service_id }}">
                        <div class="form-group">
                            <select class="form-control" id="centre_id" name="centre_id" onchange="this.form.submit()">
                                <option value="">SELECCIONA CENTRO</option>
                                @foreach ($centres as $centre)
                                    <option value="{{ $centre->id }}"
                                        {{ $centre_id == $centre->id ? 'selected' : '' }}>
                                        {{ $centre->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                    <div class="service-container">
                        <h1>Servicios</h1>
                        <form id="centreForm" action="{{ route('calculateServicesPrueba') }}" method="GET">
                            <input type="hidden" name="centre_id" value="{{ $centre_id }}">
                            <div class="form-group">
                                <select class="form-control" id="service_id" name="service_id"
                                    onchange="this.form.submit()">
                                    <option value="">TODOS</option>
                                    @foreach ($services as $service)
                                        <option value="{{ $service->id }}"
                                            {{ $service_id == $service->id ? 'selected' : '' }}>
                                            {{ $service->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </form>
                    </div>
                </div>
                <button type="button" class="btn btn-primary" onclick="resetSelectors()">Refrescar datos</button>
               <form action="{{ route('export.services')}}" method="GET">
                <input type="hidden" name="service_id" value="{{ $service_id ?? '' }}">
                <input type="hidden" name="centre_id" value="{{ $centre_id ?? '' }}">
                <input type="hidden" name="start_date" value="{{ $start_date ?? '' }}">
                <input type="hidden" name="end_date" value="{{ $end_date ?? '' }}">
                <button type="submit" class="btn btn-primary">Exportar Documento</button>
               </form>

            </div>
            <form id="serviceForm" action="{{ route('calculateServicesPrueba') }}" method="GET">
                <input type="hidden" name="centre_id" value="{{ $centre_id }}">
                <input type="hidden" name="service_id" value="{{ $service_id }}">
                <div class="form-group">
                    <label for="start_date">Fecha Inicio:</label>
                    <input type="date" class="form-control" id="start_date" name="start_date"
                        value="{{ request('start_date') }}">
                </div>
                <div class="form-group">
                    <label for="end_date">Fecha Fin:</label>
                    <input type="date" class="form-control" id="end_date" name="end_date"
                        value="{{ request('end_date') }}">
                </div>
                <button type="submit" class="btn btn-primary">Filtrar</button>
            </form>
        </div>

        @if (empty($service_id) && empty($centre_id))
            <div class="container-calculate"style="display:flex; flex-direction:column; justify-content:center; align-items:center">
                <div>Todos los Servicios</div>
                <div>
                    <canvas id="chartServiceAll" width="1800" height="500"></canvas>
                </div>
            </div>
            <table class="table">
                <thead>
                    @if (request('start_date') && request('end_date'))
                    <tr class="row-service" style="background-color: var(--red-icot);color:white;">
                        <th colspan="5">Fecha : {{ request('start_date') }}  /  {{ request('end_date') }}</th>
                    </tr>   
                    @endif
                    <tr class="row-service" style="background-color: var(--red-icot);color:white;">
                        <th>Servicio</th>
                        <th>Total Realizados</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($servicesCount as $service)
                        <tr>
                            <td>{{ $service->service_name }}</td>
                            <td>{{ $service->cantidad }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
        @if ($centre_id)
            <div class="container-calculate" style="display:flex; flex-direction:column; justify-content:center; align-items:center">
                <div>Servicios en {{ $selectedCentre->name }}</div>

                <div>
                    <canvas id="chartCentre" width="1800" height="500"></canvas>
                </div>
            </div>
            <table class="table">
                <thead>
                    @if (request('start_date') && request('end_date'))
                    <tr class="row-service" style="background-color: var(--red-icot);color:white;">
                        <th colspan="5">Fecha : {{ request('start_date') }}  /  {{ request('end_date') }}</th>
                    </tr>   
                    @endif
                    <tr class="row-service" style="background-color: var(--red-icot);color:white;">
                        <th>Centro</th>
                        <th>Servicio</th>
                        <th>Precio</th>
                        <th>Realizados</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($servicesCountCentre as $service)
                        <tr>
                            <td>{{ $service->centre_name }}</td>
                            <td>{{ $service->service_name }}</td>
                            <td>{{ $service->price }}€</td>
                            <td>{{ $service->total }}</td>
                            <td>{{ $service->price * $service->total }}€</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
        @if ($service_id)
            <div class="container-calculate"
                style="display:flex; flex-direction:column; justify-content:center; align-items:center">
                <div>Servicio de {{ $selectedService->name }}</div>
                <div>
                    <canvas id="chartService" width="1800" height="500"></canvas>
                </div>
            </div>
            <table class="table">
                <thead>
                    @if (request('start_date') && request('end_date'))
                    <tr class="row-service" style="background-color: var(--red-icot);color:white;">
                        <th colspan="5">Fecha : {{ request('start_date') }}  /  {{ request('end_date') }}</th>
                    </tr>   
                    @endif
                    <tr class="row-service" style="background-color: var(--red-icot);color:white;">
                        <th>Servicio</th>
                        <th>Realizados</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @if ($servicesCount->isNotEmpty())
                        <tr class="total-services-row" style="background-color:rgb(212, 209, 209)">
                            <td>{{ $servicesCount->first()->service_name }}</td>
                            <td>{{ $totalServices }}</td>
                            <td>{{ $grandTotal }}€</td>
                        </tr>
                    @endif
                </tbody>
            </table>
            <h2>Desglose Servicio</h2>
            <table class="table">
                <thead>
                    <tr class="row-service" style="background-color: var(--red-icot);color:white;">
                        <th>Servicio</th>
                        <th>Realizados</th>
                        <th>Centro</th>
                        <th>Empleado</th>
                        <th>Categoría</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($servicesCount as $desgloseService)
                        <tr>
                            <td>{{ $desgloseService->service_name }}</td>
                            <td>{{ $desgloseService->cantidad }}</td>
                            <td>{{ $desgloseService->centre_name }}</td>
                            <td>{{ $desgloseService->employee_name }}</td>
                            <td>{{ $desgloseService->employee_category }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const labelsCentre = JSON.parse('@json($labelsCentre)');
            const dataCentre = JSON.parse('@json($dataCentre)');
            const labelsService = JSON.parse('@json($labelsService)');
            const dataService = JSON.parse('@json($dataService)');
            const labelsServiceAll = JSON.parse('@json($labelsServiceAll)');
            const dataServiceAll = JSON.parse('@json($dataServiceAll)');


            try {
                var ctxCentre = document.getElementById('chartCentre').getContext('2d');
                var chartCentre = new Chart(ctxCentre, {
                    type: 'bar',
                    data: {
                        labels: labelsCentre,
                        datasets: [{
                            label: 'Cantidad de Servicios',
                            data: dataCentre,
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

            //chartServiceAll

            try {
                var ctxServiceAll = document.getElementById('chartServiceAll').getContext('2d');
                var chartServiceAll = new Chart(ctxServiceAll, {
                    type: 'bar',
                    data: {
                        labels: labelsServiceAll,
                        datasets: [{
                            label: 'Cantidad de Servicios',
                            data: dataServiceAll,
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
        });

        function resetSelectors() {
            window.location.href = window.location.origin + window.location.pathname;
        }
    </script>

</body>

</html>
