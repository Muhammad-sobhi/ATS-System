<?php

namespace App\Http\Controllers;


use App\Models\Job;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class JobController extends Controller
{
    /**
     * Display a listing of jobs based on user role.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->role === 'admin') {
            $jobs = Job::with('recruiter')->orderBy('created_at', 'desc')->get();
        } elseif ($user->role === 'recruiter') {
            $jobs = Job::where('recruiter_id', $user->id)->with('recruiter')->orderBy('created_at', 'desc')->get();
        } else {
            // candidate
            $jobs = Job::where('status', 'open')->with('recruiter')->orderBy('created_at', 'desc')->get();
        }

        return response()->json([
            'status' => 200,
            'data' => $jobs
        ]);
    }

    /**
     * Show a specific job.
     */
    public function show($id, Request $request)
    {
        $user = $request->user();
        $job = Job::with('recruiter')->find($id);

        if (!$job) {
            return response()->json(['status' => 404, 'message' => 'Job not found'], 404);
        }

        // Candidate cannot see closed/paused jobs
        if ($user->role === 'candidate' && $job->status !== 'open') {
            return response()->json(['status' => 403, 'message' => 'Access denied'], 403);
        }

        // Recruiter can see only their jobs
        if ($user->role === 'recruiter' && $job->recruiter_id !== $user->id) {
            return response()->json(['status' => 403, 'message' => 'Access denied'], 403);
        }

        return response()->json(['status' => 200, 'data' => $job]);
    }

    /**
     * Store a new job (admin or recruiter)
     */
    public function store(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'location' => 'nullable|string',
            'type' => 'required|in:full_time,part_time,remote,contract',
            'department' => 'nullable|string',
            'slots' => 'nullable|integer|min:1',
        ]);

        $job = new Job();
        $job->title = $request->title;
        $job->slug = Str::slug($request->title . '-' . time());
        $job->description = $request->description;
        $job->location = $request->location;
        $job->type = $request->type;
        $job->department = $request->department;
        $job->slots = $request->slots ?? 1;

        // Recruiter can create only their own job
        $job->recruiter_id = $user->role === 'recruiter' ? $user->id : ($request->recruiter_id ?? $user->id);

        $job->save();

        return response()->json(['status' => 200, 'message' => 'Job created successfully', 'data' => $job]);
    }

    /**
     * Update job (admin or recruiter)
     */
    public function update(Request $request, $id)
    {
        $user = $request->user();
        $job = Job::find($id);

        if (!$job) {
            return response()->json(['status' => 404, 'message' => 'Job not found'], 404);
        }

        // Recruiter can update only their jobs
        if ($user->role === 'recruiter' && $job->recruiter_id !== $user->id) {
            return response()->json(['status' => 403, 'message' => 'Access denied'], 403);
        }

        $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'location' => 'nullable|string',
            'type' => 'required|in:full_time,part_time,remote,contract',
            'department' => 'nullable|string',
            'slots' => 'nullable|integer|min:1',
            'status' => 'nullable|in:open,closed,paused',
        ]);

        $job->update($request->only(['title','description','location','type','department','slots','status']));

        return response()->json(['status' => 200, 'message' => 'Job updated successfully', 'data' => $job]);
    }

    /**
     * Delete job (admin or recruiter)
     */
    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        $job = Job::find($id);

        if (!$job) {
            return response()->json(['status' => 404, 'message' => 'Job not found'], 404);
        }

        if ($user->role === 'recruiter' && $job->recruiter_id !== $user->id) {
            return response()->json(['status' => 403, 'message' => 'Access denied'], 403);
        }

        $job->delete();

        return response()->json(['status' => 200, 'message' => 'Job deleted successfully']);
    }
}
