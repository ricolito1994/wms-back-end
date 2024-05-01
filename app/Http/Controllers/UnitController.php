<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
            DB::beginTransaction();
            $unit = Unit::create($request->all());
            DB::commit();
            return response()->json([
                'message' => "Success create unit", 
                'success' => true,
                "data" => $unit,
            ], 200);
        } catch(Exception $e) {
            //DB::rollback();
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
                $unit = Unit::find($unitId)->get();
                return response()->json([
                    'result' => $unit,
                    'success' => true,
                ], 200);
            } else {
                $searchBy = $request->input();
                $unit = null;
                if (isset($searchBy['sort_key']) && $searchBy['sort_key'] !== 'All') {
                    $unit = Unit::where([
                        $searchBy['sort_key'] => $searchBy['searchValue']
                        ])
                        ->paginate(5);
                } else {
                    $unit = Unit::paginate(5);
                }
                return response()->json([
                    'result' => $unit,
                    'success' => true,
                ], 200);
            }
        }catch(Exception $e){
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
            $unit = Unit::updateOrCreate($request, ['id', $unitId]);
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
