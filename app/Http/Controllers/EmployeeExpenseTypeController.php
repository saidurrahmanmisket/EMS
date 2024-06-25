<?php

namespace App\Http\Controllers;

use App\Models\EmployeeExpenseType;
use App\Traits\DataArrayTraits;
use App\Traits\UploadTraits;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EmployeeExpenseTypeController extends Controller
{
    use UploadTraits;
    use DataArrayTraits;
    public function list(Request $request){
        try {
            $employee_expense_type=EmployeeExpenseType::with([
                'company:id,name',
                'category:id,name',
                'employee_expense_category:id,name'
            ])->orderby('id', 'desc')->select('id', 'name', 'company_id', 'category_id', 'employee_expense_category_id');

            $employee_expense_type = $employee_expense_type->paginate($request->per_page);
            $employee_expense_type=$employee_expense_type->appends($request->all());
            $data['employee_expense_type'] = $employee_expense_type;

            return response()->json([
                'success' => true,
                'message' => "Employee Expense Type List",
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
                'employee_expense_category_id' => 'required',
                'name' => 'required',
                'employee_expense_type_code' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'success' => 401,
                    'message' => "Validation Error.",
                    'error' => $validator->errors(),
                ], 401);
            }
            $data = EmployeeExpenseType::create([
                'company_id' => $request->company_id,
                'category_id' => $request->category_id,
                'expense_category_id' => $request->expense_category_id,
                'expense_sector_id' => $request->expense_sector_id,
                'employee_expense_category_id' => $request->employee_expense_category_id,
                'name' => $request->name,
                'employee_expense_type_code' => $request->employee_expense_type_code,
            ]);
            return response()->json([
                'status' => true,
                'message' => "Employee Expense Type created successfully",
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
            $employee_expense_type = EmployeeExpenseType::with([
                'company:id,name',
                'category:id,name',
                'expense_category:id,name',
                'employee_expense_category:id,name'
            ])->find($request->id);
            $data['employee_expense_type'] = $employee_expense_type;
            $expense_sector = $this->expenseSectorArray(true);
            $data['expense_sector'] = $expense_sector[$employee_expense_type->expense_sector_id];
            return response()->json([
                'success' => true,
                'message' => "Single Employee Expense Type Info",
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
                'employee_expense_category_id' => 'required',
                'name' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'success' => 401,
                    'message' => "Validation Error.",
                    'error' => $validator->errors(),
                ], 401);
            }
            $data = EmployeeExpenseType::where('id', $request->id)->update([
                'company_id' => $request->company_id,
                'category_id' => $request->category_id,
                'expense_category_id' => $request->expense_category_id,
                'expense_sector_id' => $request->expense_sector_id,
                'employee_expense_category_id' => $request->employee_expense_category_id,
                'name' => $request->name,
            ]);
            return response()->json([
                'success' => true,
                'message' => "Employee Expense Type Updated Successfully",
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

    public function getEmployeeExpenseTypeListByArray(Request $request){
        try{
            $employee_expense_types = $this->employeeExpenseTypeArray();
            return response()->json([
                'success' => true,
                'message' => "Employee Expense Type List",
                'data' => $employee_expense_types
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
