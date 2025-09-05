<?php

namespace App\Http\Controllers;

use App\Models\ApplicationAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ApplicationAttachmentController extends Controller
{
    public function index($applicationId)
    {
        $attachments = ApplicationAttachment::where('application_id', $applicationId)
            ->orderBy('created_at','DESC')->get();

        return response()->json([
            'status' => 200,
            'data' => $attachments
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'application_id' => 'required|exists:applications,id',
            'filename' => 'required|string|max:255',
            'url' => 'required|url|max:1024',
            'content_type' => 'nullable|string|max:255',
            'size' => 'nullable|integer',
            'uploaded_by' => 'nullable|exists:users,id',
            'meta' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors()
            ], 400);
        }

        $attachment = ApplicationAttachment::create($request->only(
            'application_id','filename','url','content_type','size','uploaded_by','meta'
        ));

        return response()->json([
            'status' => 200,
            'message' => 'Attachment added successfully.',
            'data' => $attachment
        ]);
    }

    public function show($id)
    {
        $attachment = ApplicationAttachment::find($id);
        if (!$attachment) {
            return response()->json([
                'status' => 404,
                'message' => 'Attachment not found.',
                'data' => []
            ], 404);
        }

        return response()->json([
            'status' => 200,
            'data' => $attachment
        ]);
    }

    public function update(Request $request, $id)
    {
        $attachment = ApplicationAttachment::find($id);
        if (!$attachment) {
            return response()->json([
                'status' => 404,
                'message' => 'Attachment not found.',
                'data' => []
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'filename' => 'nullable|string|max:255',
            'url' => 'nullable|url|max:1024',
            'content_type' => 'nullable|string|max:255',
            'size' => 'nullable|integer',
            'uploaded_by' => 'nullable|exists:users,id',
            'meta' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors()
            ], 400);
        }

        $attachment->update($request->only(
            'filename','url','content_type','size','uploaded_by','meta'
        ));

        return response()->json([
            'status' => 200,
            'message' => 'Attachment updated successfully.',
            'data' => $attachment
        ]);
    }

    public function destroy($id)
    {
        $attachment = ApplicationAttachment::find($id);
        if (!$attachment) {
            return response()->json([
                'status' => 404,
                'message' => 'Attachment not found.',
                'data' => []
            ], 404);
        }

        $attachment->delete();
        return response()->json([
            'status' => 200,
            'message' => 'Attachment deleted successfully.'
        ]);
    }
}
