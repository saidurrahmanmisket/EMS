<?php

namespace App\Http\Controllers;

use App\Models\ManageMerchantMobileBankingName;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Traits\UploadTraits;
use Illuminate\Support\Facades\File;
class ManageMerchantMobileBankingNameController extends Controller
{
    use UploadTraits;
    public function index(Request $request)
    {
        try {

            $manageMerchantMobileBanking = ManageMerchantMobileBankingName::with([
                'company:id,name',
            ])->orderBy('id', 'desc')->select('id', 'company_id', 'name', 'merchant_code','location','mobile_number', 'document');
            $manageMerchantMobileBanking = $manageMerchantMobileBanking->paginate($request->per_page);
            $manageMerchantMobileBanking = $manageMerchantMobileBanking->appends($request->all());
            $data['manageMerchantMobileBanking'] = $manageMerchantMobileBanking;

            return response()->json([
                'success' => true,
                'message' => "Manage Merchant Mobile Banking name list",
                'data' => $manageMerchantMobileBanking
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function getList(Request $request)
    {
        try {

            $manageMerchantMobileBanking = ManageMerchantMobileBankingName::select('id', 'company_id', 'name', 'merchant_code','mobile_number',)->get();

            return response()->json([
                'success' => true,
                'message' => "Manage Merchant Mobile Banking name list",
                'data' => $manageMerchantMobileBanking
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
            'name' => 'required',
            'merchant_code' => 'required',
            'location' => 'required',
            // 'mobile_number' => 'required',
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
                $document = $this->uploadFile($request->file('document'), 'manageMerchantMobileBankingName');
            }

            $manageMerchantMobileBankingName = ManageMerchantMobileBankingName::create([
                'company_id' => $request->company_id,
                'name' => $request->name,
                'merchant_code' => $request->merchant_code,
                'location' => $request->location,
                'mobile_number' => $request->mobile_number,
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
            $data['manageMerchantMobileBankingName'] = ManageMerchantMobileBankingName::with([
                'company:id,name',
            ])->find($request->id);

            return response()->json([
                'success' => true,
                'message' => "Manage Merchant Mobile Banking name",
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

            'company_id' => 'required',
            'name' => 'required',
            'merchant_code' => 'required',
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

            $manageMerchantMobileBanking = ManageMerchantMobileBankingName::find($request->id);
            if (!empty($request->document)) {
                if (File::exists($manageMerchantMobileBanking->document)) {
                    File::delete($manageMerchantMobileBanking->document);
                }
                $document = $this->uploadFile($request->file('document'), 'ManageMerchantMobileBanking');
            } else {
                $document = $manageMerchantMobileBanking->document;
            }

            manageMerchantMobileBankingName::where('id', $request->id)->update([

                'company_id' => $request->company_id,
                'name' => $request->name,
                'merchant_code' => $request->merchant_code,
                'location' => $request->location,
                'mobile_number' => $request->mobile_number,
                'document' => $document
            ]);
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'ManageMerchantBankingname Updated successfully',
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
}
