<?php

namespace App\Http\Controllers;

use App\Models\PaymentClientGiver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PaymentClientGiverController extends Controller
{
    public function list(Request $request){
        try{
            $payment_client_giver = PaymentClientGiver::with([
                'company:id,name'
            ])->orderBy('id', 'desc')->select('id', 'name', 'phone_number', 'nid', 'location', 'company_id');
            $payment_client_giver = $payment_client_giver->paginate($request->per_page);
            $payment_client_giver = $payment_client_giver->appends($request->all());
            $data['payment_client_giver'] = $payment_client_giver;
            return response()->json([
                'success' => true,
                'message' => "Payment Client Giver List",
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
                'payment_client_giver_code' => 'required|unique:payment_client_givers',
                'location' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'success' => 401,
                    'message' => "Validation Error.",
                    'error' => $validator->errors(),
                ], 401);
            }
            $data = PaymentClientGiver::create([
                'company_id' => $request->company_id,
                'name' => $request->name,
                'phone_number' => $request->phone_number,
                'payment_client_giver_code' => $request->payment_client_giver_code,
                'nid' => $request->nid,
                'location' => $request->location,
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Payment Client Giver Created Successfully',
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
            $data['payment_client_giver'] = PaymentClientGiver::with([
                'company:id,name,company_code'
            ])->select('id', 'company_id', 'name', 'phone_number', 'payment_client_giver_code', 'nid', 'location')->find($request->id);
            return response()->json([
                'success' => true,
                'message' => 'Single Payment Client Giver Info',
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
            $data = PaymentClientGiver::where('id', $request->id)->update([
                'company_id' => $request->company_id,
                'name' => $request->name,
                'phone_number' => $request->phone_number,
                'nid' => $request->nid,
                'location' => $request->location,
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Payment Client Giver Updated Successfully',
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

    public function getPaymentClientGiverList()
    {
        try {

            $paymentClientGiver = PaymentClientGiver::select('id','name','phone_number')->orderBy('id', 'desc')->get();

            return response()->json([
                'success' => true,
                'message' => "Payment Client Giver List",
                'data' => $paymentClientGiver
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
