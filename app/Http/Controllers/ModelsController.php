<?php

namespace App\Http\Controllers;

use App\Models\Itemmodel;
use App\Models\Categorymodel;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ModelsController extends Controller
{
    public function index()
    {
        $models = Itemmodel::with('category:id,name')->get();

        return response()->json([
            'success' => true,
            'data'    => $models
        ]);
    }
    public function show($id)
    {
        $models = Itemmodel::with('category:id,name')->find($id);

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
                'category_id' => 'required|exists:categories,id',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors'  => $e->errors()
            ], 422);
        }

        $models = Itemmodel::create([
            'name'          => $request->name,
            'category_id' => $request->category_id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Model has been created',
            'data'    => $models
        ], 201);
    }
    public function update(Request $request, $id)
    {
        $models = Itemmodel::find($id);

        if (!$models) {
            return response()->json([
                'success' => false,
                'message' => 'Model topilmadi'
            ], 404);
        }

        try {
            $request->validate([
                'name'          => 'sometimes|string|max:255',
                'category_id' => 'sometimes|exists:categories,id',
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
        $models = Itemmodel::find($id);

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
