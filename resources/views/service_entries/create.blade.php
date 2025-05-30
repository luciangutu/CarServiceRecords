@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Adaugă intrare service pentru mașina: {{ $car->make }} {{ $car->model }} ({{ $car->license_plate }})</h1>
    
    <form method="POST" action="{{ route('cars.service_entries.store', $car) }}">
        @csrf
        
        <div class="form-group">
            <label for="date">Data</label>
            <input type="date" name="date" id="date" class="form-control" required value="{{ old('date') }}">
        </div>
        
        <div class="form-group">
            <label for="kilometers">Kilometri</label>
            <input type="number" name="kilometers" id="kilometers" class="form-control" required value="{{ old('kilometers') }}">
        </div>
        
        {{-- car_id is now from the route ($car->id), no need for a selection field here in the create form --}}
        
        <div class="form-group">
            <label for="service_name">Nume service</label>
            <input type="text" name="service_name" id="service_name" class="form-control" required value="{{ old('service_name') }}">
        </div>
        
        <div class="form-group">
            <label for="service_action">Acțiune service (ce s-a făcut)</label>
            <textarea name="service_action" id="service_action" class="form-control" rows="3" required>{{ old('service_action') }}</textarea>
        </div>
        
        <div class="form-group">
            <label for="parts_replaced">Piese înlocuite (opțional)</label>
            <textarea name="parts_replaced" id="parts_replaced" class="form-control" rows="2">{{ old('parts_replaced') }}</textarea>
        </div>
        
        <div class="form-group">
            <label for="cost">Cost (RON) (opțional)</label>
            <input type="number" step="0.01" name="cost" id="cost" class="form-control" value="{{ old('cost') }}">
        </div>
        
        <button type="submit" class="btn btn-primary">Salvează</button>
        <a href="{{ route('cars.service_entries.index', $car) }}" class="btn btn-secondary">Anulează</a>
    </form>
</div>
@endsection
