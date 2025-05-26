<?php

namespace App\Http\Controllers;

use App\Models\ServiceEntry;
use Illuminate\Http\Request;

class ServiceEntryController extends Controller
{
    public function index(Request $request)
    {
        $query = ServiceEntry::where('user_id', auth()->id());
        
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
        
        if ($request->filled('license_plate')) {
            $query->where('license_plate', $request->input('license_plate'));
        }
        
        $entries = $query->orderBy('date', 'desc')->paginate(10);
        
        $serviceNames = ServiceEntry::where('user_id', auth()->id())->select('service_name')->distinct()->pluck('service_name');
        $licensePlates = ServiceEntry::where('user_id', auth()->id())->select('license_plate')->distinct()->pluck('license_plate');
        //$licensePlates = ServiceEntry::distinct('license_plate')->pluck('license_plate');
        
        return view('service_entries.index', compact('entries', 'serviceNames', 'licensePlates'));
    }

    public function create()
    {
        $existingPlates = ServiceEntry::where('user_id', auth()->id())->select('license_plate')->distinct()->pluck('license_plate');
        return view('service_entries.create', compact('existingPlates'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'kilometers' => 'required|integer',
            'license_plate' => 'required|string|max:20',
            'service_name' => 'required|string|max:100',
            'service_action' => 'required|string',
            'parts_replaced' => 'nullable|string',
            'cost' => 'nullable|numeric',
        ]);
        
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
        return view('service_entries.edit', compact('serviceEntry'));
    }

    public function update(Request $request, ServiceEntry $serviceEntry)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'kilometers' => 'required|integer',
            'license_plate' => 'required|string|max:20',
            'service_name' => 'required|string|max:100',
            'service_action' => 'required|string',
            'parts_replaced' => 'nullable|string',
            'cost' => 'nullable|numeric',
        ]);
        
        $serviceEntry->update($validated);
        abort_if($serviceEntry->user_id !== auth()->id(), 403);
        return redirect()->route('service-entries.index')->with('success', 'Intrare actualizată cu succes!');
    }

    public function destroy(ServiceEntry $serviceEntry)
    {
        abort_if($serviceEntry->user_id !== auth()->id(), 403);
        $serviceEntry->delete();
        
        return redirect()->route('service-entries.index')->with('success', 'Intrare ștearsă cu succes!');
    }
}