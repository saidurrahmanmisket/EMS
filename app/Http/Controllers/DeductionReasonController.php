<?php

namespace App\Http\Controllers;

use App\Models\DeductionReason;
use App\Traits\DataArrayTraits;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DeductionReasonController extends Controller
{
    use DataArrayTraits;
    public function list(Request $request){
        try{
            $deduction_reason = DeductionReason::with([
                'company:id,name',
                'category:id,name',
                'expense_category:id,name',
                'expense_sector:id,name'
            ])->orderBy('id', 'desc')->select('id', 'deduction_reason', 'deduction_reason_code', 'company_id', 'category_id', 'expense_category_id', 'expense_sector_id');
            $deduction_reason = $deduction_reason->paginate($request->per_page);
            $deduction_reason = $deduction_reason->appends($request->all());
            $data['deduction_reason'] = $deduction_reason;
            return response()->json([
                'success' => true,
                'message' => "Deduction Reason List",
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
                'category_id' => 'required',
                'expense_category_id' => 'required',
                'expense_sector_id' => 'required',
                'deduction_reason' => 'required',
                'deduction_reason_code' => 'required|unique:deduction_reasons',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'success' => 401,
                    'message' => "Validation Error.",
                    'error' => $validator->errors(),
                ], 401);
            }
            $data = DeductionReason::create([
                'company_id' => $request->company_id,
                'category_id' => $request->category_id,
                'expense_category_id' => $request->expense_category_id,
                'expense_sector_id' => $request->expense_sector_id,
                'deduction_reason' => $request->deduction_reason,
                'deduction_reason_code' => $request->deduction_reason_code,
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Deduction Reason Created Successfully',
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
            $data['deduction_reason'] = DeductionReason::with([
                'company:id,name',
                'category:id,name',
                'expense_category:id,name',
                'expense_sector:id,name'
            ])->select('id', 'deduction_reason', 'deduction_reason_code', 'company_id', 'category_id', 'expense_category_id', 'expense_sector_id')->find($request->id);
            return response()->json([
                'success' => true,
                'message' => 'Single Deduction Reason Info',
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
                'category_id' => 'required',
                'expense_category_id' => 'required',
                'expense_sector_id' => 'required',
                'deduction_reason' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'success' => 401,
                    'message' => "Validation Error.",
                    'error' => $validator->errors(),
                ], 401);
            }
            $data = DeductionReason::where('id', $request->id)->update([
                'company_id' => $request->company_id,
                'category_id' => $request->category_id,
                'expense_category_id' => $request->expense_category_id,
                'expense_sector_id' => $request->expense_sector_id,
                'deduction_reason' => $request->deduction_reason,
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Deduction Reason Updated Successfully',
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
    public function getDeductionReasonListByArray(Request $request){
        try{
            $deductionReasons = $this->deductionReasonArray();
            return response()->json([
                'success' => true,
                'message' => "Deduction Reason List",
                'data' => $deductionReasons
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
