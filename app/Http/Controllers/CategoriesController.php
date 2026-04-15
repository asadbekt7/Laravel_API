<?php

namespace App\Http\Controllers;

use App\Models\Categoriesmodel;
use Illuminate\Http\Request;

class CategoriesController extends Controller
{
    public function index()
    {
        $categories = Categoriesmodel::select ('id', 'name')->get();
        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }
    public function show($id)
    {
        $categories = Categoriesmodel::find($id);
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
