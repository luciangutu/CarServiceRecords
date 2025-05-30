<?php

namespace App\Http\Controllers;

use App\Models\ServiceEntry;
use Illuminate\Http\Request;
use App\Models\Car;


class ServiceEntryController extends Controller
{
    public function index(Request $request)
    {
        $cars = Car::where('user_id', auth()->id())->get();
        $query = ServiceEntry::with('car')->where('user_id', auth()->id());
        
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('service_action', 'like', "%{$search}%")
                  ->orWhere('parts_replaced', 'like', "%{$search}%");
            });
        }
        
        if ($request->filled('date_from')) {
            $query->where('date', '>=', $request->input('date_from'));
        }
        
        if ($request->filled('date_to')) {
            $query->where('date', '<=', $request->input('date_to'));
        }
        
        if ($request->filled('service_name')) {
            $query->where('service_name', $request->input('service_name'));
        }
        
        // if ($request->filled('license_plate')) {
        //     $query->whereHas('car', function ($q) use ($request) {
        //         $q->where('license_plate', $request->input('license_plate'));
        //     });
        // }

        if ($request->filled('car_id')) {
            $query->where('car_id', $request->input('car_id'));
        }
        
        $entries = $query->orderBy('date', 'desc')->paginate(10);
        
        $serviceNames = ServiceEntry::where('user_id', auth()->id())->select('service_name')->distinct()->pluck('service_name');
        $licensePlates = Car::where('user_id', auth()->id())
                    ->select('license_plate')->distinct()->pluck('license_plate');

        return view('service_entries.index', compact('entries', 'serviceNames', 'licensePlates', 'cars'));
    }

    public function create()
    {
        $cars = Car::where('user_id', auth()->id())->get();
        return view('service_entries.create', compact('cars'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'kilometers' => 'required|integer',
            'car_id' => 'required|exists:cars,id',
            'service_name' => 'required|string|max:100',
            'service_action' => 'required|string',
            'parts_replaced' => 'nullable|string',
            'cost' => 'nullable|numeric',
        ]);
        
        // Check car ownership
        $car = Car::findOrFail($validated['car_id']);
        abort_if($car->user_id !== auth()->id(), 403);

        $validated['user_id'] = auth()->id();
        
        ServiceEntry::create($validated);
        
        return redirect()->route('service-entries.index')->with('success', 'Intrare adăugată cu succes!');
    }

    public function show(ServiceEntry $serviceEntry)
    {
        abort_if($serviceEntry->user_id !== auth()->id(), 403);
        return view('service_entries.show', compact('serviceEntry'));
    }

    public function edit(ServiceEntry $serviceEntry)
    {
        abort_if($serviceEntry->user_id !== auth()->id(), 403);
        $cars = Car::where('user_id', auth()->id())->get();
        return view('service_entries.edit', compact('serviceEntry', 'cars'));
    }

    public function update(Request $request, ServiceEntry $serviceEntry)
    {
        abort_if($serviceEntry->user_id !== auth()->id(), 403);
    
        $validated = $request->validate([
            'date' => 'required|date',
            'kilometers' => 'required|integer',
            'car_id' => 'required|exists:cars,id',
            'service_name' => 'required|string|max:100',
            'service_action' => 'required|string',
            'parts_replaced' => 'nullable|string',
            'cost' => 'nullable|numeric',
        ]);
        
        // Verificare ca masina selectata apartine utilizatorului
        $car = Car::findOrFail($validated['car_id']);
        abort_if($car->user_id !== auth()->id(), 403);

        $serviceEntry->update($validated);
        return redirect()->route('service-entries.index')->with('success', 'Intrare actualizată cu succes!');
    }

    public function destroy(ServiceEntry $serviceEntry)
    {
        abort_if($serviceEntry->user_id !== auth()->id(), 403);
        $serviceEntry->delete();
        
        return redirect()->route('service-entries.index')->with('success', 'Intrare ștearsă cu succes!');
    }
}