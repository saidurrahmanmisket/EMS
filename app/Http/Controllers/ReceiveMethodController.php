<?php

namespace App\Http\Controllers;

use App\Models\ReceiveMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReceiveMethodController extends Controller
{
    public function list(Request $request){
        try{
            $receive_method = ReceiveMethod::with([
                'company:id,name',
                'branch:id,name',
                'category:id,name'
            ])->orderBy('id', 'desc')->select('id', 'received_method', 'received_method_code', 'company_id', 'branch_id', 'category_id');
            $receive_method = $receive_method->paginate($request->per_page);
            $receive_method = $receive_method->appends($request->all());
            $data['receive_method'] = $receive_method;
            return response()->json([
                'success' => true,
                'message' => "Receive Method List",
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
                'branch_id' => 'required',
                'category_id' => 'required',
                'received_method' => 'required',
                'received_method_code' => 'required|unique:receive_methods',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'success' => 401,
                    'message' => "Validation Error.",
                    'error' => $validator->errors(),
                ], 401);
            }
            $data = ReceiveMethod::create([
                'company_id' => $request->company_id,
                'branch_id' => $request->branch_id,
                'category_id' => $request->category_id,
                'received_method' => $request->received_method,
                'received_method_code' => $request->received_method_code,
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Receive Method Created Successfully',
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
            $data['receive_method'] = ReceiveMethod::with([
                'company:id,name',
                'branch:id,name',
                'category:id,name'
            ])->select('id', 'company_id', 'branch_id', 'category_id', 'received_method', 'received_method_code')->find($request->id);
            return response()->json([
                'success' => true,
                'message' => 'Single Receive Method Info',
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
                'branch_id' => 'required',
                'category_id' => 'required',
                'received_method' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'success' => 401,
                    'message' => "Validation Error.",
                    'error' => $validator->errors(),
                ], 401);
            }
            $data = ReceiveMethod::where('id', $request->id)->update([
                'company_id' => $request->company_id,
                'branch_id' => $request->branch_id,
                'category_id' => $request->category_id,
                'received_method' => $request->received_method,
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Receive Method Updated Successfully',
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
    public function getReceiveMethodList(Request $request){
        try{
            $data['receive_method'] = ReceiveMethod::select('id', 'received_method')->get();
            return response()->json([
                'success' => true,
                'message' => "Received Method List",
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
