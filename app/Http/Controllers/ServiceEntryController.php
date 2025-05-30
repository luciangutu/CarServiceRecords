<?php

namespace App\Http\Controllers;

use App\Models\ServiceEntry;
use Illuminate\Http\Request;
use App\Models\Car;


class ServiceEntryController extends Controller
{
    public function index(Car $car, Request $request)
    {
        abort_if($car->user_id !== auth()->id(), 403);

        $query = $car->serviceEntries()->with('car')->where('user_id', auth()->id()); // Ensure user_id check for paranoia, though car ownership implies it.
        
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
        
        $entries = $query->orderBy('date', 'desc')->paginate(10);
        
        // For filtering dropdowns, specific to this car's entries or all user's entries?
        // Let's assume specific to this car for now if relevant, or remove if filters are global.
        $serviceNames = $car->serviceEntries()->where('user_id', auth()->id())->select('service_name')->distinct()->pluck('service_name');
        
        return view('service_entries.index', compact('car', 'entries', 'serviceNames'));
    }

    public function create(Car $car)
    {
        abort_if($car->user_id !== auth()->id(), 403);
        return view('service_entries.create', compact('car'));
    }

    public function store(Request $request, Car $car)
    {
        abort_if($car->user_id !== auth()->id(), 403);

        $validated = $request->validate([
            'date' => 'required|date',
            'kilometers' => 'required|integer',
            // car_id will be set from the route model $car
            'service_name' => 'required|string|max:100',
            'service_action' => 'required|string',
            'parts_replaced' => 'nullable|string',
            'cost' => 'required|numeric', // Made required as per test expectation
        ]);
        
        $validated['user_id'] = auth()->id();
        $validated['car_id'] = $car->id; // Set car_id from the route model
        
        $serviceEntry = ServiceEntry::create($validated);
        
        return redirect()->route('cars.service_entries.index', $car)->with('success', 'Intrare adăugată cu succes!');
    }

    public function show(ServiceEntry $serviceEntry)
    {
        $car = $serviceEntry->car;
        abort_if($car->user_id !== auth()->id(), 403); // Check ownership of the car linked to the service entry
        return view('service_entries.show', compact('serviceEntry', 'car'));
    }

    public function edit(ServiceEntry $serviceEntry)
    {
        $car = $serviceEntry->car;
        abort_if($car->user_id !== auth()->id(), 403);
        // $cars needed if we want to allow changing the car, but current tests don't imply this.
        // If we don't allow changing car, then $car is enough.
        return view('service_entries.edit', compact('serviceEntry', 'car'));
    }

    public function update(Request $request, ServiceEntry $serviceEntry)
    {
        $car = $serviceEntry->car;
        abort_if($car->user_id !== auth()->id(), 403);
    
        $validated = $request->validate([
            'date' => 'required|date',
            'kilometers' => 'required|integer',
            // 'car_id' => 'required|exists:cars,id', // Do not allow changing car_id here to prevent moving to another user's car
            'service_name' => 'required|string|max:100',
            'service_action' => 'required|string',
            'parts_replaced' => 'nullable|string',
            'cost' => 'required|numeric', // Made required as per test expectation
        ]);
        
        // Ensure user_id is not changed if it's part of the fillable fields (though it shouldn't be for update by user)
        // $validated['user_id'] = auth()->id(); // This should not be needed as user_id should not change
        
        $serviceEntry->update($validated);
        return redirect()->route('service_entries.show', $serviceEntry)->with('success', 'Intrare actualizată cu succes!');
    }

    public function destroy(ServiceEntry $serviceEntry)
    {
        $car = $serviceEntry->car;
        abort_if($car->user_id !== auth()->id(), 403);
        $serviceEntry->delete();
        
        return redirect()->route('cars.service_entries.index', $car)->with('success', 'Intrare ștearsă cu succes!');
    }
}