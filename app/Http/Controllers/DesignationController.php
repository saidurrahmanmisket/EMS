<?php

namespace App\Http\Controllers;

use App\Models\Designation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;

class DesignationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function list(Request $request)
    {
        try{
            $designation= Designation::with(['company' => function ($query) { $query->select('id', 'name');}])
                ->orderby('id', 'desc')
                ->select('id','company_id', 'name','document');
            $designation = $designation->paginate($request->per_page);
            $designation=$designation->appends($request->all());
            $data['designation'] = $designation;
            return response()->json([
                'success' => true,
                'message' => "Designation list",
                'data' => $data
            ], 200);
        }
        catch(\Exception $e){
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
        try{
            $validator = Validator::make($request->all(), [
                'company_id' => 'required',
                'name' => 'required',
                'document' => 'required',
            ]);
            if($validator->fails() ){
                return response()->json([
                    'success' => 401,
                    'message' => "Validator Error.",
                    'error' => $validator->errors(),
                ], 401);
            };
            $file = $request->file('document');
            $file_path = null;
            if ($file && $file !== 'null') {
                $file_name = date('Ymd-his') . '.' . $file->getClientOriginalExtension();
                $destinationPathDb = 'images/designation/' . $file_name;
                $destinationPath = storage_path('images/designation/');
                $file->move($destinationPath, $file_name);
                $document = 'storage/' . $destinationPathDb;
            }
            $data = [
                'company_id' => $request->company_id,
                'name' => $request->name,
                'document' => $document,
            ];
            $data = Designation::create($data);

            return response()->json([
                'success' => true,
                'message' => "Designation Created Successfully",
                'data' => $data
            ], 200);
        }
        catch (\Exception $e){
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
        try{
            $data['designation'] = Designation::with(['company' => function ($query) { $query->select('id', 'name');}])->find($request->id);

            return response()->json([
                'success' => true,
                'message' => "Designation Info",
                'data' => $data
            ], 200);
        }
        catch(\Exception $e){
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
        try{
            $validator = Validator::make($request->all(), [
                'name' => 'required',
            ]);
            if($validator->fails() ){
                return response()->json([
                    'success' => 401,
                    'message' => "Validator Error.",
                    'error' => $validator->errors(),
                ], 401);
            };
            $designation = Designation::find($request->id);
            if (!empty($request->document)) {
                if (File::exists($designation->document)) {
                    File::delete($designation->document);
                }
                $file = $request->file('document');
                $file_name = date('Ymd-his') . '.' . $file->getClientOriginalExtension();
                $destinationPathDb = 'images/designation/' . $file_name;
                $destinationPath = storage_path('images/designation/');
                $file->move($destinationPath, $file_name);
                $document = 'storage/' . $destinationPathDb;
            }
            else{
                $document = $designation->document;
            }
            $data = [
                'name' => $request->name,
                'document' => $document,
            ];
            $data = Designation::where('id', $request->id)->update($data);

            return response()->json([
                'success' => true,
                'message' => "Designation Updated Successfully",
                'data' => $data
            ], 200);
        }
        catch (\Exception $e){
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
    public function destroy($id)
    {
        //
    }

    public function getDesignation(Request $request){
        try {
            $data = Designation::where('company_id', $request->company_id)
                ->select('id','name')
                ->get();

            return response()->json([
                'success' => true,
                'message' => "Department Info",
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
