<?php

namespace App\Http\Controllers;

use App\Models\JobStage;
use Illuminate\Http\Request;

class JobStageController extends Controller
{
    // List all stages
    public function index()
    {
        $stages = JobStage::orderBy('id')->get();
        return response()->json([
            'status' => 200,
            'data' => $stages
        ]);
    }

    // Create a new stage
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:job_stages,name'
        ]);

        $stage = JobStage::create([
            'name' => $request->name
        ]);

        return response()->json([
            'status' => 200,
            'message' => 'Job stage created successfully',
            'data' => $stage
        ]);
    }

    // Show a single stage
    public function show($id)
    {
        $stage = JobStage::find($id);
        if (!$stage) {
            return response()->json([
                'status' => 404,
                'message' => 'Stage not found'
            ], 404);
        }

        return response()->json([
            'status' => 200,
            'data' => $stage
        ]);
    }

    // Update a stage
    public function update(Request $request, $id)
    {
        $stage = JobStage::find($id);
        if (!$stage) {
            return response()->json([
                'status' => 404,
                'message' => 'Stage not found'
            ], 404);
        }

        $request->validate([
            'name' => 'required|string|unique:job_stages,name,' . $id
        ]);

        $stage->name = $request->name;
        $stage->save();

        return response()->json([
            'status' => 200,
            'message' => 'Job stage updated successfully',
            'data' => $stage
        ]);
    }

    // Delete a stage
    public function destroy($id)
    {
        $stage = JobStage::find($id);
        if (!$stage) {
            return response()->json([
                'status' => 404,
                'message' => 'Stage not found'
            ], 404);
        }

        $stage->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Job stage deleted successfully'
        ]);
    }
}
