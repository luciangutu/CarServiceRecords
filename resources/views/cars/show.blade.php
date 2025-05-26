@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Detalii masina</h1>
    <table class="table">
        <tr><th>Marca</th><td>{{ $car->make }}</td></tr>
        <tr><th>Model</th><td>{{ $car->model }}</td></tr>
        <tr><th>Numar inmatriculare</th><td>{{ $car->license_plate }}</td></tr>
        <tr><th>VIN</th><td>{{ $car->vin ?? '-' }}</td></tr>
    </table>
    <a href="{{ route('cars.edit', $car) }}" class="btn btn-warning">Editeaza</a>
    <a href="{{ route('cars.index') }}" class="btn btn-secondary">Inapoi</a>
</div>
@endsection
