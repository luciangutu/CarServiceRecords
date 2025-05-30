@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Registru Service pentru mașina: {{ $car->make }} {{ $car->model }} ({{ $car->license_plate }})</h1>
    
    <div class="mb-4">
        <a href="{{ route('cars.service_entries.create', $car) }}" class="btn btn-primary">Adaugă intrare nouă pentru această mașină</a>
        <a href="{{ route('cars.index') }}" class="btn btn-secondary">Vezi toate mașinile</a>
    </div>
    
    <div class="card mb-4">
        <div class="card-header">Filtre pentru intrările acestei mașini</div>
        <div class="card-body">
            <form method="GET" action="{{ route('cars.service_entries.index', $car) }}">
                <div class="row">
                    <div class="col-md-4"> {{-- Adjusted column size --}}
                        <div class="form-group">
                            <label for="search">Caută (acțiune/piesă)</label>
                            <input type="text" name="search" id="search" class="form-control" value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="date_from">De la data</label>
                            <input type="date" name="date_from" id="date_from" class="form-control" value="{{ request('date_from') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="date_to">Până la data</label>
                            <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request('date_to') }}">
                        </div>
                    </div>
                    <div class="col-md-3"> {{-- Adjusted column size --}}
                        <div class="form-group">
                            <label for="service_name">Service</label>
                            <select name="service_name" id="service_name" class="form-control">
                                <option value="">Toate</option>
                                @foreach($serviceNames as $name)
                                    <option value="{{ $name }}" {{ request('service_name') == $name ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    {{-- Removed car_id filter as this view is now scoped to a single car --}}
                    <div class="col-md-2 d-flex flex-column align-items-center gap-2 mt-4"> {{-- Adjusted column size and margin --}}
                        <button type="submit" class="btn btn-primary w-100">Filtrează</button>
                        <a href="{{ route('cars.service_entries.index', $car) }}" class="btn btn-outline-secondary w-100">Resetează</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Km</th>
                    <th>Nr. înmatriculare</th>
                    <th>Service</th>
                    <th>Acțiune</th>
                    <th>Piese înlocuite</th>
                    <th>Cost</th>
                    <th>Acțiuni</th>
                </tr>
            </thead>
            <tbody>
                @foreach($entries as $entry)
                <tr>
                    <td>{{ $entry->date->format('d.m.Y') }}</td>
                    <td>{{ number_format($entry->kilometers, 0, ',', '.') }}</td>
                    <td>{{ $entry->car->license_plate ?? '' }}</td>
                    <td>{{ $entry->service_name }}</td>
                    <td>{{ Str::limit($entry->service_action, 50) }}</td>
                    <td>{{ Str::limit($entry->parts_replaced, 50) }}</td>
                    <td>{{ $entry->cost ? number_format($entry->cost, 2, ',', '.') . ' RON' : '-' }}</td>
                    <td>
                        <a href="{{ route('service_entries.show', $entry) }}" class="btn btn-sm btn-info">Vizualizează</a>
                        <a href="{{ route('service_entries.edit', $entry) }}" class="btn btn-sm btn-primary">Editează</a>
                        <form action="{{ route('service_entries.destroy', $entry) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Șterge</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    {{ $entries->links() }}
</div>
@endsection