<?php

namespace App\Http\Controllers;

use App\Models\ManageMerchantMobileBankingName;
use App\Models\ManageMerchantMobileBankingNumber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Traits\UploadTraits;
use Illuminate\Support\Facades\File;
class ManageMerchantMobileBankingNumberController extends Controller
{
    use UploadTraits;
    public function index(Request $request)
    {
        try {

            $manageMerchantMobileBankingNumber = ManageMerchantMobileBankingNumber::with([
                'company:id,name',
                'managemerchantmobilebankingname:id,name,merchant_code',
            ])->orderBy('id', 'desc')->select('id', 'company_id','manage_merchant_mobile_banking_name_id', 'merchant_mobile_number_code','mobile_number', 'document');
            $manageMerchantMobileBankingNumber = $manageMerchantMobileBankingNumber->paginate($request->per_page);
            $manageMerchantMobileBankingNumber = $manageMerchantMobileBankingNumber->appends($request->all());
            $data['ManageMerchantMobileBankingNumber'] = $manageMerchantMobileBankingNumber;

            return response()->json([
                'success' => true,
                'message' => "Manage Merchant Mobile Banking Number list",
                'data' => $manageMerchantMobileBankingNumber
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    //get list for payment dr
    public function getList(Request $request)
    {
        try {
            $company_id = $request->company_id;
            $manageMerchantMobileBankingNumber = ManageMerchantMobileBankingNumber::where('company_id', $company_id)
                ->select('id', 'company_id','mobile_number',)->get();


            return response()->json([
                'success' => true,
                'message' => "Manage Merchant Mobile Banking Number list",
                'data' => $manageMerchantMobileBankingNumber
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    public function create()
    {
        //
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company_id' => 'required',
            // 'manage_merchant_mobile_banking_name_id' => 'required',
            // 'merchant_code' => 'required',
            'mobile_number' => 'required',
            'merchant_mobile_number_code' => 'required',
            'document' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 401);
        }

        DB::beginTransaction();

        try {
            $document = null;
            if ($request->hasFile('document')) {
                $document = $this->uploadFile($request->file('document'), 'ManageMerchantMobileBankingNumber');
            }

            $manageMerchantMobileBankingNumber = ManageMerchantMobileBankingNumber::create([
                'company_id' => $request->company_id,
                'manage_merchant_mobile_banking_name_id' => $request->manage_merchant_mobile_banking_name_id,
                // 'merchant_code' => $request->merchant_code,
                'mobile_number' => $request->mobile_number,
                'merchant_mobile_number_code' => $request->merchant_mobile_number_code,
                'document' => $document,
            ]);

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
    public function show()
    {
        //
    }
    public function edit(Request $request)
    {
        try {
            $data['ManageMerchantMobileBankingNumber'] = ManageMerchantMobileBankingNumber::with([
                'company:id,name',
            ])->find($request->id);

            return response()->json([
                'success' => true,
                'message' => "Manage Merchant Mobile Banking Number",
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

            // 'company_id' => 'required',
            'mobile_number' => 'required',
            'merchant_mobile_number_code' => 'required',
            'manage_merchant_mobile_banking_name_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => 401,
                'message' => "validator failed",
                'error' => $validator->errors(),
            ], 401);
        }
        \DB::beginTransaction();

        try {

            $manageMerchantMobileBankingNumber = ManageMerchantMobileBankingNumber::find($request->id);
            if(!$manageMerchantMobileBankingNumber){
                return response()->json([
                    'success' => false,
                    'message' => 'Not Found The merchantmobilemankingnumber With Id '.$request->id,
                ], 200);
            }
            if (!empty($request->document)) {
                if (File::exists($manageMerchantMobileBankingNumber->document)) {
                    File::delete($manageMerchantMobileBankingNumber->document);
                }
                $document = $this->uploadFile($request->file('document'), 'ManageMerchantMobileBankingNumber');
            } else {
                $document = $manageMerchantMobileBankingNumber->document;
            }

            manageMerchantMobileBankingNumber::where('id', $request->id)->update([

                'company_id' => $request->company_id,
                'manage_merchant_mobile_banking_name_id' => $request->manage_merchant_mobile_banking_name_id,
                'merchant_mobile_number_code' => $request->merchant_mobile_number_code,
                'mobile_number' => $request->mobile_number,
                'document' => $document
            ]);
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'ManageMerchantBankingnumber Updated successfully',
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 200);
        }
    }
    public function destroy()
    {
        //
    }

    public function merchantlist(Request $request)
    {
        try {
            $company_id = $request->company_id;
            $merchantList = ManageMerchantMobileBankingName::select('id','name','merchant_code','company_id')->orderBy('id', 'desc');
            if ($company_id){
                $result = $merchantList->where('company_id', '=', $company_id)->get();
            }else{
                $result = $merchantList->get();
            }
            return response()->json([
                'success' => true,
                'message' => "Merchant list",
                'data' => $result
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
