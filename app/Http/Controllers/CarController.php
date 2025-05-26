<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Car;

class CarController extends Controller
{
    public function index()
    {
        $cars = Car::where('user_id', auth()->id())->get();
        return view('cars.index', compact('cars'));
    }

    public function create()
    {
        return view('cars.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'make' => 'required|string|max:50',
            'model' => 'required|string|max:50',
            'license_plate' => 'required|string|max:20|unique:cars,license_plate',
            'vin' => 'nullable|string|max:50',
        ]);
        $validated['user_id'] = auth()->id();

        Car::create($validated);

        return redirect()->route('cars.index')->with('success', 'Masina a fost adaugata!');
    }

    public function edit(Car $car)
    {
        abort_if($car->user_id !== auth()->id(), 403);
        return view('cars.edit', compact('car'));
    }

    public function update(Request $request, Car $car)
    {
        abort_if($car->user_id !== auth()->id(), 403);

        $validated = $request->validate([
            'make' => 'required|string|max:50',
            'model' => 'required|string|max:50',
            'license_plate' => 'required|string|max:20|unique:cars,license_plate,' . $car->id,
            'vin' => 'nullable|string|max:50',
        ]);

        $car->update($validated);

        return redirect()->route('cars.index')->with('success', 'Masina a fost actualizata!');
    }
    public function destroy(Car $car)
    {
        abort_if($car->user_id !== auth()->id(), 403);
        $car->delete();

        return redirect()->route('cars.index')->with('success', 'Masina a fost stearsa!');
    }
}
