<?php

namespace App\Http\Controllers;

use App\Models\Units;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UnitsController extends Controller
{
    public function index()
    {
        try {
            $units = Units::all();

            return response()->json([
                'message' => 'Units retrieved successfully',
                'data' => $units,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'An error occurred while displaying category data',
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Invalid unit data',
                'errors' => $validator->errors(),
            ], 400);
        }

        try {
            $unit = Units::create($request->all());

            return response()->json([
                'message' => 'Unit created successfully',
                'data' => $unit,
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'An error occurred while creating an unit',
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Invalid unit data',
                'errors' => $validator->errors(),
            ], 400);
        }

        try {
            $unit = Units::findOrFail($id);

            if (!$unit) {
                return response()->json([
                    'message' => 'Unit not found!',
                ], 404);
            }

            $unit->update($request->all());

            return response()->json([
                'message' => 'Unit updated successfully!',
                'data' => $unit,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'An error occurred while updating an unit',
            ], 500);
        }
    }

    public function destroy($id)
    {
        $unit = Units::find($id);

        if (!$unit) {
            return response()->json([
                'message' => 'Unit not found!',
            ], 404);
        }

        try {
            $unit->delete();
            return response()->json([
                'message' => 'Unit deleted successfully!',
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'An error occurred while deleting an unit',
            ], 500);
        }
    }
}
