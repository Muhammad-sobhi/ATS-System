<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Job;
use App\Models\Tag;

class JobTagController extends Controller
{
    // Attach a tag to a job
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'job_id' => 'required|exists:jobs,id',
            'tag_id' => 'required|exists:tags,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 400, 'errors' => $validator->errors()], 400);
        }

        $job = Job::find($request->job_id);
        $job->tags()->syncWithoutDetaching([$request->tag_id]);

        return response()->json(['status' => 200, 'message' => 'Tag attached successfully']);
    }

    // Detach a tag from a job
    public function destroy($id)
    {
        // $id is usually a composite of job_id + tag_id or pass via request
        $job_id = request()->input('job_id');
        $tag_id = request()->input('tag_id');

        if (!$job_id || !$tag_id) {
            return response()->json(['status' => 400, 'message' => 'job_id and tag_id required'], 400);
        }

        $job = Job::find($job_id);
        if (!$job) {
            return response()->json(['status' => 404, 'message' => 'Job not found'], 404);
        }

        $job->tags()->detach($tag_id);

        return response()->json(['status' => 200, 'message' => 'Tag detached successfully']);
    }
}
