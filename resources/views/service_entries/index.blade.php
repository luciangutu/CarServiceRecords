@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Registru Service Auto</h1>
    
    <div class="mb-4">
        <a href="{{ route('service-entries.create') }}" class="btn btn-primary">Adaugă intrare</a>
        <a href="{{ route('cars.index') }}" class="btn btn-primary">Listează mașinile</a>
    </div>
    
    <div class="card mb-4">
        <div class="card-header">Filtre</div>
        <div class="card-body">
            <form method="GET" action="{{ route('service-entries.index') }}">
                <div class="row">
                    <div class="col-md-3">
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
                    <div class="col-md-2">
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
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="car_id">Masina</label>
                            <select name="car_id" id="car_id" class="form-control">
                                <option value="">Toate</option>
                                @foreach($cars as $car)
                                    <option value="{{ $car->id }}" {{ request('car_id') == $car->id ? 'selected' : '' }}>
                                        {{ $car->license_plate }} - {{ $car->make }} {{ $car->model }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-1 d-flex flex-column align-items-center gap-2">
                        <button type="submit" class="btn btn-primary">Filtrează</button>
                        <a href="{{ route('service-entries.index') }}" class="btn btn-outline-secondary">Resetează</a>
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
                        <a href="{{ route('service-entries.show', $entry) }}" class="btn btn-sm btn-info">Vizualizează</a>
                        <a href="{{ route('service-entries.edit', $entry) }}" class="btn btn-sm btn-primary">Editează</a>
                        <form action="{{ route('service-entries.destroy', $entry) }}" method="POST" class="d-inline">
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