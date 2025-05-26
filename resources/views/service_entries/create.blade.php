@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Adaugă intrare service</h1>
    
    <form method="POST" action="{{ route('service-entries.store') }}">
        @csrf
        
        <div class="form-group">
            <label for="date">Data</label>
            <input type="date" name="date" id="date" class="form-control" required>
        </div>
        
        <div class="form-group">
            <label for="kilometers">Kilometri</label>
            <input type="number" name="kilometers" id="kilometers" class="form-control" required>
        </div>
        
        <div class="form-group">
            <label for="license_plate">Număr înmatriculare</label>
            <input list="license_plates" name="license_plate" id="license_plate" class="form-control" required
                value="{{ old('license_plate') }}" autocomplete="off">
            <datalist id="license_plates">
                @foreach($existingPlates as $plate)
                    <option value="{{ $plate }}">
                @endforeach
            </datalist>
        </div>
        
        <div class="form-group">
            <label for="service_name">Nume service</label>
            <input type="text" name="service_name" id="service_name" class="form-control" required>
        </div>
        
        <div class="form-group">
            <label for="service_action">Acțiune service (ce s-a făcut)</label>
            <textarea name="service_action" id="service_action" class="form-control" rows="3" required></textarea>
        </div>
        
        <div class="form-group">
            <label for="parts_replaced">Piese înlocuite (opțional)</label>
            <textarea name="parts_replaced" id="parts_replaced" class="form-control" rows="2"></textarea>
        </div>
        
        <div class="form-group">
            <label for="cost">Cost (RON) (opțional)</label>
            <input type="number" step="0.01" name="cost" id="cost" class="form-control">
        </div>
        
        <button type="submit" class="btn btn-primary">Salvează</button>
        <a href="{{ route('service-entries.index') }}" class="btn btn-secondary">Anulează</a>
    </form>
</div>
@endsection