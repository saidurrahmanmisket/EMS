<?php

namespace App\Http\Controllers;

use App\Models\CompanyDocument;
use App\Traits\UploadTraits;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\Company;
use Illuminate\Support\Facades\Validator;

class CompanyController extends Controller
{
    use UploadTraits;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function list(Request $request)
    {
        try {
            $company=Company::orderby('id', 'desc')
                ->select('id','name','company_code', 'logo', 'contact_number_1', 'contact_number_2', 'address', 'establish_date');
            $company = $company->paginate($request->per_page);
            $company=$company->appends($request->all());
            $data['company'] = $company;
            return response()->json([
                'success' => true,
                'message' => "Company Info",
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
                'name' => 'required',
                'company_code' => 'required|unique:companies',
                'address' => 'required',
                'contact_number_1' => 'required|numeric|digits:11',
                'contact_number_2' => 'required|numeric|digits:11',
                'email' => 'required|email',
                'web_address' => 'required',
                'establish_date' => 'required',
                'company_type' => 'required',
                'tin_number' => 'required',
                'trade_number' => 'required',
                'registration_number' => 'required',
                'short_description' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => "Validation Error.",
                    'error' => $validator->errors(),
                ], 401);
            }
            $file = $request->file('logo');
            $file_path = null;
            if ($file && $file !== 'null') {
                $file_name = date('Ymd-his') . '.' . $file->getClientOriginalExtension();
                $destinationPath = 'images/company/' . $file_name;
                Image::make($file->getRealPath())->resize(400, 300)->save(storage_path($destinationPath));
                $image = 'storage/' . $destinationPath;
            }
            else{
                $image=null;
            }
            $data = [
                'company_code' => $request->company_code,
                'name' => $request->name,
                'address' => $request->address,
                'contact_number_1' => $request->contact_number_1,
                'contact_number_2' => $request->contact_number_2,
                'email' => $request->email,
                'web_address' => $request->web_address,
                'establish_date' => $request->establish_date,
                'company_type' => $request->company_type,
                'tin_number' => $request->tin_number,
                'trade_number' => $request->trade_number,
                'logo' => $image,
                'registration_number' => $request->registration_number,
                'short_description' => $request->short_description
            ];

            $data = Company::create($data);


            return response()->json([
                'status' => true,
                'message' => "Company created successfully",
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
//        $company = Company::find($request->id);
        try {
            $data['company']=Company::find($request->id);

            return response()->json([
                'success' => true,
                'message' => "Company Edit",
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
//                'company_code' => 'required|unique:companies',
                'address' => 'required',
                'contact_number_1' => 'required|numeric|digits:11',
                'contact_number_2' => 'required|numeric|digits:11',
                'email' => 'required|email',
                'web_address' => 'required',
                'establish_date' => 'required',
                'company_type' => 'required',
                'tin_number' => 'required',
                'trade_number' => 'required',
                'registration_number' => 'required',
                'short_description' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'success' => 401,
                    'message' => "Validation Error.",
                    'error' => $validator->errors(),
                ], 401);
            }
            $company = Company::find($request->id);
            if($company) {
                if (!empty($request->logo)) {
                    if (File::exists($company->logo)) {
                        File::delete($company->logo);
                    }
                    $file = $request->file('logo');
                    $file_name = date('Ymd-his') . '.' . $file->getClientOriginalExtension();
                    $destinationPath = 'images/company/' . $file_name;
                    Image::make($file->getRealPath())->resize(400, 300)->save(storage_path($destinationPath));
                    $image = 'storage/' . $destinationPath;
                } else {
                    $image = $company->logo;
                }
                $data = [
                    'company_code' => $request->company_code,
                    'name' => $request->name,
                    'address' => $request->address,
                    'contact_number_1' => $request->contact_number_1,
                    'contact_number_2' => $request->contact_number_2,
                    'email' => $request->email,
                    'web_address' => $request->web_address,
                    'establish_date' => $request->establish_date,
                    'company_type' => $request->company_type,
                    'tin_number' => $request->tin_number,
                    'trade_number' => $request->trade_number,
                    'logo' => $image,
                    'registration_number' => $request->registration_number,
                    'short_description' => $request->short_description
                ];
                $data = Company::where('id', $request->id)->update($data);


                return response()->json([
                    'status' => true,
                    'message' => "Company updated successfully",
                    'data' => $data
                ], 200);
            }
            else{
                return response()->json([
                'success' => false,
                'message' => 'Not Find Any Company With Id '.$request->id,
            ], 200);
            }

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

    public function getCompany()
    {
        try {
            $data['company']=Company::orderby('id', 'desc')
                ->select('id','name', 'company_code')
                ->get();
            return response()->json([
                'success' => true,
                'message' => "Company info",
                'data' => $data
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 200);
        }

    }
    public function getCompanyDocument(Request $request)
    {
        try {
            $data['Company_document']=Company::where('id', $request->id)->whereNotNull('logo')
                ->select('id','logo')
                ->get();
            $data['Others_documents']=CompanyDocument::where('company_id', $request->id)
                ->select('id', 'document')
                ->get();
            return response()->json([
                'success' => true,
                'message' => "Company Documents",
                'data' => $data
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 200);
        }

    }
    public function addOthersDocument(Request $request)
    {
//        Log::debug($request->all());
        try {
            $validator = Validator::make($request->all(), [
                'document' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => "Validation Error.",
                    'error' => $validator->errors(),
                ], 401);
            }
            if(is_array($request->document)){
                foreach($request->document as  $documentData){
                    $file = $documentData['document'] ?? null;
                    if($file !== 'null'){
                        $file_type = $file->getClientOriginalExtension();
                        if($file_type != "jpg" && $file_type != "png" && $file_type != "jpeg" && $file_type != "svg"){
                            $document = $this->uploadFile($file, 'company_document');
                        }
                        else{
                            $document = $this->uploadImage($file, 'company_document');
                        }
                    }
                    $other_document = array(
                        'company_id' => $documentData['id'],
                        'document' => $document,
                    );
                    $data['document'] = CompanyDocument::create($other_document);
                }
            }
            return response()->json([
                'success' => true,
                'message' => "Company Documents uploaded Successfully",
                'data' => $data
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 200);
        }

    }
    public function deleteDocument(Request $request)
    {
        try {
            $company = Company::find($request->id);
            if ($company) {
                if (File::exists($company->logo)) {
                    File::delete($company->logo);
                }
            }
            $data = Company::where('id', $request->id)->update([
                'logo' => null,
            ]);
            return response()->json([
                'success' => true,
                'message' => "Company Document Deleted Successfully",
                'data' => $data
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 200);
        }

    }
    public function deleteOthersDocument(Request $request)
    {
        try {
            $company_document = CompanyDocument::find($request->id);
            if ($company_document) {
                if (File::exists($company_document->document)) {
                    File::delete($company_document->document);
                }
            }
            $data= $company_document->delete();
            return response()->json([
                'success' => true,
                'message' => "Company Others Document Deleted successfully",
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
