<?php

namespace App\Http\Controllers;

use DB;
use Exception;
use App\Models\Month;
use Nette\Utils\Json;
use App\Models\Employee;
use App\Models\SalaryMonth;
use App\Traits\UploadTraits;
use Illuminate\Http\Request;
use App\Models\ExpenseRental;
use App\Models\ExpenseProduct;
use App\Models\ExpenseUtility;
use Illuminate\Support\Carbon;
use App\Models\ExpenseEmployee;
use Illuminate\Support\Facades\Log;
use App\Models\ExpenseProductDetail;
use Illuminate\Support\Facades\File;
use App\Models\EmployeeOfficialDetail;
use App\Models\ExpenseEmployeeAdvance;
use App\Models\ExpenseEmployeeRegular;
use Illuminate\Support\Facades\Validator;
use App\Models\ExpenseInformationRelatedToEmployee;

class ExpenseController extends Controller
{
    use UploadTraits;
    public function addExpense(Request $request)
    {
        // dd("error");
        // return response()->json("error ok");
        try {
//            if ($request->expense_category_id == 1 || $request->expense_category_id == 2 || $request->expense_category_id == 3) {
                if ($request->expense_sector_id == 1) {
                    return  $this->addProductExpense($request);
                } elseif ($request->expense_sector_id == 2) {
                    return  $this->addEmployeeExpense($request);
                } elseif ($request->expense_sector_id == 3) {
                    return  $this->addRentalExpense($request);
                }elseif ($request->expense_sector_id == 4) {
                    return  $this->addExpenseUtility($request);
                }else {

                    return response()->json([
                        'success' => false,
                        'message' => "Expense Sector Id is not Matched",
                    ], 200);
                }
//            } else {
//                return response()->json([
//                    'success' => false,
//                    'message' => "Expense Category Id is Not Matched sdf3453453",
//                    //                'data' => $data
//                ], 200);
//            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 200);
        }
    }
    public function addProductExpense(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required',
            'company_id' => 'required',
            'branch_id' => 'required',
            'product_expense_code' => 'required|unique:expense_products',
            'expense_category_id' => 'required',
            'expense_sector_id' => 'required',
            'shop_id' => 'required',
            'purchaser_id' => 'required',
            //                    'document_image' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => 401,
                'message' => "Validation Error.",
                'error' => $validator->errors(),
            ], 401);
        }
        $document_image = $request->document_image;
        if ($document_image !== 'null') {
            $file_type = $document_image->getClientOriginalExtension();
            if ($file_type != "jpg" && $file_type != "png" && $file_type != "jpeg" && $file_type != "svg") {
                $document_image = $this->uploadFile($document_image, 'expense/product_expense');
            } else {
                $document_image = $this->uploadImage($document_image, 'expense/product_expense');
            }
        }
        $data['expense_product_expense'] = ExpenseProduct::create([
            'date' => $request->date,
            'company_id' => $request->company_id,
            'branch_id' => $request->branch_id,
            'product_expense_code' => $request->product_expense_code,
            'expense_category_id' => $request->expense_category_id,
            'expense_sector_id' => $request->expense_sector_id,
            'shop_id' => $request->shop_id,
            'purchaser_id' => $request->purchaser_id,
            'document_image' => $document_image,
        ]);
        $product_expense = $data['expense_product_expense'];
        if (is_array($request->product_detail)) {
            foreach ($request->product_detail as  $productDetailData) {
                $product_detail = array(
                    'expense_product_expense_id' => $product_expense->id,
                    'product_id' => $productDetailData['product_id'],
                    'product_details' => $productDetailData['product_details'],
                    'per_unit_price' => $productDetailData['per_unit_price'],
                    'total_unit' => $productDetailData['total_unit'],
                    'total_unit_price' => $productDetailData['total_unit_price'],
                );
                $data['expense_product_detail'] = ExpenseProductDetail::create($product_detail);
            }
        }
        return response()->json([
            'success' => true,
            'message' => 'Product Expense Created Successfully',
            'data' => $data,
        ], 200);
    }

    // Expense Employee
    public function addEmployeeExpense(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'date' => 'required',
            'company_id' => 'required',
            'branch_id' => 'required',
            'expense_category_id' => 'required',
            'expense_sector_id' => 'required',
            'employee_expense_category_id' => 'required',
            'employee_expense_type_id' => 'required',
            'employee_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => "Validation Error.",
                'error' => $validator->errors(),
            ], 200);
        }

        $employee_id = $request->employee_id;

        $Employee = Employee::find($employee_id);

        if (!$Employee) {
            return response()->json([
                'success' => false,
                'message' => "Not Find Employee With Id " . $employee_id,
            ], 200);
        }

        $employee_expense_type_id = $request->employee_expense_type_id;

        if ($employee_expense_type_id == 1 || $employee_expense_type_id == 2) {

            if ($employee_expense_type_id == 1) {
                return $this->addEmployeeExpenseAdvance($request);
            } elseif ($employee_expense_type_id == 2) {
                return $this->addEmployeeExpenseRegular($request);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => "Validation Error.",
                    'error' => $validator->errors(),
                ], 200);
            }
        }
    }

    public function addEmployeeExpenseAdvance(Request $request)
    {


        $validator = Validator::make($request->all(), [
            'advance_amount_given' => 'required',

        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => "Validation Error.",
                'error' => $validator->errors(),
            ], 200);
        }

        $date = $request->date;
        $advance_amount_given = $request->advance_amount_given;
        $remarks = $request->remarks;
        $company_id = $request->company_id;
        $branch_id = $request->branch_id;
        $employee_expense_code = $request->employee_expense_code;
        $expense_category_id = $request->expense_category_id;
        $expense_sector_id = $request->expense_sector_id;
        $employee_expense_category_id = $request->employee_expense_category_id;
        $employee_expense_type_id = $request->employee_expense_type_id;
        $employee_id = $request->employee_id;


        if ($advance_amount_given > 0) {
            $ExpenseEmployee = ExpenseEmployee::where('employee_id', $employee_id)->first();
            if (!$ExpenseEmployee) {

                ExpenseEmployee::create([
                    'total_advance_amount_given' => $advance_amount_given,
                    'employee_id' => $employee_id,
                ]);
            } else {
                $total_advance_amount_given = $ExpenseEmployee->total_advance_amount_given;
                $total_advance_amount_given = $total_advance_amount_given + $advance_amount_given;
                $ExpenseEmployee->total_advance_amount_given = $total_advance_amount_given;
                $ExpenseEmployee->save();
            }
        }

        ExpenseEmployee::create([
            'date' => $date,
            'company_id' => $company_id,
            'branch_id' => $branch_id,
            'employee_expense_code' => $employee_expense_code,
            'expense_category_id' => $expense_category_id,
            'expense_sector_id' => $expense_sector_id,
            'employee_expense_category_id' => $employee_expense_category_id,
            'employee_expense_type_id' => $employee_expense_type_id,
            'employee_id' => $employee_id,
            'advance_amount_given' => $advance_amount_given,
            'remarks' => $remarks
        ]);


        return response()->json([
            'success' => true,
            'message' => 'Employee Expense is Created for Advance Successfully',
        ], 200);
    }

    public function addEmployeeExpenseRegular(Request $request)
    {


        $validator = Validator::make($request->all(), [
            'employee_expense_code' => 'required|unique:expense_employee_regulars',
            'salary_month_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => "Validation Error.",
                'error' => $validator->errors(),
            ], 200);
        }

        $date = $request->date;
        $remarks = $request->remarks;
        $company_id = $request->company_id;
        $branch_id = $request->branch_id;
        $employee_expense_code = $request->employee_expense_code;
        $expense_category_id = $request->expense_category_id;
        $expense_sector_id = $request->expense_sector_id;
        $employee_expense_category_id = $request->employee_expense_category_id;
        $employee_expense_type_id = $request->employee_expense_type_id;
        $employee_id = $request->employee_id;
        $deduction_amount = $request->deduction_amount;
        $deduction_reason_id = $request->deduction_reason_id;
        $salary_month_id = $request->salary_month_id;


        $EmployeeOfficialDetail = EmployeeOfficialDetail::where('employee_id', $employee_id)->first();

        $salary_of_employee = $EmployeeOfficialDetail->salary;

        if ($deduction_amount <= $salary_of_employee) {

            if ($deduction_reason_id == 4) {
                $ExpenseEmployee = ExpenseEmployee::where('employee_id', $employee_id)->first();
                $total_advance_amount_given = $ExpenseEmployee->total_advance_amount_given;
                if ($total_advance_amount_given > 0) {

                    if ($deduction_amount <= $total_advance_amount_given) {
                        $total_advance_amount_given = $total_advance_amount_given - $deduction_amount;
                        $ExpenseEmployee->total_advance_amount_given = $total_advance_amount_given;
                        $ExpenseEmployee->save();
                    } else {
                        return response()->json([
                            'success' => false,
                            'message' => "Deduction amount is getter than total advance amount given for this employee with id " . $employee_id,
                        ], 200);
                    }
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => "total advance amount given is 0 or less than 0 for this employee with id " . $employee_id,
                    ], 200);
                }
            }

            $will_get_total = $salary_of_employee - $deduction_amount;

            $now = Carbon::now();

            ExpenseEmployee::create([
                'date' => $date,
                'company_id' => $company_id,
                'branch_id' => $branch_id,
                'employee_expense_code' => $employee_expense_code,
                'expense_category_id' => $expense_category_id,
                'expense_sector_id' => $expense_sector_id,
                'employee_expense_category_id' => $employee_expense_category_id,
                'employee_expense_type_id' => $employee_expense_type_id,
                'employee_id' => $employee_id,
                'deduction_reason_id' => $deduction_reason_id,
                'salary_month_id' => $salary_month_id,
                'year' => $now->year,
                'salary_amount' => $salary_of_employee,
                'deduction_amount' => $deduction_amount,
                'will_get_total' => $will_get_total,
                'remarks' => $remarks,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Employee Expense is Created for Regular Successfully',
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => "Deduction amount is getter than salary amount of employee for this employee with id " . $employee_id,
            ], 200);
        }
    }
    public function totalAdvanceAmount(Request $request)
    {
        try {
            $data = [];
            $employee_id = $request->employee_id;

            $ExpenseEmployee = ExpenseEmployee::where('employee_id', $employee_id)->first();

            if ($ExpenseEmployee) {
                $data['total_advance_amount_given'] = $ExpenseEmployee->total_advance_amount_given;

                return response()->json([
                    'success' => true,
                    'message' => "Total Advance Amount Given",
                    'data' => $data
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => "No ExpenseEmployee found for the given employee_id",
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 200);
        }
    }

    public function getMonthlySalaryOfEmployee(Request $request)
    {
        try {

            $data = [];

            $employee_id = $request->employee_id;

            $EmployeeOfficialDetail = EmployeeOfficialDetail::where('employee_id', $employee_id)->first();

            $salary_of_employee = $EmployeeOfficialDetail->salary;

            $data['salary_of_employee'] = $salary_of_employee;


            return response()->json([
                'success' => true,
                'message' => "Salary of Employee",
                'data' => $data
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 200);
        }
    }
    public function months_list(Request $request)
    {
        try {

            $months = SalaryMonth::select('id', 'month_name')->get();

            return response()->json([
                'success' => true,
                'message' => "Months list",
                'data' => $months
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 200);
        }
    }

    //Expense Rental
    public function addRentalExpense(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required',
            'company_id' => 'required',
            'branch_id' => 'required',
            'rental_expense_code' => 'required|unique:expense_rentals',
            'expense_category_id' => 'required',
            'expense_sector_id' => 'required',
            'rental_space_id' => 'required',
            'rental_expense_type_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => 401,
                'message' => 'Validation Error.',
                'error' => $validator->errors(),
            ], 401);
        }

        $document_image = $request->document_image;

        if ($document_image !== 'null') {
            $file_type = $document_image->getClientOriginalExtension();
            if ($file_type != 'jpg' && $file_type != 'png' && $file_type != 'jpeg' && $file_type != 'svg') {
                $document_image = $this->uploadFile($document_image, 'expense/rental_expense');
            } else {
                $document_image = $this->uploadImage($document_image, 'expense/rental_expense');
            }
        }

        // Rental expense Advance type
        if ($request->rental_expense_type_id == 1) {
            $total_advance = $request->total_advance;
            $advance_given = $request->advance_given;

            // Get total advance given
            $totalAdvanceGiven = ExpenseRental::where('company_id', $request->company_id)
                ->where('branch_id', $request->branch_id)
                ->where('rental_expense_type_id', $request->rental_expense_type_id)
                ->where('expense_sector_id', $request->expense_sector_id)
                ->where('rental_space_id', $request->rental_space_id)
                ->sum('advance_given');

            $totalAdvanceGiven += $advance_given;

            if ($advance_given > $total_advance) {
                return response()->json(['error' => 'Invalid advance_given amount'], 400);
            }

            $advance_remain = $total_advance - $totalAdvanceGiven;
            if ($advance_remain < 0) {
                return response()->json(['error' => 'Invalid advance_remain amount.'], 400);
            }
            $data['expense_rental_expense'] = ExpenseRental::create([
                'date' => $request->date,
                'company_id' => $request->company_id,
                'branch_id' => $request->branch_id,
                'rental_expense_code' => $request->rental_expense_code,
                'rental_expense_type_code' => $request->rental_expense_type_code,
                'expense_category_id' => $request->expense_category_id,
                'expense_sector_id' => $request->expense_sector_id,
                'rental_space_id' => $request->rental_space_id,
                'rental_expense_type_id' => $request->rental_expense_type_id,
                'document_image' => $document_image,
                'remarks' => $request->remarks,
                'advance_given_date' => $request->advance_given_date,
                'total_advance' => $total_advance,
                'advance_given' => $advance_given,
                'advance_remain' => max(0, $advance_remain)

            ]);
        } elseif ($request->rental_expense_type_id == 2) {
            $data['expense_rental_expense'] = ExpenseRental::create([
                'date' => $request->date,
                'company_id' => $request->company_id,
                'branch_id' => $request->branch_id,
                'rental_expense_code' => $request->rental_expense_code,
                'rental_expense_type_code' => $request->rental_expense_type_code,
                'expense_category_id' => $request->expense_category_id,
                'expense_sector_id' => $request->expense_sector_id,
                'rental_space_id' => $request->rental_space_id,
                'rental_expense_type_id' => $request->rental_expense_type_id,
                'month_id' => $request->month_id,
                'document_image' => $document_image,
                'remarks' => $request->remarks,
                'total_advance' => $request->total_advance,
                'regular_given' => $request->regular_given,
            ]);
        }
        return response()->json([
            'success' => true,
            'message' => 'Rental Expense Created Successfully',
            'data' => $data,
        ], 200);
    }

    public function totalAdvance(Request $request){
        try {
            $data = [];

            $rental_expense_type_id = $request->rental_expense_type_id;
            $rental_space_id = $request->rental_space_id;

            $ExpenseRental = ExpenseRental::where('rental_space_id', $rental_space_id)
                    ->where('rental_expense_type_id', $rental_expense_type_id)->latest('created_at')
                    ->first();

            if ($ExpenseRental) {
                $data['total_advance'] = $ExpenseRental->total_advance  ;
                $data['advance_remain'] = $ExpenseRental->advance_remain ?? 0;
            } else {
                $data['total_advance'] = 0 ;
                $data['advance_remain'] =  0;
                return response()->json([
                    'success' => false,
                    'data' => $data,
                    'message' => "Record not found for rental_space_id: $rental_space_id and rental_expense_type_id: $rental_expense_type_id",
                ], 200);
            }

            return response()->json([
                'success' => true,
                'message' => "Total Advance Amount",
                'data' => $data
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    public function months(Request $request)
    {
        try {

            $months =Month::select('id', 'month_name')->get();

            return response()->json([
                'success' => true,
                'message' => "Months list",
                'data' => $months
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 200);
        }
    }

    //Expense Utility
    public function addExpenseUtility(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required',
            'company_id' => 'required',
            'branch_id' => 'required',
            'utility_expense_code' => 'required|unique:expense_utilities',
            'manage_utility_billing_sector_informations_id'=> 'required',
            'expense_category_id' => 'required',
            'expense_sector_id' => 'required',
            'rental_space_id' => 'required',
            'manage_utility_sector_id' => 'required',
            // 'manage_billing_type_id' => 'required',
            'billing_amount' => 'required',
            'cylinder_price'=> 'required',

        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => 401,
                'message' => "Validation Error.",
                'error' => $validator->errors(),
            ], 401);
        }
        $document_image = $request->document_image;
        if ($document_image !== 'null') {
            $file_type = $document_image->getClientOriginalExtension();
            if ($file_type != "jpg" && $file_type != "png" && $file_type != "jpeg" && $file_type != "svg") {
                $document_image = $this->uploadFile($document_image, 'expense/utilities_expense');
            } else {
                $document_image = $this->uploadImage($document_image, 'expense/utilities_expense');
            }
        }

        DB::beginTransaction();

        try {
            $manage_utility_sector_id = $request->manage_utility_sector_id;

            $store_array =[
                'date' => $request->date,
                'company_id' => $request->company_id,
                'branch_id' => $request->branch_id,
                'utility_expense_code' => $request->utility_expense_code,
                'expense_category_id' => $request->expense_category_id,
                'expense_sector_id' => $request->expense_sector_id,
                'rental_space_id' => $request->rental_space_id,
                'manage_utility_sector_id' => $request->manage_utility_sector_id,
                // 'billing_sector_code' => $request->billing_sector_code,
                 'manage_utility_billing_sector_informations_id' => $request->manage_utility_billing_sector_informations_id,
                'month_id' => $request->month_id,
                'document_image' => $document_image,
                'remarks' => $request->remarks,

            ];
                       //1=Electricity bill,2=Water and sewage bill,3=Phone bill,4=Internet bill,5=Titas gas bill
            if ($manage_utility_sector_id == 1 || $manage_utility_sector_id == 2 || $manage_utility_sector_id == 3 || $manage_utility_sector_id == 4 || $manage_utility_sector_id == 5) {

                $billing_amount = $request->billing_amount;

                $validator = Validator::make($request->all(), [
                    'billing_amount' => 'required',
                ]);

                if ($validator->fails()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Validation failed',
                        'errors' => $validator->errors(),
                    ], 200);
                }


                $store_array += ['billing_amount'=>$billing_amount];
            }
                            //6=Gas cylinder bill
            elseif ($manage_utility_sector_id == 6) {
                $cylinder_price = $request->cylinder_price;
                $cylinder_purchase_date = $request->cylinder_purchase_date;
                $validator = Validator::make($request->all(), [
                    'cylinder_price' => 'required',
                    'cylinder_purchase_date'=> 'required'
                ]);
                if ($validator->fails()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Validation failed',
                        'errors' => $validator->errors(),
                    ], 200);
                }
                $store_array += ['cylinder_price' => $cylinder_price,'cylinder_purchase_date' => $cylinder_purchase_date];
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'If you wish to include the Utility Sector, please contact the Utility Billing Sector.',
                ], 200);
            }
            $expenseUtility = ExpenseUtility::create($store_array);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Utility Expense Created Successfully',
                'data' => $store_array,
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

