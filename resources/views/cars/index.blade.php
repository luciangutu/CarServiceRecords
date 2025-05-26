@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Masini</h1>
    <a href="{{ route('cars.create') }}" class="btn btn-primary mb-3">Adauga masina noua</a>
    <a href="{{ route('service-entries.index') }}" class="btn btn-secondary mb-3">Înapoi la Registru</a>
    @if($cars->isEmpty())
        <p>Nu exista masini adaugate.</p>
    @else
    <table class="table">
        <thead>
            <tr>
                <th>Marca</th>
                <th>Model</th>
                <th>Numar inmatriculare</th>
                <th>VIN</th>
                <th>Actiuni</th>
            </tr>
        </thead>
        <tbody>
            @foreach($cars as $car)
            <tr>
                <td>{{ $car->make }}</td>
                <td>{{ $car->model }}</td>
                <td>{{ $car->license_plate }}</td>
                <td>{{ $car->vin }}</td>
                <td>
                    <a href="{{ route('cars.edit', $car) }}" class="btn btn-sm btn-warning">Editeaza</a>
                    <form method="POST" action="{{ route('cars.destroy', $car) }}" style="display:inline" onsubmit="return confirm('Stergi masina?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-danger" type="submit">Sterge</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</div>
@endsection
