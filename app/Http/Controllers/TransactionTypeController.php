<?php

namespace App\Http\Controllers;

use App\Models\TransactionType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TransactionTypeController extends Controller
{
    public function list(Request $request){
        try{
            $transaction_type = TransactionType::with([
                'company:id,name',
                'branch:id,name',
                'category:id,name',
                'receive_method:id,received_method'
            ])->orderBy('id', 'desc')->select('id', 'company_id', 'branch_id', 'category_id', 'receive_method_id', 'transaction_type', 'transaction_type_code');
            $transaction_type = $transaction_type->paginate($request->per_page);
            $transaction_type = $transaction_type->appends($request->all());
            $data['transaction_type'] = $transaction_type;
            return response()->json([
                'success' => true,
                'message' => "Transaction Type List",
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
                'receive_method_id' => 'required',
                'transaction_type' => 'required',
                'transaction_type_code' => 'required|unique:transaction_types',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'success' => 401,
                    'message' => "Validation Error.",
                    'error' => $validator->errors(),
                ], 401);
            }
            $data = TransactionType::create([
                'company_id' => $request->company_id,
                'branch_id' => $request->branch_id,
                'category_id' => $request->category_id,
                'receive_method_id' => $request->receive_method_id,
                'transaction_type' => $request->transaction_type,
                'transaction_type_code' => $request->transaction_type_code,
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Transaction Type Created Successfully',
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
            $data['transaction_type'] = TransactionType::with([
                'company:id,name',
                'branch:id,name',
                'category:id,name',
                'receive_method:id,received_method'
            ])->select('id', 'company_id', 'branch_id', 'category_id', 'receive_method_id', 'transaction_type', 'transaction_type_code')->find($request->id);
            return response()->json([
                'success' => true,
                'message' => 'Single Transaction Type Info',
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
                'receive_method_id' => 'required',
                'transaction_type' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'success' => 401,
                    'message' => "Validation Error.",
                    'error' => $validator->errors(),
                ], 401);
            }
            $data = TransactionType::where('id', $request->id)->update([
                'company_id' => $request->company_id,
                'branch_id' => $request->branch_id,
                'category_id' => $request->category_id,
                'receive_method_id' => $request->receive_method_id,
                'transaction_type' => $request->transaction_type,
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Transaction Type Updated Successfully',
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
