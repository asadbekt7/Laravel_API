<?php

namespace App\Http\Controllers;

use App\Models\Networkmodel;
use App\Models\Statusmodel;
use Illuminate\Http\Request;

class NetworkController extends Controller
{
    public function index()
    {
        $network = Networkmodel::all();
        return response()->json([
            'success' => true,
            'data' => $network
        ]);
    }
    public function show($id)
    {
        $status = Statusmodel::find($id);
        if(!$status){
            return response()->json([
                'success' => false,
                'message' => 'Sorry, Network with id ' . $id . ' cannot be found'
            ]);
        }
        return response()->json([
            'success' => true,
            'data' => $status
        ]);
    }
}
