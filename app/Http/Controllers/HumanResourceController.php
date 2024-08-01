<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class HumanResourceController extends Controller
{
    public function __construct(){}
    //
    public function index() 
    {   
        $request = request()->all();
        try {
            $userData = User::byFullname($request['users_name'])
                ->orderBy('created_at', 'DESC')
                ->paginate(6);
            return response()->json([
                'data' => $userData,
                'success' => true,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'err' => $e,
                'success' => false,
            ], 200);
        }
    }

    public function show($userId)
    {
        try {
            $userData = User::find($userId);
            return response()->json([
                'data' => $userData,
                'success' => true,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'err' => $e,
                'success' => false,
            ], 200);
        }
    }

    public function create(Request $request) 
    {
        try {
            $request = $request->all();
            DB::beginTransaction();
            $result = User::create($request);
            DB::commit();
            return response()->json([
                'data' => $result,
                'success' => true,
            ], 200);
        }  catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'err' => $e,
                'success' => false,
            ], 400);
        } 
    }


    public function update($userId) 
    {
        try {
            $request = request()->all();
            DB::beginTransaction();
            $result = User::updateOrCreate(['id' => $userId], $request);
            DB::commit();
            return response()->json([
                'data' => $result,
                'success' => true,
            ], 200);
        }  catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'err' => $e,
                'success' => false,
            ], 400);
        } 
    }

    public function createCrew () 
    {
    }
}
