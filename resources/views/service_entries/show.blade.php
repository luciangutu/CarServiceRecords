@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Detalii intrare service</h1>
        <div>
            <a href="{{ route('service-entries.edit', $serviceEntry) }}" class="btn btn-primary">Editează</a>
            <a href="{{ route('service-entries.index') }}" class="btn btn-secondary">Înapoi la listă</a>
        </div>
    </div>
    
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5 class="card-title">Informații generale</h5>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <strong>Data:</strong> {{ $serviceEntry->date->format('d.m.Y') }}
                        </li>
                        <li class="list-group-item">
                            <strong>Kilometri:</strong> {{ number_format($serviceEntry->kilometers, 0, ',', '.') }} km
                        </li>
                        <li class="list-group-item">
                            <strong>Numar inmatriculare:</strong> {{ $serviceEntry->car->license_plate ?? 'N/A' }}
                        </li>
                        <li class="list-group-item">
                            <strong>Service:</strong> {{ $serviceEntry->service_name }}
                        </li>
                        <li class="list-group-item">
                            <strong>Cost:</strong> 
                            {{ $serviceEntry->cost ? number_format($serviceEntry->cost, 2, ',', '.') . ' RON' : '-' }}
                        </li>
                    </ul>
                </div>
                
                <div class="col-md-6">
                    <h5 class="card-title">Detalii service</h5>
                    <div class="mb-3">
                        <strong>Acțiune service:</strong>
                        <p class="mt-2">{{ $serviceEntry->service_action }}</p>
                    </div>
                    
                    @if($serviceEntry->parts_replaced)
                    <div class="mb-3">
                        <strong>Piese înlocuite:</strong>
                        <p class="mt-2">{{ $serviceEntry->parts_replaced }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="card-footer text-muted">
            <small>
                Creat la: {{ $serviceEntry->created_at->format('d.m.Y H:i') }} | 
                Actualizat la: {{ $serviceEntry->updated_at->format('d.m.Y H:i') }}
            </small>
        </div>
    </div>
    
    <div class="mt-4">
        <form method="POST" action="{{ route('service-entries.destroy', $serviceEntry) }}" 
              onsubmit="return confirm('Sigur doriți să ștergeți această intrare?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">Șterge intrarea</button>
        </form>
    </div>
</div>
@endsection