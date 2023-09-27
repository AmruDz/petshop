<?php

namespace App\Http\Controllers;

use App\Models\Units;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UnitsController extends Controller
{
    //api route
    public function index()
    {
        try {
            $units = Units::orderBy('name', 'asc')->get();

            return response()->json([
                'message' => 'Units retrieved successfully',
                'data' => $units,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'An error occurred while displaying unit data',
            ], 500);
        }
    }

    //web route
    public function indexMaster()
    {
        $unit = Units::orderBy('name', 'asc')->get();

        return view('', compact('unit'));
    }
    public function createMaster()
    {
        return view('', compact('unit'));
    }
    public function storeMaster(Request $request)
    {
        Validator::make($request->all(), [
            'name' => 'required',
        ]);

        $unit = Units::create($request->all());

        return redirect()->route('unit')->with('success', 'Unit created successfully');
    }
    public function editMaster()
    {
        return view('', compact('unit'));
    }
    public function updateMaster(Request $request, $id)
    {
        Validator::make($request->all(), [
            'name' => 'required',
        ])->validate();

        $unit = Units::findOrFail($id);

        if (!$unit) {
            return redirect()->route('unit')->with('error', 'Unit not found');
        }

        $unit->update($request->all());

        return redirect()->route('unit')->with('success', 'Unit updated successfully');
    }
    public function destroyMaster($id)
    {
        $unit = Units::find($id);

        if (!$unit) {
            return redirect()->route('unit')->with('error', 'Unit not found');
        }

        $unit->delete();

        return redirect()->route('unit')->with('success', 'Unit successfully deleted');
    }
}
