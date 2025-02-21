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
            $response = null;
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

    public function all (Request $request, string $type) 
    {
        try {
            switch ($type) {
                case "city":
                    $response = City::filter($request)
                        ->orderBy('id')
                        ->get();
                    break;
                case "purok":
                    $response = Purok::filter($request)
                        ->with(['city', 'barangay'])
                        ->orderBy('id')
                        ->get();
                    break;
                case "barangay":
                    $response = Barangay::filter($request)
                        ->with(['city', 'purok'])
                        ->orderBy('id', 'DESC')
                        ->get();
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
                        ->get();
                    break;
            }
            return response()->json([
                'data' => $response,
                'address_type' => $type,
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
                    $response = Address::with([
                        'city', 
                        'purok', 
                        'barangay', 
                        'street'
                    ])
                        ->findOrFail($landmarkId);
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
                    $response = tap(City::findOrFail($landmarkId))
                        ->update($request->all())
                        ->fresh();
                    break;
                case "purok":
                    $response = tap(Purok::findOrFail($landmarkId))
                        ->update($request->all())
                        ->fresh();
                    break;
                case "barangay":
                    $response = tap(Barangay::findOrFail($landmarkId))
                        ->update($request->all())
                        ->fresh();
                    break;
                case "address":
                    $response = tap(Address::findOrFail($landmarkId))
                        ->update($request->all())
                        ->fresh();
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

    public function getAllLandmarks (Request $request) 
    {
        try {
            if (! $request->has('place_name'))
                throw  new \Exception("place_name is undefined.");

            $place_name = $request->place_name;

            $barangay = DB::table('barangay')->select(DB::raw("CONCAT('barangay-',id) as id"), DB::raw("'barangay' as address_type"),'barangay_name as place_name','longitude','latitude')
                ->where('barangay_name', 'LIKE', "%{$place_name}%");
            $purok = DB::table('purok')->select(DB::raw("CONCAT('purok-',id) as id"), DB::raw("'purok' as address_type"),'purok_name as place_name','longitude','latitude')
                ->where('purok_name', 'LIKE', "%{$place_name}%");
            $address = DB::table('address')->select(DB::raw("CONCAT('address-',id) as id"), DB::raw("'address' as address_type"),'full_address as place_name','longitude','latitude')
                ->where('full_address', 'LIKE', "%{$place_name}%");
            
            $response = $address
                ->union($barangay)
                ->union($purok)
                ->orderBy('place_name')
                ->paginate(10);

            return response()->json([
                'success' => true,
                'data' => $response,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'err' => $e->getMessage(),
                'success' => false,
            ], 500);
        }
    }
 
}
