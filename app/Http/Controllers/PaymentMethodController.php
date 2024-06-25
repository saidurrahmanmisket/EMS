<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use App\Models\ReceiveMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PaymentMethodController extends Controller
{
    public function list(Request $request){
        try{
            $payment_method = PaymentMethod::with([
                'company:id,name',
                'branch:id,name',
                'category:id,name'
            ])->orderBy('id', 'desc')->select('id', 'payment_method', 'payment_method_code', 'company_id', 'branch_id', 'category_id');
            $payment_method = $payment_method->paginate($request->per_page);
            $payment_method = $payment_method->appends($request->all());
            $data['payment_method'] = $payment_method;
            return response()->json([
                'success' => true,
                'message' => "Payment Method List",
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
                'payment_method' => 'required',
                'payment_method_code' => 'required|unique:payment_methods',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'success' => 401,
                    'message' => "Validation Error.",
                    'error' => $validator->errors(),
                ], 401);
            }
            $data = PaymentMethod::create([
                'company_id' => $request->company_id,
                'branch_id' => $request->branch_id,
                'category_id' => $request->category_id,
                'payment_method' => $request->payment_method,
                'payment_method_code' => $request->payment_method_code,
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Payment Method Created Successfully',
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
            $data['payment_method'] = PaymentMethod::with([
                'company:id,name',
                'branch:id,name',
                'category:id,name'
            ])->select('id', 'company_id', 'branch_id', 'category_id', 'payment_method', 'payment_method_code')->find($request->id);
            return response()->json([
                'success' => true,
                'message' => 'Single Payment Method Info',
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
                'payment_method' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'success' => 401,
                    'message' => "Validation Error.",
                    'error' => $validator->errors(),
                ], 401);
            }
            $data = PaymentMethod::where('id', $request->id)->update([
                'company_id' => $request->company_id,
                'branch_id' => $request->branch_id,
                'category_id' => $request->category_id,
                'payment_method' => $request->payment_method,
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Payment Method Updated Successfully',
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
