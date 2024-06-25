<?php

namespace App\Http\Controllers;

use App\Models\ManageBillingType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ManageUtilitySector;
use Illuminate\Support\Facades\Validator;
use App\Models\ManageUtilityBillingSectorInformation;

class ManageUtilityBillingSectorInformationController extends Controller
{
    public function index(Request $request)
    {
        try {
            $manageUtilityBillingSectorInformation = ManageUtilityBillingSectorInformation::with([
                'company:id,name',
                'branch:id,name',
                'rentalspace:id,rental_code',
                'manageutilitysector:id,utility_billing_sector_name,billing_sector_code',
                'managebillingtype:id,billing_type_name',
            ])->orderBy('id', 'desc')->select(
                'id',
                'company_id',
                'branch_id',
                'rental_space_id',
                'manage_utility_sector_id',
                'meter_number',
                'meter_code',
                'customer_id_number',
                'customer_id_number_code',
                'phone_bill_number',
                'isp_name',
                'manage_billing_type_id',
                'remarks'
            );
            $manageUtilityBillingSectorInformation = $manageUtilityBillingSectorInformation->paginate($request->per_page);
            $manageUtilityBillingSectorInformation = $manageUtilityBillingSectorInformation->appends($request->all());
            $data['ManageUtilityBillingSectorInformation'] = $manageUtilityBillingSectorInformation;
            return response()->json([
                'success' => true,
                'message' => "ManageUtilityBillingSectorInformation List",
                'data' => $data
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 200);
        }
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required',
            'company_id' => 'required',
            'branch_id' => 'required',
            'rental_space_id' => 'required',
            'manage_utility_sector_id' => 'required',
            'manage_billing_type_id' => 'required',
//            'remarks' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 200);
        }

        DB::beginTransaction();

        try {

            $manage_utility_sector_id = $request->manage_utility_sector_id;

            $store_array = [
//
                'company_id' => $request->company_id,
                'branch_id' => $request->branch_id,
                'rental_space_id' => $request->rental_space_id,
                'manage_utility_sector_id' => $request->manage_utility_sector_id,
                'manage_billing_type_id' => $request->manage_billing_type_id,
//
            ];

            if ($manage_utility_sector_id == 1) {
                $meter_number = $request->meter_number;
                $meter_code = $request->meter_code;

                $validator = Validator::make($request->all(), [
                    'meter_number' => 'required',
                    'meter_code' => 'required',
                ]);


                if ($validator->fails()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Validation failed',
                        'errors' => $validator->errors(),
                    ], 200);
                }


                $store_array += ['meter_number' => $meter_number, 'meter_code' => $meter_code];
            } elseif ($manage_utility_sector_id == 2) {

                $meter_number = $request->meter_number;
                $meter_code = $request->meter_code;

                $validator = Validator::make($request->all(), [
                    'meter_number' => 'required',
                    'meter_code' => 'required',
                ]);


                if ($validator->fails()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Validation failed',
                        'errors' => $validator->errors(),
                    ], 200);
                }


                $store_array += ['meter_number' => $meter_number, 'meter_code' => $meter_code];
            } elseif ($manage_utility_sector_id == 3) {

                $phone_bill_number = $request->phone_bill_number;
                $validator = Validator::make($request->all(), [
                    'phone_bill_number' => 'required|digits:11',
                ]);


                if ($validator->fails()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Validation failed',
                        'errors' => $validator->errors(),
                    ], 200);
                }


                $store_array += ['phone_bill_number' => $phone_bill_number];
            } elseif ($manage_utility_sector_id == 4) {

                $isp_name = $request->isp_name;
                $validator = Validator::make($request->all(), [
                    'isp_name' => 'required',
                ]);


                if ($validator->fails()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Validation failed',
                        'errors' => $validator->errors(),
                    ], 200);
                }


                $store_array += ['isp_name' => $isp_name];
            } elseif ($manage_utility_sector_id == 5) {

                $customer_id_number = $request->customer_id_number;
                $customer_id_number_code = $request->customer_id_number_code;
                $validator = Validator::make($request->all(), [
                    'customer_id_number' => 'required',
                    'customer_id_number_code' => 'required'
                ]);


                if ($validator->fails()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Validation failed',
                        'errors' => $validator->errors(),
                    ], 200);
                }


                $store_array += ['customer_id_number' => $customer_id_number, 'customer_id_number_code' => $customer_id_number_code];
            }

            elseif ($manage_utility_sector_id == 6) {

            }
            else {
                return response()->json([
                    'success' => false,
                    'message' => 'The Utility Sector You Selcted is Newly Added So Please Contact With Software Developer to Add Functionality for Storing Utility Billing Sector Information.',
                ], 200);
            }
            $manageUtilityBillingSectorInformation = ManageUtilityBillingSectorInformation::where($store_array)->first();
            if (!$manageUtilityBillingSectorInformation){
                $store_array['date'] = $request->date;
                $store_array['remarks'] = $request->remarks;
                $manageUtilityBillingSectorInformation = ManageUtilityBillingSectorInformation::Create($store_array);
            }else{
                return response()->json([
                    'success' => false,
                    'message' => 'This data already exist',
                ], 200);
            }




            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data saved successfully',
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    public function edit(Request $request)
    {
        try {
            $data['manageUtilityBillingSectorInformation'] = ManageUtilityBillingSectorInformation::with([
                'company:id,name,company_code',
                'branch:id,name',
                'rentalspace:id,rental_space_name,rental_code',
                // 'rentalspaceowner:id,rentalspace_id,owner_name,owner_phone_number',
                'manageutilitysector:id,utility_billing_sector_name',
                'managebillingtype:id,billing_type_name',
            ])->find($request->id);

            return response()->json([
                'success' => true,
                'message' => "Manage Utility Billing Sector Info",
                'data' => $data
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 200);
        }
    }
public function update(Request $request)
{
    $validator = Validator::make($request->all(), [
        'date' => 'required',
        'company_id' => 'required',
        'branch_id' => 'required',
        'rental_space_id' => 'required',
        'manage_utility_sector_id' => 'required',
        'manage_billing_type_id' => 'required',
        'remarks' => 'required'
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $validator->errors(),
        ], 400);
    }

    DB::beginTransaction();

    try {
        $manageUtilityBillingSectorInformation = ManageUtilityBillingSectorInformation::find($request->id);
        if (!$manageUtilityBillingSectorInformation) {
            return response()->json([
                'success' => false,
                'message' => 'Not Found The ManageUtilityBillingSectorInformation With Id ' . $request->id,
            ], 404);
        }

        $update_array = [
            'date' => $request->date,
            'company_id' => $request->company_id,
            'branch_id' => $request->branch_id,
            'rental_space_id' => $request->rental_space_id,
            'manage_utility_sector_id' => $request->manage_utility_sector_id,
            'manage_billing_type_id' => $request->manage_billing_type_id,
            'remarks' => $request->remarks
        ];

        if ($request->manage_utility_sector_id == 1 || $request->manage_utility_sector_id == 2) {
            $validator = Validator::make($request->all(), [
                'meter_number' => 'required',
                'meter_code' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 400);
            }

            $update_array['meter_number'] = $request->meter_number;
            $update_array['meter_code'] = $request->meter_code;
        } elseif ($request->manage_utility_sector_id == 3) {
            $validator = Validator::make($request->all(), [
                'phone_bill_number' => 'required|digits:11',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 400);
            }

            $update_array['phone_bill_number'] = $request->phone_bill_number;
        } elseif ($request->manage_utility_sector_id == 4) {
            $validator = Validator::make($request->all(), [
                'isp_name' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 400);
            }

            $update_array['isp_name'] = $request->isp_name;
        } elseif ($request->manage_utility_sector_id == 5) {
            $validator = Validator::make($request->all(), [
                'customer_id_number' => 'required',
                'customer_id_number_code' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 400);
            }

            $update_array['customer_id_number'] = $request->customer_id_number;
            $update_array['customer_id_number_code'] = $request->customer_id_number_code;
        }
        elseif ($request->manage_utility_sector_id == 6) {

        }
        else {
            return response()->json([
                'success' => false,
                'message' => 'The Utility Sector You Selected is Newly Added. Please Contact the Software Developer to Add Functionality for Storing Utility Billing Sector Information.',
            ], 400);
        }
        // dd($update_array);

        $manageUtilityBillingSectorInformation->update($update_array);

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'ManageUtilityBillingSectorInformation updated successfully',
        ], 200);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'success' => false,
            'message' => 'An error occurred while updating ManageUtilityBillingSectorInformation',
            'error' => $e->getMessage(),
        ], 500);
    }
}
public function utilityBillingTypeList(Request $request)
{
    try {

        $utilityBilingType = ManageBillingType::select('id','billing_type_name')->orderBy('id', 'desc')->get();

        return response()->json([
            'success' => true,
            'message' => "Utility billing type List",
            'data' => $utilityBilingType
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage(),
        ], 500);
    }
}

