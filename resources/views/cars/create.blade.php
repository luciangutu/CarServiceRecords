@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Adauga masina</h1>
    <form method="POST" action="{{ route('cars.store') }}">
        @csrf
        <div class="form-group">
            <label for="make">Marca</label>
            <input type="text" name="make" id="make" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="model">Model</label>
            <input type="text" name="model" id="model" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="license_plate">Numar inmatriculare</label>
            <input type="text" name="license_plate" id="license_plate" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="vin">VIN (optional)</label>
            <input type="text" name="vin" id="vin" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary">Salveaza</button>
        <a href="{{ route('cars.index') }}" class="btn btn-secondary">Anuleaza</a>
    </form>
</div>
@endsection
