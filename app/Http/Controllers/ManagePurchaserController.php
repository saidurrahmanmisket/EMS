<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ManagePurchaser;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Traits\UploadTraits;
use Illuminate\Support\Facades\File;

class ManagePurchaserController extends Controller
{
    use UploadTraits;
    public function index(Request $request)
    {
        try {

            $managePurchaser = ManagePurchaser::with([
                'company:id,name',
                'branch:id,name',
            ])->orderBy('id', 'desc')->select('id', 'company_id', 'branch_id', 'name', 'code', 'address', 'phone_number','document');
            $managePurchaser = $managePurchaser->paginate($request->per_page);
            $managePurchaser = $managePurchaser->appends($request->all());
            $data['rentalSpace'] = $managePurchaser;

            return response()->json([
                'success' => true,
                'message' => "Manage Purchaser list",
                'data' => $managePurchaser
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
            'branch_id' => 'required',
            'name' => 'required',
            'code' => 'required',
            'address' => 'required',
            'phone_number' => 'required|digits:11',
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
                $document = $this->uploadFile($request->file('document'), 'managepurchaser');
            }

            $managePurchaser = ManagePurchaser::create([
                'company_id' => $request->company_id,
                'branch_id' => $request->branch_id,
                'name' => $request->name,
                'code' => $request->code,
                'address' => $request->address,
                'phone_number' => $request->phone_number,
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
    public function show($id)
    {
        //
    }
    public function edit(Request $request)
    {
        try {
            $data['managePurchaser'] = ManagePurchaser::with([
                'company:id,name',
                'branch:id,name',

            ])->find($request->id);

            return response()->json([
                'success' => true,
                'message' => "Manage Purchaser",
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
            'branch_id' => 'required',
            'name' => 'required',
            'code' => 'required',
            'address' => 'required',
            'phone_number' => 'required|digits:11'

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

            $managePurchaser = ManagePurchaser::find($request->id);
            if (!empty($request->document)) {
                if (File::exists($managePurchaser->document)) {
                    File::delete($managePurchaser->document);
                }
                $document = $this->uploadFile($request->file('document'), 'managepurchaser');
            } else {
                $document = $managePurchaser->document;
            }

            ManagePurchaser::where('id', $request->id)->update([

                'company_id' => $request->company_id,
                'branch_id' => $request->branch_id,
                'name' => $request->name,
                'code' => $request->code,
                'address' => $request->address,
                'phone_number' => $request->phone_number,
                'document' => $document
            ]);
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Manage Purchaser Updated successfully',
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 200);
        }
    }
    public function destroy($id)
    {
        //
    }
    public function getPurchaserByCompanyAndBranch(Request $request){
        try {
            $data = ManagePurchaser::where('company_id', $request->company_id)
                ->where('branch_id', $request->branch_id)
                ->select('id','name', 'phone_number')
                ->get();

            return response()->json([
                'success' => true,
                'message' => "Purchaser Info",
                'data' => $data
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 200);
        }
    }
}
