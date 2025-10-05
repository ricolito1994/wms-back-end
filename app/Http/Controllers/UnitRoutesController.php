<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Arr;
use App\Models\UnitRouteTemplate;
use App\Models\UnitRoute;

class UnitRoutesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $filter = $request->all();
            $result = UnitRouteTemplate::with([
                "unitRoutes" =>  function ($query) {
                    $query->select ('id', 'unit_route_template_id', 'lat', 'lng');
                }
            ])
            -> filter($filter)
            -> paginate(6);
            return response()->json([
                "data" => $result,
                "success" => true,
            ], 200);
        } catch (\Exception $e) {
            return response()->json(
                [
                    "message" => "Failed",
                    "success" => false,
                ],
            500);
        }
    }

    public function save (Request $request) 
    {
        try {
            DB::beginTransaction();
            
            $req = $request->all()['data'];

            $unitRouteTemplateFillable = (new UnitRouteTemplate)->getFillable();
            $unitRouteTemplateRequest = Arr::only($req, $unitRouteTemplateFillable);
            $unitRoutesRequest = Arr::except($req, $unitRouteTemplateFillable);

            $unitRouteTemplate = UnitRouteTemplate::create($unitRouteTemplateRequest);

            if (! empty($unitRoutesRequest))
                $unitRouteTemplate->unitRoutes()->createMany($unitRoutesRequest['locations']);

            DB::commit();
            return response()->json([
                "message" => "Unit routes saved.",
                "success" => true
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                "message" => "Something went wrong ". $e->getMessage(),
                "success" => false
            ], 500);
        }
    }


    public function show (int $unitRouteTemplateID) 
    {
        try {
            $routeTemplate = UnitRouteTemplate::with("unitRoutes")
                ->findOrFail($unitRouteTemplateID);

            return response()->json([
                'data' => $routeTemplate,
                'success' => true,
            ], 200);
        } catch (\Exception $e) {
             return response()->json([
                "message" => "Something went wrong ". $e->getMessage(),
                "success" => false
            ], 500);
        }
    }

    public function patch (UnitRouteTemplate $route, Request $request) 
    {
        return response()->json([], 200);
    }

    public function delete (UnitRouteTemplate $route) 
    {
        return response()->json([], 200);
    }
}