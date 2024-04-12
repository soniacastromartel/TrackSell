@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Servicios por Centro</h1>
    <form action="{{ route('services.filter') }}" method="GET">
        <div class="form-group">
            <label for="centre_id">Selecciona un Centro:</label>
            <select class="form-control" id="centre_id" name="centre_id" onchange="this.form.submit()">

                @foreach ($centres as $centre)
                <option value="{{ $centre->id }}" {{ $centre->selected ? 'selected' : '' }}>
                    {{ $centre->name }}
                </option>
                @endforeach

            </select>

        </div>
    </form>
    @if (isset($services))
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
            @foreach ($services as $service)
            <tr>
                <td>{{ $service->name }}</td>
                <td>
                    @if (!empty($service->servicePrice))
                        @foreach ($service->servicePrice as $price)
                            @if ($loop->first)
                                {{ $price->price }}€
                            @endif
                        @endforeach
                    @else
                        No disponible
                    @endif
                </td>
                <td>
                    @php
                        $count = $serviceCount->firstWhere('service_id', $service->id);
                        $calculatedTotal = $price->price * ($count ? $count->total : 0);
                    @endphp
                    {{ $count ? $count->total : '0' }}
                </td>

                <td>
                    {{ $calculatedTotal }}€
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</div>
@endsection

