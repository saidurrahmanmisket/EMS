<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UnitController extends Controller
{
    public function list(Request $request){
        try {
            $unit=Unit::orderby('id', 'desc')->select('id', 'name');
            $unit = $unit->paginate($request->per_page);
            $unit=$unit->appends($request->all());
            $data['unit'] = $unit;
            return response()->json([
                'success' => true,
                'message' => "Unit List",
                'data' => $data
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 200);
        }
    }
    public function add(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'success' => 401,
                    'message' => "Validation Error.",
                    'error' => $validator->errors(),
                ], 401);
            }
            $data = Unit::create([
                'name' => $request->name,
            ]);
            return response()->json([
                'success' => true,
                'message' => "Unit created successfully",
                'data' => $data
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 200);
        }
    }
    public function edit(Request $request){
        try{
            $data = Unit::find($request->id);
            if(!$data){
                return response()->json([
                    'success' => false,
                    'message' => 'No Information Found With Id '.$request->id,
                ], 200);
            }
            return response()->json([
                'success' => true,
                'message' => "Single Unit Info",
                'data' => $data
            ], 200);
        }
        catch (\Exception $e){
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 200);
        }
    }
    public function update(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'name' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'success' => 401,
                    'message' => "Validation Error.",
                    'error' => $validator->errors(),
                ], 401);
            }
            $data = Unit::where('id', $request->id)->update([
                'name' => $request->name,
            ]);
            return response()->json([
                'success' => true,
                'message' => "Unit Updated Successfully",
                'data' => $data
            ], 200);
        }
        catch (\Exception $e){
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 200);
        }
    }
    public function getUnitList(Request $request){
        try{
            $data = Unit::select('id', 'name')->get();
            return response()->json([
                'success' => true,
                'message' => "Unit List",
                'data' => $data
            ], 200);
        }
        catch (\Exception $e){
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 200);
        }
    }
}
