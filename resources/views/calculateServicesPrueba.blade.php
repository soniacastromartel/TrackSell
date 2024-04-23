@extends('layouts.app')
@section('content')

    <div class="container">
        
        {{-- <form action="{{ route('calculateServicesPrueba') }}" method="GET">
            <input type="hidden" name="centre_id" value="{{ $centre_id }}">
            <input type="hidden" name="service_id" value="{{ $service_id }}">
            
            <div class="form-group">
                <label for="start_date">Fecha Inicio:</label>
                <input type="date" class="form-control" id="start_date" name="start_date" value="{{ request('start_date') }}">
            </div>
            
            <div class="form-group">
                <label for="end_date">Fecha Fin:</label>
                <input type="date" class="form-control" id="end_date" name="end_date" value="{{ request('end_date') }}">
            </div>
            
            <button type="submit" class="btn btn-primary">Filtrar</button>
        </form> --}}

      
        <h1>Servicios por Centro</h1>
        <form action="{{ route('calculateServicesPrueba') }}" method="GET">
            <input type="hidden" name="service_id" value="{{ $service_id }}">
          
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
        <h1>Servicios</h1>
        <form action="{{ route('calculateServicesPrueba') }}" method="GET">
            <input type="hidden" name="centre_id" value="{{ $centre_id }}">
         
            <div class="form-group">
                <label for="service_id">Selecciona un Servicio:</label>
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
        @if ($centre_id)
        <table class="table">
            <thead>
                <tr>
                    <th>Servicio</th>
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
        @if ($service_id)
        <table class="table">
            <thead>
                <tr>
                    <th>Servicio</th>
                    <th>Realizados</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @if ($servicesCount->isNotEmpty())
                <tr>
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
                <tr>
                    <th>Servicio</th>
                    <th>Realizados</th>
                    <th>Centro</th>
                    <th>Empleado</th>
                    <th>Categoría</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($servicesCount as $desgloseService )
                <tr>
                    <td>{{ $desgloseService->service_name}}</td>
                    <td>{{ $desgloseService->cantidad}}</td>
                    <td>{{ $desgloseService->centre_name}}</td>
                    <td>{{ $desgloseService->employee_name}}</td>
                    <td>{{ $desgloseService->employee_category}}</td>
                 
                </tr>
                @endforeach
            
            </tbody>
        </table>
        @endif
    </div>
@endsection
