<?php

namespace App\Http\Controllers;

use App\Models\PaymentReceiver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PaymentReceiverController extends Controller
{
    public function list(Request $request){
        try{
            $payment_receiver = PaymentReceiver::with([
                'company:id,name',
                'branch:id,name',
                'category:id,name'
            ])->orderBy('id', 'desc')->select('id', 'name', 'phone_number', 'nid', 'location', 'company_id', 'branch_id', 'category_id');
            $payment_receiver = $payment_receiver->paginate($request->per_page);
            $payment_receiver = $payment_receiver->appends($request->all());
            $data['payment_receiver'] = $payment_receiver;
            return response()->json([
                'success' => true,
                'message' => "Payment Receiver List",
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
                'name' => 'required',
                'phone_number' => 'required|numeric|digits:11',
                'payment_receiver_code' => 'required|unique:payment_receivers',
                'location' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'success' => 401,
                    'message' => "Validation Error.",
                    'error' => $validator->errors(),
                ], 401);
            }
            $data = PaymentReceiver::create([
                'company_id' => $request->company_id,
                'branch_id' => $request->branch_id,
                'category_id' => $request->category_id,
                'name' => $request->name,
                'phone_number' => $request->phone_number,
                'payment_receiver_code' => $request->payment_receiver_code,
                'nid' => $request->nid,
                'location' => $request->location,
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Payment Receiver Created Successfully',
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
            $data['payment_receiver'] = PaymentReceiver::with([
                'company:id,name,company_code',
                'branch:id,name',
                'category:id,name'
            ])->select('id', 'company_id', 'branch_id', 'category_id', 'name', 'phone_number', 'payment_receiver_code', 'nid', 'location')->find($request->id);
            return response()->json([
                'success' => true,
                'message' => 'Single Payment Receiver Info',
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
            $data = PaymentReceiver::where('id', $request->id)->update([
                'company_id' => $request->company_id,
                'branch_id' => $request->branch_id,
                'category_id' => $request->category_id,
                'name' => $request->name,
                'phone_number' => $request->phone_number,
                'nid' => $request->nid,
                'location' => $request->location,
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Payment Receiver Updated Successfully',
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
    public function paymentReceiverList()
    {
        try {

            $paymentReceiver = PaymentReceiver::select('id','name','phone_number')->orderBy('id', 'desc')->get();

            return response()->json([
                'success' => true,
                'message' => "Payment Receiver List",
                'data' => $paymentReceiver
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
