<?php

namespace App\Http\Controllers;

use App\Models\ManageUtilitySector;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Traits\UploadTraits;
use Illuminate\Support\Facades\File;

class ManageUtilitySectorController extends Controller
{
    use UploadTraits;
    public function index(Request $request)
    {
        try {

            $manageUtilitySector = ManageUtilitySector::with([
                'company:id,name,company_code',
            ])->orderBy('id', 'desc')->select('id', 'company_id', 'utility_billing_sector_name', 'billing_sector_code','document');
            $manageUtilitySector = $manageUtilitySector->paginate($request->per_page);
            $manageUtilitySector = $manageUtilitySector->appends($request->all());
            $data['manageUtilitySector'] = $manageUtilitySector;

            return response()->json([
                'success' => true,
                'message' => "Manage utility sector list",
                'data' => $manageUtilitySector
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
            'utility_billing_sector_name' => 'required',
            'billing_sector_code' => 'required',
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
                $document = $this->uploadFile($request->file('document'), 'manageUtilitySector');
            }

            $manageUtilitySector = ManageUtilitySector::create([
                'company_id' => $request->company_id,
                'utility_billing_sector_name' => $request->utility_billing_sector_name,
                'billing_sector_code' => $request->billing_sector_code,
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
            $data['manageUtilitySector'] = ManageUtilitySector::with([
                'company:id,name,company_code',
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

            'company_id' => 'required',
            'utility_billing_sector_name' => 'required',
            'billing_sector_code' => 'required',
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

            $manageUtilitySector = ManageUtilitySector::find($request->id);
            if (!empty($request->document)) {
                if (File::exists($manageUtilitySector->document)) {
                    File::delete($manageUtilitySector->document);
                }
                $document = $this->uploadFile($request->file('document'), 'manageUtilitySector');
            } else {
                $document = $manageUtilitySector->document;
            }

            ManageUtilitySector::where('id', $request->id)->update([

                'company_id' => $request->company_id,
                'utility_billing_sector_name' => $request->utility_billing_sector_name,
                'billing_sector_code' => $request->billing_sector_code,
                'document' => $document
            ]);
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Manageutilitysector Updated successfully',
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
