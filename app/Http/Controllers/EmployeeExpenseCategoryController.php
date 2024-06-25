<?php

namespace App\Http\Controllers;

use App\Models\EmployeeExpenseCategory;
use App\Traits\DataArrayTraits;
use App\Traits\UploadTraits;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EmployeeExpenseCategoryController extends Controller
{
    use UploadTraits;
    use DataArrayTraits;

    public function list(Request $request){
        try {
            $employee_expense_category=EmployeeExpenseCategory::with([
                'company:id,name'
            ])->orderby('id', 'desc')->select('id', 'name', 'company_id');

            $employee_expense_category = $employee_expense_category->paginate($request->per_page);
            $employee_expense_category=$employee_expense_category->appends($request->all());
            $data['employee_expense_category'] = $employee_expense_category;

            return response()->json([
                'success' => true,
                'message' => "Employee Expense Category List",
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
                'expense_sector_id' => 'required',
                'name' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'success' => 401,
                    'message' => "Validation Error.",
                    'error' => $validator->errors(),
                ], 401);
            }
            $data = EmployeeExpenseCategory::create([
                'company_id' => $request->company_id,
                'category_id' => $request->category_id,
                'expense_category_id' => $request->expense_category_id,
                'expense_sector_id' => $request->expense_sector_id,
                'name' => $request->name,
            ]);
            return response()->json([
                'status' => true,
                'message' => "Employee Expense Category created successfully",
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
             $employee_expense_category= EmployeeExpenseCategory::with([
                'company:id,name',
                'category:id,name',
                'expense_category:id,name',
            ])->find($request->id);
            $data['employee_expense_category'] = $employee_expense_category;
             $expense_sector = $this->expenseSectorArray(true);
             $data['expense_sector'] = $expense_sector[$employee_expense_category->expense_sector_id];
            return response()->json([
                'success' => true,
                'message' => "Single Employee Expense Category Info",
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
                'expense_sector_id' => 'required',
                'name' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'success' => 401,
                    'message' => "Validation Error.",
                    'error' => $validator->errors(),
                ], 401);
            }
            $data = EmployeeExpenseCategory::where('id', $request->id)->update([
                'company_id' => $request->company_id,
                'category_id' => $request->category_id,
                'expense_category_id' => $request->expense_category_id,
                'expense_sector_id' => $request->expense_sector_id,
                'name' => $request->name,
            ]);
            return response()->json([
                'success' => true,
                'message' => "Employee Expense Category Updated Successfully",
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
    public function getEmployeeExpenseCategoryListByExpenseSector(Request $request){
        try{
            $data = EmployeeExpenseCategory::where('expense_sector_id', $request->expense_sector_id)->select('id', 'name')->get();
            return response()->json([
                'success' => true,
                'message' => "Employee Expense Category List By Expense Sector",
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

    public function getEmployeeExpenseCategoryListByArray(Request $request){
        try{
            $employee_expense_categories = $this->employeeExpenseCategoryArray();
            return response()->json([
                'success' => true,
                'message' => "Employee Expense Category List",
                'data' => $employee_expense_categories
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
