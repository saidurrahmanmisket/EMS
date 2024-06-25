<?php

namespace App\Http\Controllers;

use App\Models\ExpenseSector;
use App\Traits\UploadTraits;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ExpenseSectorController extends Controller
{
    use UploadTraits;
    public function list(Request $request){
        try {
            $expense_sector=ExpenseSector::with([
                'company:id,name',
                'category:id,name',
                'expense_category:id,name'
            ])->orderby('id', 'desc')->select('id', 'name', 'expense_sector_code', 'company_id', 'category_id', 'expense_category_id');

            $expense_sector = $expense_sector->paginate($request->per_page);
            $expense_sector=$expense_sector->appends($request->all());
            $data['expense_sector'] = $expense_sector;

            return response()->json([
                'success' => true,
                'message' => "Expense Sector List",
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
                'company_id' => 'required',
                'category_id' => 'required',
                'expense_category_id' => 'required',
                'name' => 'required',
                'expense_sector_code' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'success' => 401,
                    'message' => "Validation Error.",
                    'error' => $validator->errors(),
                ], 401);
            }
            $data = ExpenseSector::create([
                'company_id' => $request->company_id,
                'category_id' => $request->category_id,
                'expense_category_id' => $request->expense_category_id,
                'name' => $request->name,
                'expense_sector_code' => $request->expense_sector_code,
            ]);
            return response()->json([
                'status' => true,
                'message' => "Expense Sector created successfully",
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
            $data = ExpenseSector::with([
                'company:id,name',
                'category:id,name',
                'expense_category:id,name'
            ])->find($request->id);
            return response()->json([
                'success' => true,
                'message' => "Single Expense Sector Info",
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
                'company_id' => 'required',
                'category_id' => 'required',
                'expense_category_id' => 'required',
                'name' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'success' => 401,
                    'message' => "Validation Error.",
                    'error' => $validator->errors(),
                ], 401);
            }
            $data = ExpenseSector::where('id', $request->id)->update([
                'company_id' => $request->company_id,
                'category_id' => $request->category_id,
                'expense_category_id' => $request->expense_category_id,
                'name' => $request->name,
            ]);
            return response()->json([
                'success' => true,
                'message' => "Expense Sector Updated Successfully",
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
    public function getExpenseSectorListByExpenseCategory(Request $request){
        try{
            $data = ExpenseSector::where('expense_category_id', $request->expense_category_id)->select('id', 'name')->get();
            return response()->json([
                'success' => true,
                'message' => "Expense Sector List By Expense Category",
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
    public function getExpenseSectorByArray(Request $request){
        try{
            $expense_sectors = $this->expenseSectorArray();
            return response()->json([
                'success' => true,
                'message' => "Expense Sector List",
                'data' => $expense_sectors
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
