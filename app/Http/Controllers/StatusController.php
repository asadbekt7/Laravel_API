<?php

namespace App\Http\Controllers;

use App\Models\Statusmodel;
use Illuminate\Http\Request;

class StatusController extends Controller
{
    public function index()
    {
        $status = Statusmodel::all();
        return response()->json([
            'success' => true,
            'data' => $status
        ]);
    }
    public function show($id)
    {
        $status = Statusmodel::find($id);
        if (!$status) {
            return response()->json([
                'success' => false,
                'message' => 'Status with id ' . $id . ' cannot be found'
            ], 404);
        }
        return response()->json([
            'success' => true,
            'data' => $status
        ]);
    }
}
