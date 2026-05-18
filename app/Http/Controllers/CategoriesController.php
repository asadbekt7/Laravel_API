<?php

namespace App\Http\Controllers;

use App\Models\CategoryModel;
use Illuminate\Http\Request;

class CategoriesController extends Controller
{
    public function index()
    {
        $categories = CategoryModel::select ('id', 'name')->get();
        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }
    public function show($id)
    {
        $categories = CategoryModel::find($id);
        if (!$categories) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, Category with id ' . $id . ' cannot be found'
            ], 404);
        }
        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }
}
