<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use App\Models\City;
use App\Models\Purok;
use App\Models\Barangay;
use App\Models\Address;

class LandmarksController extends Controller
{
    //
    public function show (Request $request, string $type) 
    {
        try {
            switch ($type) {
                case "city":
                    $response = City::filter($request)
                        ->orderBy('id')
                        ->paginate(6);
                    break;
                case "purok":
                    $response = Purok::filter($request)
                        ->with(['city', 'barangay'])
                        ->orderBy('id')
                        ->paginate(6);
                    break;
                case "barangay":
                    $response = Barangay::filter($request)
                        ->with(['city', 'purok'])
                        ->orderBy('id', 'DESC')
                        ->paginate(6);
                    break;
                case "address":
                    $response = Address::filter($request)
                        ->with([
                            'city', 
                            'purok', 
                            'barangay', 
                            'street'
                        ])
                        ->orderBy('id')
                        ->paginate(6);
                    break;
            }
            return response()->json([
                'data' => $response,
                'success' => true,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'err' => $e,
                'success' => false,
            ], 500);
        }
    }

    public function get (int $landmarkId, string $type) 
    {
        try {
            switch ($type) {
                case "city":
                    $response = City::findOrFail($landmarkId);
                    break;
                case "purok":
                    $response = Purok::with(['city', 'barangay'])
                        ->findOrFail($landmarkId);
                    break;
                case "barangay":
                    $response = Barangay::with(['city', 'purok'])
                        ->findOrFail($landmarkId);
                    break;
                case "address":
                    $response = Address::findOrFail($landmarkId);
                    break;
            }
            return response()->json([
                'data' => $response,
                'success' => true,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'err' => $e,
                'success' => false,
            ], 500);
        }
    }

    public function store (Request $request, string $type) 
    {
        try {
            DB::beginTransaction();
            switch ($type) {
                case "city":
                    $response = City::create($request->all());
                    break;
                case "purok":
                    $response = Purok::create($request->all());
                    break;
                case "barangay":
                    $response = Barangay::create($request->all());
                    break;
                case "address":
                    $response = Address::create($request->all());
                    break;
            }
            DB::commit();
            return response()->json([
                'data' => $response,
                'success' => true,
            ], 200);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json([
                'err' => $e,
                'success' => false,
            ], 500);
        }
    }

    public function update (
        Request $request,  
        int $landmarkId,
        string $type
    ) 
    {
        try {
            DB::beginTransaction();
            switch ($type) {
                case "city":
                    $response = City::findOrFail($landmarkId)
                        ->update($request->all());
                    break;
                case "purok":
                    $response = Purok::findOrFail($landmarkId)
                        ->update($request->all());
                    break;
                case "barangay":
                    $response = Barangay::findOrFail($landmarkId)
                        ->update($request->all());
                    break;
                case "address":
                    $response = Address::findOrFail($landmarkId)
                        ->update($request->all());
                    break;
            }
            DB::commit();
            return response()->json([
                'data' => $response,
                'success' => true,
            ], 200);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json([
                'err' => $e,
                'success' => false,
            ], 500);
        }
    }

    public function delete (int $landmarkId, string $type) 
    {
        try {
            switch ($type) {
                case "city":
                    $response = City::findOrFail($landmarkId)
                        ->delete();
                    break;
                case "purok":
                    $response = Purok::findOrFail($landmarkId)
                        ->delete();
                    break;
                case "barangay":
                    $response = Barangay::findOrFail($landmarkId)
                        ->delete();
                    break;
                case "address":
                    $response = Address::findOrFail($landmarkId)
                        ->delete();
                    break;
            }
            return response()->json([
                'data' => $response,
                'success' => true,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'err' => $e,
                'success' => false,
            ], 500);
        }
    }
 
}
