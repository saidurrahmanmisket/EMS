<?php

namespace App\Http\Controllers;

use App\Models\Receiver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReceiverController extends Controller
{
    public function list(Request $request){
        try{
            $receiver = Receiver::with([
                'company:id,name'
            ])->orderBy('id', 'desc')->select('id', 'name', 'phone_number', 'nid', 'location', 'company_id');
            $receiver = $receiver->paginate($request->per_page);
            $receiver = $receiver->appends($request->all());
            $data['receiver'] = $receiver;
            return response()->json([
                'success' => true,
                'message' => "Receiver List",
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
                'receiver_code' => 'required|unique:receivers',
                'location' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'success' => 401,
                    'message' => "Validation Error.",
                    'error' => $validator->errors(),
                ], 401);
            }
            $data = Receiver::create([
                'company_id' => $request->company_id,
                'name' => $request->name,
                'phone_number' => $request->phone_number,
                'receiver_code' => $request->receiver_code,
                'nid' => $request->nid,
                'location' => $request->location,
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Receiver Created Successfully',
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
            $data['receiver'] = Receiver::with([
                'company:id,name,company_code'
            ])->select('id', 'company_id', 'name', 'phone_number', 'receiver_code', 'nid', 'location')->find($request->id);
            return response()->json([
                'success' => true,
                'message' => 'Single Receiver Info',
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
            $data = Receiver::where('id', $request->id)->update([
                'company_id' => $request->company_id,
                'name' => $request->name,
                'phone_number' => $request->phone_number,
                'nid' => $request->nid,
                'location' => $request->location,
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Receiver Updated Successfully',
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
