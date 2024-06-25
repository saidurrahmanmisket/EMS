<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class BranchController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function list(Request $request)
    {
        try {
            $branch=Branch::with(['company' => function ($query) { $query->select('id', 'name');}])
                ->orderby('id', 'desc')
                ->select('id','company_id', 'name','branch_code', 'address', 'document');
            $branch = $branch->paginate($request->per_page);
            $branch=$branch->appends($request->all());
            $data['branch'] = $branch;
            return response()->json([
                'success' => true,
                'message' => "Branch Info",
                'data' => $data
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 200);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function add(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'company_id' => 'required',
                'branch_code' => 'required|unique:branches',
                'name' => 'required',
                'address' => 'required',
                'document' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'success' => 401,
                    'message' => "Validation Error.",
                    'error' => $validator->errors(),
                ], 401);
            }
            $file = $request->file('document');
            $file_path = null;
            if ($file && $file !== 'null') {
                $file_name = date('Ymd-his') . '.' . $file->getClientOriginalExtension();
                $destinationPathDb = 'images/branch/' . $file_name;
                $destinationPath = storage_path('images/branch/');
                $file->move($destinationPath, $file_name);
                $document = 'storage/' . $destinationPathDb;
            }
            $data = [
                'branch_code' => $request->branch_code,
                'company_id' => $request->company_id,
                'name' => $request->name,
                'address' => $request->address,
                'document' => $document,
            ];
            $data = Branch::create($data);

            return response()->json([
                'status' => true,
                'message' => "Branch created successfully",
                'data' => $data
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 200);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        try {
            $data['branch']=Branch::with(['company' => function ($query) { $query->select('id', 'name');}])
            ->find($request->id);

            return response()->json([
                'success' => true,
                'message' => "Branch Edit",
                'data' => $data
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 200);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'address' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'success' => 401,
                    'message' => "Validation Error.",
                    'error' => $validator->errors(),
                ], 401);
            }
            $branch = Branch::find($request->id);
            if (!empty($request->document)) {
                if (File::exists($branch->document)) {
                    File::delete($branch->document);
                }
                $file = $request->file('document');
                $file_name = date('Ymd-his') . '.' . $file->getClientOriginalExtension();
                $destinationPathDb = 'images/branch/' . $file_name;
                $destinationPath = storage_path('images/branch/');
                $file->move($destinationPath, $file_name);
                $document = 'storage/' . $destinationPathDb;
            }
            else{
                $document= $branch->document;
            }
            $data = [
                'name' => $request->name,
                'address' => $request->address,
                'document' => $document,
            ];
            $data = Branch::where('id', $request->id)->update($data);

            return response()->json([
                'status' => true,
                'message' => "Branch updated successfully",
                'data' => $data
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 200);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
    }

    public function getBranch(Request $request){
        try {
            $data = Branch::where('company_id', $request->company_id)
                ->select('id','name')
                ->get();

            return response()->json([
                'success' => true,
                'message' => "Branch Info",
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
