<?php

namespace App\Http\Controllers;

use App\Models\ManageBillingType;
use App\Models\ManageUtilitySector;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Traits\UploadTraits;
use Illuminate\Support\Facades\File;

class ManageBillingTypeController extends Controller
{
    use UploadTraits;
    public function index(Request $request)
    {
        try {

            $manageBillingType = ManageBillingType::with([
                'company:id,name,company_code',
                'manageutilitysector:id,utility_billing_sector_name',
            ])->orderBy('id', 'desc')->select('id','company_id','manage_utility_sector_id', 'billing_type_name','billing_type_code','document');
            $manageBillingType = $manageBillingType->paginate($request->per_page);
            $manageBillingType = $manageBillingType->appends($request->all());
            $data['manageBillingType'] = $manageBillingType;

            return response()->json([
                'success' => true,
                'message' => "Manage billing type list",
                'data' => $manageBillingType
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
            'manage_utility_sector_id' => 'required',
            'billing_type_name' => 'required',
            'billing_type_code' => 'required',
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
                $document = $this->uploadFile($request->file('document'), 'manageBillingType');
            }

            $manageBillingType = ManageBillingType::create([
                'company_id' => $request->company_id,
                'manage_utility_sector_id' => $request->manage_utility_sector_id,
                'billing_type_name' => $request->billing_type_name,
                'billing_type_code' => $request->billing_type_code,
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
            $data['manageBillingType'] = ManageBillingType::with([
                'company:id,name,company_code',
                'manageUtilitySector:id,utility_billing_sector_name,billing_sector_code',
            ])->find($request->id);

            return response()->json([
                'success' => true,
                'message' => "Manage utility sector",
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
            'billing_type_name' => 'required',
            'billing_type_code' => 'required',
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

            $manageBillingType = ManageBillingType::find($request->id);

            if(!$manageBillingType){
                return response()->json([
                    'success' => false,
                    'message' => 'Not Found The ManageBillingType With Id '.$request->id,
                ], 200);
            }
            if (!empty($request->document)) {
                if (File::exists($manageBillingType->document)) {
                    File::delete($manageBillingType->document);
                }
                $document = $this->uploadFile($request->file('document'), 'manageBillingType');
            } else {
                $document = $manageBillingType->document;
            }

            manageBillingType::where('id', $request->id)->update([
                'company_id' => $request->company_id,
                'manage_utility_sector_id' => $request->manage_utility_sector_id,
                'billing_type_name' => $request->billing_type_name,
                'billing_type_code' => $request->billing_type_code,
                'document' => $document
            ]);
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Managebillingtype Updated successfully',
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

    public function utilitySectorList(Request $request)
    {
        try {
            $company_id = $request->company_id;
            $manageUtilitySector = ManageUtilitySector::select('id','utility_billing_sector_name', 'company_id')->orderBy('id', 'asc')->limit(6);
            if ($company_id){
                $result = $manageUtilitySector->where('company_id', '=', $company_id)->get();
            }else{
                $result = $manageUtilitySector->get();
            }
            return response()->json([
                'success' => true,
                'message' => "Utility Sector List",
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

