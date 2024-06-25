<?php

namespace App\Http\Controllers;

use App\Models\Giver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GiverController extends Controller
{
    public function list(Request $request){
        try{
            $giver = Giver::with([
                'company:id,name'
            ])->orderBy('id', 'desc')->select('id', 'name', 'phone_number', 'nid', 'location', 'company_id');
            $giver = $giver->paginate($request->per_page);
            $giver = $giver->appends($request->all());
            $data['giver'] = $giver;
            return response()->json([
                'success' => true,
                'message' => "Giver List",
                'data' => $data
            ], 200);
        }
        catch (\Exception $e){
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 200);
        }
    }
    public function add(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'company_id' => 'required',
                'name' => 'required',
                'phone_number' => 'required|numeric|digits:11',
                'giver_code' => 'required|unique:givers',
                'location' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'success' => 401,
                    'message' => "Validation Error.",
                    'error' => $validator->errors(),
                ], 401);
            }
            $data = Giver::create([
                'company_id' => $request->company_id,
                'name' => $request->name,
                'phone_number' => $request->phone_number,
                'giver_code' => $request->giver_code,
                'nid' => $request->nid,
                'location' => $request->location,
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Giver Created Successfully',
                'data' => $data,
            ], 200);
        }
        catch (\Exception $e){
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 200);
        }
    }
    public function edit(Request $request){
        try{
            $data['giver'] = Giver::with([
                'company:id,name,company_code'
            ])->select('id', 'company_id', 'name', 'phone_number', 'giver_code', 'nid', 'location')->find($request->id);
            return response()->json([
                'success' => true,
                'message' => 'Single Giver Info',
                'data' => $data,
            ], 200);
        }
        catch (\Exception $e){
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 200);
        }
    }
    public function update(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'company_id' => 'required',
                'name' => 'required',
                'phone_number' => 'required|numeric|digits:11',
                'location' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'success' => 401,
                    'message' => "Validation Error.",
                    'error' => $validator->errors(),
                ], 401);
            }
            $data = Giver::where('id', $request->id)->update([
                'company_id' => $request->company_id,
                'name' => $request->name,
                'phone_number' => $request->phone_number,
                'nid' => $request->nid,
                'location' => $request->location,
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Giver Updated Successfully',
                'data' => $data,
            ], 200);
        }
        catch (\Exception $e){
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 200);
        }
    }
}
