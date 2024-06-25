<?php

namespace App\Http\Controllers;

use App\Models\ManageBank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Traits\UploadTraits;
use Illuminate\Support\Facades\File;

class ManageBankController extends Controller
{
    use UploadTraits;

    public function index(Request $request)
    {
        try {

            $manageBank = ManageBank::with([
                'company:id,name',
            ])->orderBy('id', 'desc')->select('id', 'company_id', 'name', 'bank_name_code', 'document');
            $manageBank = $manageBank->paginate($request->per_page);
            $manageBank = $manageBank->appends($request->all());
            $data['manageBank'] = $manageBank;

            return response()->json([
                'success' => true,
                'message' => "Manage Bank list",
                'data' => $manageBank
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
            'bank_name_code' => 'required',
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
                $document = $this->uploadFile($request->file('document'), 'manageBank');
            }

            $manageBank = ManageBank::create([
                'company_id' => $request->company_id,
                'name' => $request->name,
                'bank_name_code' => $request->bank_name_code,
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

    public function getBanks(Request $request)
    {
        try {
            $company_id = $request->company_id;
            $manageBank = ManageBank::with([
                'company:id,name',
            ])
                ->select('id', 'name', 'bank_name_code', 'document', 'company_id',);
            if ($company_id){
                $manageBank->where('company_id', $company_id);

            }
                $result = $manageBank->get();

            if ($manageBank->count() == 0) {
                return response()->json([
                    'success' => false,
                    'message' => "Bank Not Found"
                ]);
            }
            return response()->json([
                'success' => true,
                'message' => "Manage Bank list",
                'data' => $result
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => "Something went wrong",
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function edit(Request $request)
    {
        try {
            $data['manageBank'] = ManageBank::with([
                'company:id,name',
            ])->find($request->id);

            return response()->json([
                'success' => true,
                'message' => "Manage Bank",
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
            'bank_name_code' => 'required',
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

            $manageBank = ManageBank::find($request->id);
            if (!empty($request->document)) {
                if (File::exists($manageBank->document)) {
                    File::delete($manageBank->document);
                }
                $document = $this->uploadFile($request->file('document'), 'manageBank');
            } else {
                $document = $manageBank->document;
            }

            manageBank::where('id', $request->id)->update([

                'company_id' => $request->company_id,
                'name' => $request->name,
                'bank_name_code' => $request->bank_name_code,
                'document' => $document
            ]);
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Manage Bank Updated successfully',
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 200);
        }
    }

    public function destroy(ManageBank $manageBank)
    {
        //
    }
}
