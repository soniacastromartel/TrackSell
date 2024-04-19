@extends('layouts.app')

@section('content')


    <div class="container">
        <h1>Servicios por Centro</h1>
        <form action="{{ route('calculateServicesPrueba') }}" method="GET">
            <div class="form-group">
                <label for="centre_id">Selecciona un Centro:</label>
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

        <form action="{{ route('calculateServicesPrueba') }}" method="GET">
            <input type="hidden" name="centre_id" value="{{ $centre_id }}">
            <div class="form-group">
                <label for="service_id">Selecciona un Servicio:</label>
          
                <select class="form-control" id="service_id" name="service_id" onchange="this.form.submit()">
                    <option value="">TODOS</option>
                    @foreach ($services as $service)
                        <option value="{{ $service->id }}" {{ isset($selectedService) && $selectedService->id == $service->id ? 'selected' : '' }}>
                            {{ $service->name }}
                        </option>
                    @endforeach
                </select>
       
            
            </div>
        </form> 

        <table class="table">
            <thead>
                <tr>
                    <th>Servicio</th>
                    <th>Precio</th>
                    <th>Cantidad</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                {{-- @if (isset($selectedService))
                @php
                    $prices = $selectedService->servicePrice->where('cancellation_date', null)->pluck('price')->unique();
                    $totalServices = $serviceCount->where('service_id', $selectedService->id)->first()->total ?? 0;
                    $priceDisplays = [];
            
                    foreach ($prices as $price) {
                        $totalPrice = $price * $totalServices;
                        $priceDisplays[] = $price . '€: ' . $totalPrice . '€';
                    }
            
                    $priceDisplay = implode(' / ', $priceDisplays);
                    $totalSum = array_sum(
                        array_map(function ($price) {
                            // Extracción de la parte numérica después del ': ' y eliminación del '€'
                            $parts = explode(':', $price);
                            return floatval(trim(str_replace('€', '', $parts[1])));
                        }, $priceDisplays),
                    );
                @endphp
                <tr>
                    <td>{{ $selectedService->name }}</td>
                    <td>{{ $priceDisplay }}</td>
                    <td>{{ $totalServices }}</td>
                    <td>{{ $totalSum }}€</td>
                </tr>
                @else --}}
                @foreach ($servicesCountCentre as $service)
                <tr>
                    <td>{{ $service->service_name }}</td>
                    <td>{{ $service->price }}€</td>
                    <td>{{ $service->total }}</td>
                    <td>{{ $service->price * $service->total }}€</td>
                </tr>
            @endforeach
                {{-- @endif --}}
            </tbody>
        </table>
    </div>
@endsection
