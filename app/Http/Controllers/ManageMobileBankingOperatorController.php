<?php

namespace App\Http\Controllers;

use App\Models\ManageMobileBankingOperator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Traits\UploadTraits;
use Illuminate\Support\Facades\File;

class ManageMobileBankingOperatorController extends Controller
{
    use UploadTraits;
    public function index(Request $request)
    {
        try {

            $manageMobileBankingOperator = ManageMobileBankingOperator::with([
                'company:id,name',
            ])->orderBy('id', 'desc')->select('id', 'company_id', 'name', 'operator_name_code', 'document');
            $manageMobileBankingOperator = $manageMobileBankingOperator->paginate($request->per_page);
            $manageMobileBankingOperator = $manageMobileBankingOperator->appends($request->all());
            $data['manageMobileBankingOperator'] = $manageMobileBankingOperator;

            return response()->json([
                'success' => true,
                'message' => "Manage Mobile Banking Operator list",
                'data' => $manageMobileBankingOperator
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    public function getList(Request $request){
        try {
            $company_id = $request->company_id;
            $manageMobileBankingOperator = ManageMobileBankingOperator::with([
                'company:id,name',
            ])->where('company_id', $company_id)->select('id', 'company_id', 'name', 'operator_name_code',)->orderBy('id', 'desc')->get();

            return response()->json([
                'success' => true,
                'message' => "Manage Mobile Banking Operator list",
                'data' => $manageMobileBankingOperator
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
            'operator_name_code' => 'required',
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
                $document = $this->uploadFile($request->file('document'), 'manageMobileBankingOperator');
            }

            $ManageMobileBankingOperator = ManageMobileBankingOperator::create([
                'company_id' => $request->company_id,
                'name' => $request->name,
                'operator_name_code' => $request->operator_name_code,
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
            $data['manageMobileBankingOperator'] = ManageMobileBankingOperator::with([
                'company:id,name',
            ])->find($request->id);

            return response()->json([
                'success' => true,
                'message' => "Manage Mobile Banking Operator",
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
            'operator_name_code' => 'required',
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

            $manageMobileBankingOperator = ManageMobileBankingOperator::find($request->id);
            if (!empty($request->document)) {
                if (File::exists($manageMobileBankingOperator->document)) {
                    File::delete($manageMobileBankingOperator->document);
                }
                $document = $this->uploadFile($request->file('document'), 'ManageMobileBankingOperator');
            } else {
                $document = $manageMobileBankingOperator->document;
            }

            ManageMobileBankingOperator::where('id', $request->id)->update([

                'company_id' => $request->company_id,
                'name' => $request->name,
                'operator_name_code' => $request->operator_name_code,
                'document' => $document
            ]);
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'ManageMobileBankingOperator Updated successfully',
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
