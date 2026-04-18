<?php

namespace App\Http\Controllers;

use App\Models\Unitmodel;
use Illuminate\Http\Request;

class UnityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $unity = Unitmodel::all();
        return response()->json([
            'success' => true,
            'data' => $unity
        ]);
    }
    public function show($id)
    {
        $unity = Unitmodel::find($id);
        if (!$unity) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, unity with id ' . $id . ' cannot be found'
            ]);
        }
        return response()->json([
            'success' => true,
            'data' => $unity
        ]);
    }
}
