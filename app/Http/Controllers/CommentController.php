<?php

namespace App\Http\Controllers;

use App\Models\Commentmodel;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CommentController extends Controller
{
    public function index()
    {
        $comment = Commentmodel::all();
        return response()->json([
            'success' => true,
            'data' => $comment
        ]);
    }

    public function show($id)
    {
        $comment = Commentmodel::find($id);
        if (!$comment) {
            return response()->json([
                'success' => false,
                'message' => 'Comment with id ' . $id . ' not found'
            ], 404);
        }
        return response()->json([
            'success' => true,
            'data' => $comment
        ]);
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'comment' => 'required|string',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors'  => $e->errors()
            ], 422);
        }

        $comment = Commentmodel::create([
            'comment' => $request->comment,
        ]);

        return response()->json([
            'success' => true,
            'data' => $comment
        ], 201);
    }

    public function update(Request $request, string $id)
    {
        $comment = Commentmodel::find($id);
        if (!$comment) {
            return response()->json([
                'success' => false,
                'message' => 'Comment with id ' . $id . ' not found'
            ], 404);
        }

        try {
            $request->validate([
                'comment' => 'required|string',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors'  => $e->errors()
            ], 422);
        }
        $comment->update([
            'comment' => $request->comment,
        ]);

        return response()->json([
            'success' => true,
            'data' => $comment
        ]);
    }

    public function destroy(string $id)
    {
        $comment = Commentmodel::find($id);

        if (!$comment) {
            return response()->json([
                'success' => false,
                'message' => 'Comment is not found'
            ], 404);
        }

        $comment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Comment deleted successfully'
        ]);
    }
}
