<?php

namespace App\Http\Controllers;

use App\Models\Models;
use App\Models\Categoriesmodel;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ModelsController extends Controller
{
    public function index()
    {
        $models = Models::with('categories:id,name')->get();

        return response()->json([
            'success' => true,
            'data'    => $models
        ]);
    }
    public function show($id)
    {
        $models = Models::with('categories:id,name')->find($id);

        if (!$models) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, model with id ' . $id . ' cannot be found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $models
        ]);
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name'          => 'required|string|max:255',
                'categories_id' => 'required|exists:categories,id',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors'  => $e->errors()
            ], 422);
        }

        $models = Models::create([
            'name'          => $request->name,
            'categories_id' => $request->categories_id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Model has been created',
            'data'    => $models
        ], 201);
    }
    public function update(Request $request, $id)
    {
        $models = Models::find($id);

        if (!$models) {
            return response()->json([
                'success' => false,
                'message' => 'Model topilmadi'
            ], 404);
        }

        try {
            $request->validate([
                'name'          => 'sometimes|string|max:255',
                'categories_id' => 'sometimes|exists:categories,id',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors'  => $e->errors()
            ], 422);
        }

        $models->update($request->only(['name', 'categories_id']));

        return response()->json([
            'success' => true,
            'message' => 'Model tahrirlandi',
            'data'    => $models
        ]);
    }
    public function destroy($id)
    {
        $models = Models::find($id);

        if (!$models) {
            return response()->json([
                'success' => false,
                'message' => 'Model topilmadi'
            ], 404);
        }

        $models->delete();

        return response()->json([
            'success' => true,
            'message' => 'Model muvaffaqiyatli ochirildi'
        ]);
    }
}
