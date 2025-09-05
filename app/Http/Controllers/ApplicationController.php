<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Job;
use App\Models\Candidate;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ApplicationController extends Controller
{
    /**
     * List applications based on user role
     */
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->role === 'admin') {
            $applications = Application::with('job', 'candidate')
                ->orderBy('created_at', 'desc')
                ->get();
        } elseif ($user->role === 'recruiter') {
            $applications = Application::whereHas('job', function ($q) use ($user) {
                $q->where('recruiter_id', $user->id);
            })
            ->with('job', 'candidate')
            ->orderBy('created_at', 'desc')
            ->get();
        } else {
            // Candidate sees only their own applications
            $applications = Application::where('candidate_id', $user->id)
                ->with('job')
                ->orderBy('created_at', 'desc')
                ->get();
        }

        $formatted = $applications->map(function ($app) use ($user) {
            return [
                'id' => $app->id,
                'status' => $app->stage->name ?? $app->status ?? 'N/A',
                'applied_at' => $app->created_at->toDateTimeString(),
                'job_title' => $app->job->title ?? null,
                'company' => $app->job->company ?? null,
                'candidate_name' => $user->role !== 'candidate' ? ($app->candidate->name ?? null) : null,
                'candidate_email' => $user->role !== 'candidate' ? ($app->candidate->email ?? null) : null,
            ];
        });

        return response()->json(['status' => 200, 'data' => $formatted]);
    }

    /**
     * Show single application
     */
    public function show($id, Request $request)
{
    $user = $request->user();
    $application = Application::with('job', 'candidate', 'stage')->find($id);

    if (!$application) {
        return response()->json(['status' => 404, 'message' => 'Application not found'], 404);
    }

    if ($user->role === 'candidate' && $application->candidate_id !== $user->id) {
        return response()->json(['status' => 403, 'message' => 'Access denied'], 403);
    }

    if ($user->role === 'recruiter' && $application->job->recruiter_id !== $user->id) {
        return response()->json(['status' => 403, 'message' => 'Access denied'], 403);
    }

    $formatted = [
        'id' => $application->id,
        'status' => $application->stage?->name ?? $application->status ?? 'N/A',
        'applied_at' => $application->created_at,
        'job' => $application->job,
        'candidate' => $application->candidate,
        'resume_snapshot' => $application->resume_snapshot,
        'assigned_to' => $application->assigned_to,
        'notes' => $application->notes,
    ];

    return response()->json(['status' => 200, 'data' => $formatted]);
}


    /**
     * Store a new application (candidate applies)
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'job_id' => 'required|exists:jobs,id',
            'candidate_id' => 'required|exists:candidates,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 400, 'errors' => $validator->errors()], 400);
        }

        $existing = Application::where('job_id', $request->job_id)
            ->where('candidate_id', $request->candidate_id)
            ->first();

        if ($existing) {
            return response()->json(['status' => 409, 'message' => 'Already applied', 'data' => $existing], 409);
        }

        $application = Application::create([
            'job_id' => $request->job_id,
            'candidate_id' => $request->candidate_id,
            'status' => 'applied',
        ]);

        return response()->json(['status' => 200, 'message' => 'Application submitted', 'data' => $application]);
    }

    /**
     * Update an application (admin/recruiter)
     */
    public function update(Request $request, $id)
    {
        $user = $request->user();
        $application = Application::find($id);

        if (!$application) {
            return response()->json(['status' => 404, 'message' => 'Application not found'], 404);
        }

        if ($user->role === 'candidate') {
            return response()->json(['status' => 403, 'message' => 'Access denied'], 403);
        }

        if ($user->role === 'recruiter' && $application->job->recruiter_id !== $user->id) {
            return response()->json(['status' => 403, 'message' => 'Access denied'], 403);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'nullable|in:applied,phone_screen,interview,hired,rejected',
            'assigned_to' => 'nullable|exists:users,id',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 400, 'errors' => $validator->errors()], 400);
        }

        $application->update($request->only(['status','assigned_to','notes']));

        return response()->json(['status' => 200, 'message' => 'Application updated', 'data' => $application]);
    }

    /**
     * Delete an application (admin/recruiter)
     */
    public function destroy($id, Request $request)
    {
        $user = $request->user();
        $application = Application::find($id);

        if (!$application) {
            return response()->json(['status' => 404, 'message' => 'Application not found'], 404);
        }

        if ($user->role === 'candidate') {
            return response()->json(['status' => 403, 'message' => 'Access denied'], 403);
        }

        if ($user->role === 'recruiter' && $application->job->recruiter_id !== $user->id) {
            return response()->json(['status' => 403, 'message' => 'Access denied'], 403);
        }

        $application->delete();

        return response()->json(['status' => 200, 'message' => 'Application deleted']);
    }

    /**
     * Candidate sees only their own applications
     */
    public function myApplications(Request $request)
    {
        $user = $request->user();

        if ($user->role !== 'candidate') {
            return response()->json(['status' => 403, 'message' => 'Access denied'], 403);
        }

        $applications = Application::where('candidate_id', $user->id)
            ->with('job')
            ->orderBy('created_at','desc')
            ->get();

        return response()->json(['status' => 200, 'data' => $applications]);
    }
}
