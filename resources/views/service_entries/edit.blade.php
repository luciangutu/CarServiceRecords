@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Editează intrarea service</h1>
    
    <form method="POST" action="{{ route('service-entries.update', $serviceEntry) }}">
        @csrf
        @method('PUT')
        
        <div class="form-group mb-3">
            <label for="date">Data</label>
            <input type="date" name="date" id="date" class="form-control" 
                   value="{{ old('date', $serviceEntry->date->format('Y-m-d')) }}" required>
            @error('date')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="form-group mb-3">
            <label for="kilometers">Kilometri</label>
            <input type="number" name="kilometers" id="kilometers" class="form-control" 
                   value="{{ old('kilometers', $serviceEntry->kilometers) }}" required>
            @error('kilometers')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="form-group mb-3">
            <label for="car_id">Masina (numar inmatriculare)</label>
            <select name="car_id" id="car_id" class="form-control" required>
                <option value="">-- Selecteaza masina --</option>
                @foreach($cars as $car)
                    <option value="{{ $car->id }}" {{ old('car_id', $serviceEntry->car_id) == $car->id ? 'selected' : '' }}>
                        {{ $car->license_plate }} - {{ $car->make }} {{ $car->model }}
                    </option>
                @endforeach
            </select>
            @error('car_id')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="form-group mb-3">
            <label for="service_name">Nume service</label>
            <input type="text" name="service_name" id="service_name" class="form-control" 
                   value="{{ old('service_name', $serviceEntry->service_name) }}" required>
            @error('service_name')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="form-group mb-3">
            <label for="service_action">Acțiune service (ce s-a făcut)</label>
            <textarea name="service_action" id="service_action" class="form-control" rows="3" required>{{ old('service_action', $serviceEntry->service_action) }}</textarea>
            @error('service_action')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="form-group mb-3">
            <label for="parts_replaced">Piese înlocuite (opțional)</label>
            <textarea name="parts_replaced" id="parts_replaced" class="form-control" rows="2">{{ old('parts_replaced', $serviceEntry->parts_replaced) }}</textarea>
            @error('parts_replaced')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="form-group mb-3">
            <label for="cost">Cost (RON) (opțional)</label>
            <input type="number" step="0.01" name="cost" id="cost" class="form-control" 
                   value="{{ old('cost', $serviceEntry->cost) }}">
            @error('cost')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="d-flex justify-content-between">
            <button type="submit" class="btn btn-primary">Actualizează</button>
            <a href="{{ route('service-entries.index') }}" class="btn btn-secondary">Anulează</a>
        </div>
    </form>
</div>
@endsection