//Expense Utility get api
public function getExpenseUtility(Request $request){
//    dd("i");
    $validator = Validator::make($request->all(), [
        'company_id' => 'required',
        'branch_id' => 'required',
        'rental_space_id' => 'required',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $validator->errors(),
        ], 200);
    }

    DB::beginTransaction();
    try {
        $company_id = $request->company_id;
        $branch_id = $request->branch_id;
        $rental_space_id = $request->rental_space_id;
        $manage_utility_sector_id = $request->manage_utility_sector_id;

        $query = ManageUtilityBillingSectorInformation::join('manage_billing_types', 'manage_utility_billing_sector_informations.manage_billing_type_id', '=', 'manage_billing_types.id')
            ->where('manage_utility_billing_sector_informations.company_id', $company_id)
            ->where('manage_utility_billing_sector_informations.branch_id', $branch_id)
            ->where('manage_utility_billing_sector_informations.rental_space_id', $rental_space_id)
            ->where('manage_utility_billing_sector_informations.manage_utility_sector_id', $manage_utility_sector_id);

        // Conditionally select on manage_utility_sector_id where 1=Electricity bill,2=Water and sewage bill,3=Phone bill,4=Internet bill,5=Titas gas bill,6=Gas cylinder bill
        if ($manage_utility_sector_id == 1 || $manage_utility_sector_id == 2) {
            $query->select('manage_utility_billing_sector_informations.id', 'meter_number', 'meter_code', 'billing_type_name');
        } elseif ($manage_utility_sector_id == 3 ){
            $query->select('manage_utility_billing_sector_informations.id', 'phone_bill_number','billing_type_name');
        } elseif ($manage_utility_sector_id == 4){
            $query->select('manage_utility_billing_sector_informations.id', 'isp_name','billing_type_name');
        } elseif ($manage_utility_sector_id == 5){
            $query->select('manage_utility_billing_sector_informations.id', 'customer_id_number','customer_id_number_code','billing_type_name');
        } elseif ($manage_utility_sector_id == 6){
            $query->select('manage_utility_billing_sector_informations.id','billing_type_name');
        }else {
            return response()->json([
                'success' => false,
                'message' => 'If you wish to add the Manage Utility Sector ID, please contact the software developer to implement the functionality for storing utility billing sectors.',
            ], 400);
        }
        $manageUtilityBillingSectorInformation = $query->orderBy('manage_utility_billing_sector_informations.id', 'desc')->get();

        $data['manageUtilityBillingSectorInformation'] = $manageUtilityBillingSectorInformation;

        return response()->json([
            'success' => true,
            'message' => "Utility Expense List",
            'data' => $data
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage(),
        ], 500);
    }
}



}
