<?php

namespace App\Http\Controllers;

use App\Models\ExpenseCategory;
use App\Traits\DataArrayTraits;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ExpenseCategoryController extends Controller
{
    use DataArrayTraits;
    public function list(Request $request){
        try {
            $expense_category=ExpenseCategory::with([
                'company:id,name',
                'category:id,name'
            ])->orderby('id', 'desc')->select('id', 'name', 'expense_category_code', 'company_id', 'category_id');

            $expense_category = $expense_category->paginate($request->per_page);
            $expense_category=$expense_category->appends($request->all());
            $data['expense_category'] = $expense_category;

            return response()->json([
                'success' => true,
                'message' => "Expense Category List",
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
                'name' => 'required',
                'expense_category_code' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'success' => 401,
                    'message' => "Validation Error.",
                    'error' => $validator->errors(),
                ], 401);
            }
            $data = ExpenseCategory::create([
                'company_id' => $request->company_id,
                'category_id' => $request->category_id,
                'name' => $request->name,
                'expense_category_code' => $request->expense_category_code,
            ]);
            return response()->json([
                'status' => true,
                'message' => "Expense Category created successfully",
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
            $data = ExpenseCategory::with([
                'company:id,name',
                'category:id,name'
            ])->find($request->id);
            return response()->json([
                'success' => true,
                'message' => "Single Expense Category Info",
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
                'name' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'success' => 401,
                    'message' => "Validation Error.",
                    'error' => $validator->errors(),
                ], 401);
            }
            $data = ExpenseCategory::where('id', $request->id)->update([
                'company_id' => $request->company_id,
                'category_id' => $request->category_id,
                'name' => $request->name,
            ]);
            return response()->json([
                'success' => true,
                'message' => "Expense Category Updated Successfully",
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
    public function getExpenseCategoryListByCompanyAndCategory(Request $request){
        try{
            $data = ExpenseCategory::where('company_id', $request->company_id)->where('category_id', $request->category_id)->select('id', 'name')->get();
            return response()->json([
                'success' => true,
                'message' => "Expense Category List By Company and Category",
                'data' => $data
            ], 200);
        }
        catch (\Exception $e){
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 200);
        }
    }public function getExpenseCategoryListByCompany(Request $request){
        try{
            $data = ExpenseCategory::where('company_id', $request->company_id)->select('id', 'name')->get();
            return response()->json([
                'success' => true,
                'message' => "Expense Category List By Company",
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
    public function getExpenseCategoryListByArray(Request $request){
        try{
            $expense_categories = $this->expenseCategoryArray();
            return response()->json([
                'success' => true,
                'message' => "Expense Category List",
                'data' => $expense_categories
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
