<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class UnitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        //
        try {
            $req = $request->all();
            DB::beginTransaction();
            $updteCondition = isset($req['id']) ? ['id' => $req['id']] : [];
            $unit = Unit::updateOrCreate($updteCondition, $req);
            DB::commit();
            return response()->json([
                'message' => "Success create unit", 
                'success' => true,
                "data" => $unit,
            ], 200);
        } catch(Exception $e) {
            DB::rollback();
            return response()->json([
                'message' => $e->getMessage(),
                'success' => false,
            ], 500);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(Request $request, $unitId = null)
    {   
        //
        try{
            if (isset($unitId)) {
                $unit = Unit::with(['crew' => function($q) {
                    $q->with('employee');
                }])->find($unitId);
                return response()->json([
                    'result' => $unit,
                    'success' => true,
                ], 200);
            } else {
                $searchBy = $request->all();
                $unit = null;
                if (
                    isset($searchBy['searchValue']) && 
                    $searchBy['sort_key'] !== 'all'
                ) {
                    $unit = Unit::where(
                        $searchBy['sort_key'], 
                        'like', 
                        "%{$searchBy['searchValue']}%"
                    )
                    ->with(['crew' => function($q) {
                        $q->with('employee');
                    }])
                    ->orderBy('id')
                    ->paginate(5);
                    return response()->json([
                        'result' => $unit,
                        'success' => true,
                    ], 200);
                } else {
                    $unit = Unit::with(['crew' => function($q) {
                        $q->with('employee');
                    }])->orderBy('id')->paginate(5);
                    return response()->json([
                        'result' => $unit,
                        'success' => true,
                    ], 200);
                }
            }
        } catch(Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'success' => false,
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $unitId)
    {
        //
        try {
            DB::beginTransaction();
            $request = $request->all();
            $unit = Unit::updateOrCreate(['id', $unitId], $request);
            DB::commit();
            return response()->json([
                'message' => "Update unit success", 
                'success' => true,
                "data" => $unit
            ], 200);
        } catch(Exception $e) {
            DB::rollback();
            return response()->json([
                'message' => $e->getMessage(),
                'success' => false,
            ], 500);
        }
    }   

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($unitId = null)
    {
        //
        try {
            DB::beginTransaction();
            $unit = Unit::delete($unitId);
            DB::commit();
            return response()->json([
                'message' => 'Deleted Successfully', 
                'success' => true
            ], 200);
        } catch(Exception $e) {
            DB::rollback();
            return response()->json([
                'message' => $e->getMessage(),
                'success' => false,
            ], 500);
        }
    }
}
