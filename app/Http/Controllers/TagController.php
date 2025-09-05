<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TagController extends Controller
{
    public function index()
    {
        $tags = Tag::orderBy('name')->get();
        return response()->json([
            'status' => 200,
            'data' => $tags
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:tags,name',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors()
            ], 400);
        }

        $tag = Tag::create($request->only('name'));

        return response()->json([
            'status' => 200,
            'message' => 'Tag added successfully.',
            'data' => $tag
        ]);
    }

    public function show($id)
    {
        $tag = Tag::find($id);
        if (!$tag) {
            return response()->json([
                'status' => 404,
                'message' => 'Tag not found.',
                'data' => []
            ], 404);
        }

        return response()->json([
            'status' => 200,
            'data' => $tag
        ]);
    }

    public function update(Request $request, $id)
    {
        $tag = Tag::find($id);
        if (!$tag) {
            return response()->json([
                'status' => 404,
                'message' => 'Tag not found.',
                'data' => []
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:tags,name,'.$id,
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors()
            ], 400);
        }

        $tag->update($request->only('name'));

        return response()->json([
            'status' => 200,
            'message' => 'Tag updated successfully.',
            'data' => $tag
        ]);
    }

    public function destroy($id)
    {
        $tag = Tag::find($id);
        if (!$tag) {
            return response()->json([
                'status' => 404,
                'message' => 'Tag not found.',
                'data' => []
            ], 404);
        }

        $tag->delete();
        return response()->json([
            'status' => 200,
            'message' => 'Tag deleted successfully.'
        ]);
    }
}
