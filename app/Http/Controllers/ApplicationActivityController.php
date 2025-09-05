<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ApplicationActivity;
use Illuminate\Support\Facades\Validator;

class ApplicationActivityController extends Controller
{
    // List all activities (optional)
    public function index()
    {
        $activities = ApplicationActivity::orderBy('created_at', 'desc')->get();
        return response()->json([
            'status' => 200,
            'data' => $activities
        ]);
    }

    // Create new activity
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'application_id' => 'required|exists:applications,id',
            'actor_id' => 'nullable|exists:users,id',
            'type' => 'required|in:status_change,comment,assignment,file_upload,note',
            'payload' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors()
            ], 400);
        }

        $activity = ApplicationActivity::create([
            'application_id' => $request->application_id,
            'actor_id' => $request->actor_id,
            'type' => $request->type,
            'payload' => $request->payload,
            'created_at' => now()
        ]);

        return response()->json([
            'status' => 200,
            'message' => 'Activity created successfully',
            'data' => $activity
        ]);
    }

    // Show single activity
    public function show($id)
    {
        $activity = ApplicationActivity::find($id);
        if (!$activity) {
            return response()->json([
                'status' => 404,
                'message' => 'Activity not found'
            ], 404);
        }
        return response()->json([
            'status' => 200,
            'data' => $activity
        ]);
    }

    // Optionally, delete activity
    public function destroy($id)
    {
        $activity = ApplicationActivity::find($id);
        if (!$activity) {
            return response()->json([
                'status' => 404,
                'message' => 'Activity not found'
            ], 404);
        }
        $activity->delete();
        return response()->json([
            'status' => 200,
            'message' => 'Activity deleted successfully'
        ]);
    }
}