//expense list start
    public function expenseList(Request $request)
    {
        try {
            $expenseSectorId = $request->expense_sector_id;
            $companyId = $request->company_id;
            $branchId = $request->branch_id;
            $dateStartFrom = $request->date_start_form;
            $dateEndTo = $request->date_end_To;
            $employeeTypeId = $request->employee_type_id; // 1 for regular and 2 for Advance

            if ($expenseSectorId == 1) {  // 1 is product expense
                return $this->expenseProductList($dateStartFrom, $dateEndTo, $companyId, $branchId, $expenseSectorId);
            } else if ($expenseSectorId == 2) { // 2 is employee expense
                return $this->expenseEmployeeList($employeeTypeId, $dateStartFrom, $dateEndTo, $companyId, $branchId, $expenseSectorId);
            } else if ($expenseSectorId == 3) { // 3 is rental expense
                return $this->expenseRentalList($dateStartFrom, $dateEndTo, $companyId, $branchId, $expenseSectorId);
            }else if ($expenseSectorId == 4){
                return $this->expenseUtilityList($dateStartFrom, $dateEndTo, $companyId, $branchId, $expenseSectorId);
            } else {
                return response()->json([
                    'success' => true,
                    'message' => "Expense sector Not Found",
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function expenseProductList($dateStartFrom, $dateEndTo, $companyId, $branchId, $expenseSectorId)
    {
        $query = ExpenseProduct::where('expense_sectors.id', '=', $expenseSectorId)
            ->select(
                'expense_products.id',
                'expense_products.date',
                'companies.name as company_name',
                'branches.name as branch_name',
                'expense_products.product_expense_code as expense_code',
                'expense_categories.name as expense_category_name',
                'expense_products.expense_category_id as expense_category_id',
                'expense_sectors.name as expense_sector_name',
                'expense_products.expense_sector_id',
                'shops.name as shop_name',

            )
            ->leftJoin('companies', 'companies.id', '=', 'expense_products.company_id')
            ->leftJoin('branches', 'branches.id', '=', 'expense_products.branch_id')
            ->leftJoin('expense_categories', 'expense_categories.id', '=', 'expense_products.expense_category_id')
            ->leftJoin('expense_sectors', 'expense_sectors.id', '=', 'expense_products.expense_sector_id')
            ->leftJoin('shops', 'shops.id', '=', 'expense_products.shop_id')
            ->orderBy('date', 'DESC');

        if ($dateStartFrom && $dateEndTo) {
            $query->whereBetween('expense_products.date', [$dateStartFrom, $dateEndTo]);
        }
        if ($dateStartFrom) {
            $query->where('expense_products.date', '>=', $dateStartFrom);
        }
        if ($dateEndTo) {
            $query->where('expense_products.date', '<=', $dateEndTo);
        }
        if ($companyId) {
            $query->where('companies.id', '=', $companyId);
        }
        if ($branchId) {
            $query->where('branches.id', '=', $branchId);
        }


        $results = $query->get();
        if ($results->count() == 0) {
            return response()->json([
                'success' => false,
                'message' => "No data found",
            ], 200);
        }
        return response()->json([
            'success' => true,
            'message' => "Product expense list fetch success",
            'data'=> $results
        ], 200);
    }

    public function expenseEmployeeList($employeeTypeId, $dateStartFrom, $dateEndTo, $companyId, $branchId, $expenseSectorId)
    {
        try {

            $query = ExpenseEmployee::where('expense_sector_id', '=', $expenseSectorId)
                ->where('employee_expense_type_id', $employeeTypeId)
                ->select(
                    'expense_employees.id',
                    'expense_employees.date',
                    'companies.name as company_name',
                    'branches.name as branch_name',
                    'expense_employees.employee_expense_code as expense_id',
                    'expense_categories.name as expense_category_name',
                    'expense_employees.employee_expense_category_id as expense_category_id',
                    'expense_sectors.name as expense_sector_name',
                    'expense_employees.expense_sector_id',
                    'employees.name as employee_name',
                    'expense_employees.employee_expense_type_id'
                )
                ->leftJoin('companies', 'companies.id', '=', 'expense_employees.company_id')
                ->leftJoin('branches', 'branches.id', '=', 'expense_employees.branch_id')
                ->leftJoin('expense_categories', 'expense_categories.id', '=', 'expense_employees.expense_category_id')
                ->leftJoin('expense_sectors', 'expense_sectors.id', '=', 'expense_employees.expense_sector_id')
                ->leftJoin('employees', 'employees.id', '=', 'expense_employees.employee_id')
                ->orderBy('date', 'DESC');

            if ($dateStartFrom && $dateEndTo) {
                $query->whereBetween('expense_employees.date', [$dateStartFrom, $dateEndTo]);
            }
            if ($dateStartFrom) {
                $query->where('expense_employees.date', '>=', $dateStartFrom);
            }
            if ($dateEndTo) {
                $query->where('expense_employees.date', '<=', $dateEndTo);
            }
            if ($companyId) {
                $query->where('companies.id', '=', $companyId);
            }
            if ($branchId) {
                $query->where('branches.id', '=', $branchId);
            }

            $results = $query->get();
            if ($results->count() == 0) {
                return response()->json([
                    'success' => false,
                    'message' => "No data found",
                ], 200);
            }
            return response()->json([
                'success' => true,
                'message' => "Expense Employee Regular list fetch success",
                'data' => $results
            ], 200);
        }catch (\Exception $e){
            return response()->json([
                'success' => false,
                'message' => "Something went wrong",
                'error' => $e->getMessage()
            ], 200);
        }
    }

    public function expenseRentalList($dateStartFrom, $dateEndTo, $companyId, $branchId, $expenseSectorId)
    {
        $query = ExpenseRental::where('expense_sectors.id', '=', $expenseSectorId)
            ->select(
                'expense_rentals.id',
                'expense_rentals.date',
                'companies.name as company_name',
                'branches.name as branch_name',
                'expense_rentals.rental_expense_code as expense_code',
                'expense_categories.name as expense_category_name',
                'expense_rentals.expense_category_id as expense_category_id',
                'expense_sectors.name as expense_sector_name',
                'expense_rentals.expense_sector_id',
                'months.month_name as rental_given_month_name',

            )
            ->leftJoin('companies', 'companies.id', '=', 'expense_rentals.company_id')
            ->leftJoin('branches', 'branches.id', '=', 'expense_rentals.branch_id')
            ->leftJoin('expense_categories', 'expense_categories.id', '=', 'expense_rentals.expense_category_id')
            ->leftJoin('expense_sectors', 'expense_sectors.id', '=', 'expense_rentals.expense_sector_id')
            ->leftJoin('months', 'months.id', '=', 'expense_rentals.month_id')
            ->orderBy('date', 'DESC');

        if ($dateStartFrom && $dateEndTo) {
            $query->whereBetween('expense_rentals.date', [$dateStartFrom, $dateEndTo]);
        }
        if ($dateStartFrom) {
            $query->where('expense_rentals.date', '>=', $dateStartFrom);
        }
        if ($dateEndTo) {
            $query->where('expense_rentals.date', '<=', $dateEndTo);
        }
        if ($companyId) {
            $query->where('companies.id', '=', $companyId);
        }
        if ($branchId) {
            $query->where('branches.id', '=', $branchId);
        }


        $results = $query->get();
        if ($results->count() == 0) {
            return response()->json([
                'success' => false,
                'message' => "No data found",
            ], 200);
        }
        return response()->json([
            'success' => true,
            'message' => "Expense rentals list fetch success",
            'data'=> $results
        ], 200);
    }

    public function expenseUtilityList($dateStartFrom, $dateEndTo, $companyId, $branchId, $expenseSectorId)
    {
        try {


            $query = ExpenseUtility::where('expense_utilities.expense_sector_id', '=', $expenseSectorId)
                ->select(
                    'expense_utilities.id',
                    'expense_utilities.date',
                    'expense_utilities.utility_expense_code',
                    'companies.name as company_name',
                    'branches.name as branch_name',
                    'expense_categories.name as expense_category_name',
                    'rental_spaces.rental_space_name as 	rental_space_name',
                    'expense_rentals.id as expense_rentals_id',
                    'rental_space_owners.owner_name as rental_space_owner_name',
                    'manage_utility_sectors.utility_billing_sector_name as utility_billing_sector_name',
                    'manage_utility_sectors.billing_sector_code as billing_sector_code',
                    'manage_utility_billing_sector_informations.id as meter_number',
                    'manage_billing_types.billing_type_name as billing_type_name',
                    'manage_utility_billing_sector_informations.customer_id_number as customer_id_number',
                    'manage_utility_billing_sector_informations.phone_bill_number as phone_bill_number',
                    'manage_utility_billing_sector_informations.isp_name as isp_name',
                    'manage_utility_billing_sector_informations.customer_id_number_code as customer_id_number_code',
                    'expense_utilities.billing_amount as billing_amount',
                )
                ->leftJoin('companies', 'companies.id', '=', 'expense_utilities.company_id')
                ->leftJoin('branches', 'branches.id', '=', 'expense_utilities.branch_id')
                ->leftJoin('expense_categories', 'expense_categories.id', '=', 'expense_utilities.expense_category_id')
                ->leftJoin('expense_sectors', 'expense_sectors.id', '=', 'expense_utilities.expense_sector_id')
                ->leftJoin('rental_spaces', 'rental_spaces.id', '=', 'expense_utilities.rental_space_id')
                ->leftJoin('manage_utility_sectors', 'manage_utility_sectors.id', '=', 'expense_utilities.manage_utility_sector_id')
                ->leftJoin('manage_utility_billing_sector_informations', 'manage_utility_billing_sector_informations.id', '=', 'expense_utilities.manage_utility_billing_sector_informations_id')
                ->leftJoin('rental_space_owners', 'rental_space_owners.rental_space_id', '=', 'rental_spaces.id')
                ->leftJoin('expense_rentals', 'expense_rentals.id', '=', 'expense_utilities.rental_space_id')
                ->leftJoin('manage_billing_types', 'manage_billing_types.id', '=', 'manage_utility_billing_sector_informations.manage_billing_type_id')
                ->orderBy('date', 'DESC');

            if ($dateStartFrom && $dateEndTo) {
                $query->whereBetween('expense_utilities.date', [$dateStartFrom, $dateEndTo]);
            }
            if ($dateStartFrom) {
                $query->where('expense_utilities.date', '>=', $dateStartFrom);
            }
            if ($dateEndTo) {
                $query->where('expense_utilities.date', '<=', $dateEndTo);
            }
            if ($companyId) {
                $query->where('companies.id', '=', $companyId);
            }
            if ($branchId) {
                $query->where('branches.id', '=', $branchId);
            }


            $results = $query->get();
            if ($results->count() == 0) {
                return response()->json([
                    'success' => false,
                    'message' => "No data found",
                ], 200);
            }
            return response()->json([
                'success' => true,
                'message' => "Utilities expense list fetch success",
                'data' => $results
            ], 200);
        }catch (\Exception $e){
            return response()->json([
                'success' => false,
                'message' => "Something went wrong",
                'error' => $e->getMessage()
            ], 500);
        }

    }
//expense list end

//expense list view start
    public function expenseListView(Request $request)
    {
        $expenseSectorId = $request->expense_sector_id;
        $expenseId = $request->id;
        $employeeTypeId = $request->employee_type_id; // 1 for regular and 2 for Advance

        if ($expenseSectorId == 1) { //1 for product expense
            return $this->expenseProductListView($expenseId);
        } elseif ($expenseSectorId == 2) {//2 for employee expense
            return $this->expenseEmployeeListView($expenseId, $employeeTypeId);
        } elseif ($expenseSectorId == 3) {//3 for rental expense
            return $this->expenseRentalListView($expenseId);
        }elseif ($expenseSectorId == 4) {//3 for rental expense
            return $this->expenseUtilityListView($expenseId);
        }
        else {
            return response()->json([
                'success' => true,
                'message' => "Expense sector Not Found",
            ], 200);
        }
    }

    public function expenseProductListView($expenseId)
    {
        $query = ExpenseProduct::where('expense_products.id', '=', $expenseId)
            ->select(
                'expense_products.id',
                'expense_products.date',
                'companies.name as company_name',
                'expense_products.product_expense_code as expense_code',
                'companies.id as company_id',
                'branches.name as branch_name',
                'expense_categories.name as expense_category_name',
                'expense_products.expense_category_id as expense_category_id',
                'expense_sectors.name as expense_sector_name',
                'expense_products.expense_sector_id',
                'shops.name as shop_name',
                'shops.id as shop_id',
                'manage_purchasers.name as purchaser_name',
                'manage_purchasers.phone_number as purchaser_phone_no'


            )
            ->leftJoin('companies', 'companies.id', '=', 'expense_products.company_id')
            ->leftJoin('branches', 'branches.id', '=', 'expense_products.branch_id')
            ->leftJoin('expense_categories', 'expense_categories.id', '=', 'expense_products.expense_category_id')
            ->leftJoin('expense_sectors', 'expense_sectors.id', '=', 'expense_products.expense_sector_id')
            ->leftJoin('shops', 'shops.id', '=', 'expense_products.shop_id')
            ->leftJoin('manage_purchasers', 'manage_purchasers.id', '=', 'expense_products.purchaser_id')
            ->first();

        if (!$query) {
            return response()->json([
                'success' => false,
                'message' => "No data found",
            ], 200);
        }
        $query["expense_product_details"] = ExpenseProductDetail::where('expense_product_expense_id', '=', $expenseId)
            ->select(
                'expense_product_details.id',
                'products.name as product_name',
                'products.id as product_id',
                'expense_product_details.product_details',
                'units.name as unit',
                'expense_product_details.per_unit_price',
                'expense_product_details.total_unit',
                'expense_product_details.total_unit_price'
            )
            ->leftJoin('products', 'products.id', '=', 'expense_product_details.product_id')
            ->leftJoin('units', 'units.id', '=', 'products.unit_id')
            ->get();

        return response()->json([
            'success' => true,
            'message' => "Expense Product list view fetch success",
            'data'=> $query
        ], 200);

    }

    public function expenseEmployeeListView($expenseId, $employeeTypeId)
    {
        try {

            $query = ExpenseEmployee::where('expense_employees.id', '=', $expenseId)
                ->select(
                    'expense_employees.id',
                    'expense_employees.date',
                    'companies.name as company_name',
                    'companies.id as company_id',
                    'branches.name as branch_name',
                    'expense_employees.employee_expense_code as expense_id',
                    'employee_expense_categories.name as expense_category_name',
                    'expense_employees.employee_expense_category_id as expense_category_id',
                    'expense_sectors.name as expense_sector_name',
                    'expense_employees.expense_sector_id',
                    'employee_expense_categories.name as employee_expense_category',
                    'employee_expense_categories.id as employee_expense_category_id',
                    'employee_expense_types.name as employee_expense_type',
                    'employee_expense_types.id as employee_expense_type_id',
                    'designations.name as designations',
                    'expense_employees.remarks as remarks',
                    'employees.name as employee_name',
                    'employees.id as employee_id',
                    'salary_months.month_name as month_name',
                    'expense_employees.salary_amount as total_amount',
                    'expense_employees.deduction_amount as deduction_amount',
                    'deduction_reasons.deduction_reason as reason',
                    'expense_employees.will_get_total as will_get_total',
                    'expense_employees.advance_amount_given',
                )
                    ->leftJoin('companies', 'companies.id', '=', 'expense_employees.company_id')
                    ->leftJoin('branches', 'branches.id', '=', 'expense_employees.branch_id')
                    ->leftJoin('employee_expense_categories', 'employee_expense_categories.id', '=', 'expense_employees.employee_expense_category_id')
                    ->leftJoin('expense_sectors', 'expense_sectors.id', '=', 'expense_employees.expense_sector_id')
                    ->leftJoin('employees', 'employees.id', '=', 'expense_employees.employee_id')
                    ->leftJoin('employee_expense_types', 'employee_expense_types.id', '=', 'expense_employees.employee_expense_type_id')
                    ->leftJoin('employee_official_details', 'employee_official_details.employee_id', '=', 'employees.id')
                    ->leftJoin('designations', 'designations.id', '=', 'employee_official_details.designation_id')
                    ->leftJoin('salary_months', 'salary_months.id', '=', 'expense_employees.salary_month_id')
                    ->leftJoin('deduction_reasons', 'deduction_reasons.id', '=', 'expense_employees.deduction_reason_id')
                    ->first();


            if (!$query) {
                return response()->json([
                    'success' => false,
                    'message' => "No data found",
                ], 200);
            }
            return response()->json([
                'success' => true,
                'message' => "Expense Employee Regular list view fetch success",
                'data'=> $query
            ], 200);

        } catch (\Exception $error) {
            return response()->json([
                'success' => false,
                'message' => "Something went wrong",
                "error"=> $error->getMessage()
            ], 500);
        }
    }

    public function expenseRentalListView($expenseId)
    {
        try {

            $query = ExpenseRental::where('expense_rentals.id', '=', $expenseId)
                ->select(
                    'expense_rentals.id',
                    'expense_rentals.date',
                    'companies.name as company_name',
                    'companies.id as company_id',
                    'companies.company_code as company_code',
                    'branches.name as branch_name',
                    'expense_rentals.rental_expense_code as expense_code',
                    'expense_categories.name as expense_category_name',
                    'expense_rentals.expense_category_id as expense_category_id',
                    'expense_sectors.name as expense_sector_name',
                    'expense_rentals.expense_sector_id',
                    'rental_expense_types.rental_type_name as rental_expense_types',
                    'rental_expense_types.id as rental_expense_types_id',
                    'expense_rentals.advance_given',
                    'expense_rentals.total_advance',
                    'expense_rentals.advance_remain',
                    'expense_rentals.regular_given',
                    'expense_rentals.advance_given_date',
                    'expense_rentals.document_image',
                    'expense_rentals.remarks',
                    'months.month_name as rental_given_month_name',

                )
                ->leftJoin('companies', 'companies.id', '=', 'expense_rentals.company_id')
                ->leftJoin('branches', 'branches.id', '=', 'expense_rentals.branch_id')
                ->leftJoin('expense_categories', 'expense_categories.id', '=', 'expense_rentals.expense_category_id')
                ->leftJoin('expense_sectors', 'expense_sectors.id', '=', 'expense_rentals.expense_sector_id')
                ->leftJoin('months', 'months.id', '=', 'expense_rentals.month_id')
                ->leftJoin('rental_expense_types', 'rental_expense_types.id', '=', 'expense_rentals.rental_expense_type_id')
                ->first();


            if (!$query) {
                return response()->json([
                    'success' => false,
                    'message' => "No data found",
                ], 200);
            }
            return response()->json([
                'success' => true,
                'message' => "Expense Employee Rentals list view fetch success",
                'data'=> $query
            ], 200);
        } catch (\Exception $error) {
            return response()->json([
                'success' => false,
                'message' => "Something went wrong",
                "error"=> $error->getMessage()
            ], 500);
        }

    }
    public function expenseUtilityListView($expenseId)
    {
        try {


            $query = ExpenseUtility::where('expense_utilities.id', '=', $expenseId)
                ->select(
                    'expense_utilities.id',
                    'expense_utilities.date',
                    'companies.name as company_name',
                    'companies.id as company_id',
                    'branches.name as branch_name',
                    'expense_utilities.utility_expense_code',
                    'expense_categories.name as expense_category_name',
                    'expense_categories.id as expense_category_id',
                    'expense_sectors.name as expense_sector_name',
                    'expense_utilities.expense_sector_id',
                    'rental_spaces.rental_space_name as rental_space_name',
                    'expense_rentals.id as expense_rentals_id',
                    'rental_space_owners.owner_name as rental_space_owner_name',
                    'manage_utility_billing_sector_informations.phone_bill_number as phone_bill_number',
                    'rental_space_cities.name as rental_space_cities_location',
                    'manage_utility_sectors.billing_sector_code as utility_billing_sector_code',
                    'manage_utility_sectors.utility_billing_sector_name as utility_billing_sector_name',
                    'manage_billing_types.billing_type_name as billing_type_name',
                    'manage_utility_billing_sector_informations.isp_name as isp_name',
                    'expense_utilities.billing_amount',
//                    'manage_utility_billing_sector_informations.id as isp_id',
                    'months.month_name as utility_given_month_name',
                    'expense_utilities.document_image',
                    'expense_utilities.remarks',

                )
                ->leftJoin('companies', 'companies.id', '=', 'expense_utilities.company_id')
                ->leftJoin('branches', 'branches.id', '=', 'expense_utilities.branch_id')
                ->leftJoin('expense_categories', 'expense_categories.id', '=', 'expense_utilities.expense_category_id')
                ->leftJoin('expense_sectors', 'expense_sectors.id', '=', 'expense_utilities.expense_sector_id')
                ->leftJoin('rental_spaces', 'rental_spaces.id', '=', 'expense_utilities.rental_space_id')
                ->leftJoin('manage_utility_sectors', 'manage_utility_sectors.id', '=', 'expense_utilities.manage_utility_sector_id')
                ->leftJoin('manage_utility_billing_sector_informations', 'manage_utility_billing_sector_informations.id', '=', 'expense_utilities.manage_utility_billing_sector_informations_id')
                ->leftJoin('rental_space_owners', 'rental_space_owners.rental_space_id', '=', 'rental_spaces.id')
                ->leftJoin('expense_rentals', 'expense_rentals.id', '=', 'expense_utilities.rental_space_id')
                ->leftJoin('manage_billing_types', 'manage_billing_types.id', '=', 'manage_utility_billing_sector_informations.manage_billing_type_id')
                ->leftJoin('rental_space_cities', 'rental_space_cities.id', '=', 'rental_spaces.rental_space_city_id')
                ->leftJoin('months', 'months.id', '=', 'expense_rentals.month_id')
                ->orderBy('date', 'DESC');



//                ->leftJoin('rental_space_owners', 'rental_space_owners.rental_space_id', '=', 'expense_utilities.rental_space_id')
//                ->leftJoin('manage_billing_types', 'manage_billing_types.id', '=', 'expense_utilities.manage_billing_type_id')



            $results = $query->get();
            if ($results->count() == 0) {
                return response()->json([
                    'success' => false,
                    'message' => "No data found",
                ], 200);
            }
            return response()->json([
                'success' => true,
                'message' => "Utilities expense list fetch success",
                'data' => $results
            ], 200);
        }catch (\Exception $e){
            return response()->json([
                'success' => false,
                'message' => "Something went wrong",
                'error' => $e->getMessage()
            ], 500);
        }
    }
//expense list view end
}

