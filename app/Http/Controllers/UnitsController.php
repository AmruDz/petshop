<?php

namespace App\Http\Controllers;

use App\Models\Units;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UnitsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Units::all();
        return response()->json($data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required',
        ]);

        // $unit = Units::create([
        //     'name' => $validatedData['name']
        // ]);

        $unit = Units::create($validatedData);

        return response()->json([
            'message' => 'Unit created successfully',
            'data' => $unit,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $data = Units::findOrFail($id);
        return response()->json($data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Units $unit)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $data = $request->all();
        $unit = Units::findOrFail($id);

        // $unit->update([
        //     'name' => $data['name'],
        // ]);

        $unit->update($data);

        return response()->json([
            'message' => 'Unit updated successfully!',
            'data' => $unit,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $unit = Units::findOrFail($id);

        if (!$unit){
            return response()->json(['message' => 'Unit not found!', Response::HTTP_NOT_FOUND]);
        }

        $unit->delete();

        return response()->json(['message' => 'Unit deleted successfully!', Response::HTTP_OK]);
    }
}
