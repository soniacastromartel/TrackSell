@extends('layouts.app')
@section('content')


   <body>

    <div class="container-calculate" style="margin:20px;">
        <div class="card"
            style="display: flex; flex-direction:row; justify-content:space-evenly; margin:100px; padding:50px;">

            <div class="row-centre-service">
                <div class="centre-container">
                    <h1>Centro</h1>
                    <form action="{{ route('calculateServicesPrueba') }}" method="GET">
                        <input type="hidden" name="service_id" value="{{ $service_id }}">
                        <div class="form-group">
                            <select class="form-control" id="centre_id" name="centre_id" onchange="this.form.submit()">
                                <option value="">SELECCIONA CENTRO</option>
                                @foreach ($centres as $centre)
                                    <option value="{{ $centre->id }}" {{ $centre_id == $centre->id ? 'selected' : '' }}>
                                        {{ $centre->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </form>
               

                <div class="service-container">
                    <h1>Servicios</h1>
                    <form action="{{ route('calculateServicesPrueba') }}" method="GET">
                        <input type="hidden" name="centre_id" value="{{ $centre_id }}">
                        <div class="form-group">
                            <select class="form-control" id="service_id" name="service_id" onchange="this.form.submit()">
                                <option value="">TODOS</option>
                                @foreach ($services as $service)
                                    <option value="{{ $service->id }}" {{ $service_id == $service->id ? 'selected' : '' }}>
                                        {{ $service->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>
            </div>
                <button type="button" class="btn btn-primary" onclick="resetSelectors()">Refrescar datos</button>

            </div>

            <form action="{{ route('calculateServicesPrueba') }}" method="GET">
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

    
    
        @if ($centre_id)

        <h1>Centre Services Chart</h1>
        <div>{!! $chart->container() !!}</div>

            <table class="table">
                <thead>
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
            <table class="table">
                <thead>
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
                        <th>Inicio</th>
                        <th>Final</th>
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
                            <td>{{ $desgloseService->started_date}}</td>
                            <td>{{ $desgloseService->end_date}}</td>
                            <td>{{ $desgloseService->employee_name }}</td>
                            <td>{{ $desgloseService->employee_category }}</td>

                        </tr>
                    @endforeach

                </tbody>
            </table>
        @endif



</div>
</body>
@endsection
@section('scripts')
{{-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
{!! $chart->script() !!}

<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('myChart').getContext('2d');
    const myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Red', 'Blue', 'Yellow', 'Green', 'Purple', 'Orange'],
            datasets: [{
                label: '# of Votes',
                data: [12, 19, 3, 5, 2, 3],
                backgroundColor: [
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(255, 206, 86, 0.2)',
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(153, 102, 255, 0.2)'
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)'
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
});

</script>
 @endsection