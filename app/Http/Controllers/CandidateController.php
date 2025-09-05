<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class CandidateController extends Controller
{
    /**
     * List all candidates (admin/recruiter only)
     */
    public function index(Request $request)
    {
        $user = $request->user();

        if (!in_array($user->role, ['admin', 'recruiter'])) {
            return response()->json(['status' => 403, 'message' => 'Access denied'], 403);
        }

        $candidates = Candidate::orderBy('created_at', 'desc')->get();
        return response()->json(['status' => 200, 'data' => $candidates]);
    }

    /**
     * Show candidate profile by ID
     */
    public function show(Request $request, $id)
    {
        $candidate = Candidate::find($id);

        if (!$candidate) {
            return response()->json(['status' => 404, 'message' => 'Candidate not found'], 404);
        }

        $user = $request->user();
        if ($candidate->user_id !== $user->id && !in_array($user->role, ['admin', 'recruiter'])) {
            return response()->json(['status' => 403, 'message' => 'Access denied'], 403);
        }

        return response()->json(['status' => 200, 'data' => $candidate]);
    }

    /**
     * Show logged-in candidate profile
     */
  // CandidateController.php
public function myProfile(Request $request)
{
    $user = $request->user();
    $candidate = Candidate::where('user_id', $user->id)->first();

    if (!$candidate) {
        return response()->json(['status'=>404,'message'=>'Candidate not found'],404);
    }

    return response()->json(['status'=>200,'data'=>$candidate]);
}

public function updateMyProfile(Request $request)
{
    $user = $request->user();
    $candidate = Candidate::where('user_id', $user->id)->first();

    if (!$candidate) {
        return response()->json(['status'=>404,'message'=>'Candidate not found'],404);
    }

    $validator = Validator::make($request->all(), [
        'name' => 'nullable|string|max:255',
        'email'=> 'nullable|email|unique:candidates,email,' . $candidate->id,
        'phone'=> 'nullable|string|max:20',
        'linkedin_url'=> 'nullable|url',
        'resume'=> 'nullable|file|mimes:pdf,doc,docx|max:2048',
    ]);

    if ($validator->fails()) {
        return response()->json(['status'=>400,'errors'=>$validator->errors()],400);
    }

    $candidate->fill($request->only(['name','email','phone','linkedin_url']));

    // Handle resume upload
    if ($request->hasFile('resume')) {
        if ($candidate->resume_url) {
            Storage::disk('public')->delete($candidate->resume_url);
        }
        $candidate->resume_url = $request->file('resume')->store('resumes','public');
    }

    $candidate->save();

    return response()->json(['status'=>200,'message'=>'Profile updated','data'=>$candidate]);
}


    /**
     * Create candidate profile (only if not exists)
     */
    public function store(Request $request)
    {
        $user = $request->user();

        // Check if candidate already exists for user
        if (Candidate::where('user_id', $user->id)->exists()) {
            return response()->json(['status' => 400, 'message' => 'Profile already exists'], 400);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:candidates,email',
            'phone' => 'nullable|string|max:20',
            'linkedin_url' => 'nullable|url',
            'resume' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 400, 'errors' => $validator->errors()], 400);
        }

        $candidate = new Candidate();
        $candidate->user_id = $user->id;
        $candidate->name = $request->name;
        $candidate->email = $request->email;
        $candidate->phone = $request->phone ?? null;
        $candidate->linkedin_url = $request->linkedin_url ?? null;

        if ($request->hasFile('resume')) {
            $candidate->resume_url = $request->file('resume')->store('resumes', 'public');
        }

        $candidate->save();

        return response()->json(['status' => 201, 'message' => 'Profile created', 'data' => $candidate]);
    }

    /**
     * Update candidate profile
     */
     
    public function update(Request $request)
{
    $user = $request->user();
    $candidate = Candidate::where('user_id', $user->id)->first();

    if (!$candidate) {
        return response()->json(['status'=>404,'message'=>'Candidate not found'],404);
    }

    $data = $request->all();

    $validator = Validator::make($data, [
        'name' => 'nullable|string|max:255',
        'email'=> 'nullable|email|unique:candidates,email,' . $candidate->id,
        'phone'=> 'nullable|string|max:20',
        'linkedin_url'=> 'nullable|url',
        'resume'=> 'nullable|file|mimes:pdf,doc,docx|max:2048',
    ]);

    if ($validator->fails()) {
        return response()->json(['status'=>400,'errors'=>$validator->errors()],400);
    }

    $candidate->fill($request->only(['name','email','phone','linkedin_url']));

    if ($request->hasFile('resume')) {
        if ($candidate->resume_url) {
            Storage::disk('public')->delete($candidate->resume_url);
        }
        $candidate->resume_url = $request->file('resume')->store('resumes','public');
    }

    $candidate->save();

    return response()->json(['status'=>200,'message'=>'Profile updated','data'=>$candidate]);
}


    /**
     * Delete candidate (admin only)
     */
    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        if ($user->role !== 'admin') {
            return response()->json(['status' => 403, 'message' => 'Access denied'], 403);
        }

        $candidate = Candidate::find($id);
        if (!$candidate) {
            return response()->json(['status' => 404, 'message' => 'Candidate not found'], 404);
        }

        if ($candidate->resume_url) {
            Storage::disk('public')->delete($candidate->resume_url);
        }

        $candidate->delete();

        return response()->json(['status' => 200, 'message' => 'Candidate deleted successfully']);
    }
}
