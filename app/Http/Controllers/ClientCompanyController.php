<?php

namespace App\Http\Controllers;

use App\Models\ClientCompany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClientCompanyController extends Controller
{
    public function list(Request $request){
        try{
            $client_company = ClientCompany::with([
                'company:id,name'
            ])->orderBy('id', 'desc')->select('id', 'name', 'phone_number', 'location', 'company_id');
            $client_company = $client_company->paginate($request->per_page);
            $client_company = $client_company->appends($request->all());
            $data['client_company'] = $client_company;
            return response()->json([
                'success' => true,
                'message' => "Client Company List",
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
    public function add(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'company_id' => 'required',
                'name' => 'required',
                'phone_number' => 'required|numeric|digits:11',
                'client_company_code' => 'required|unique:client_companies',
                'location' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'success' => 401,
                    'message' => "Validation Error.",
                    'error' => $validator->errors(),
                ], 401);
            }
            $data = ClientCompany::create([
                'company_id' => $request->company_id,
                'name' => $request->name,
                'phone_number' => $request->phone_number,
                'client_company_code' => $request->client_company_code,
                'location' => $request->location,
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Client Company Created Successfully',
                'data' => $data,
            ], 200);
        }
        catch (\Exception $e){
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 200);
        }
    }
    public function edit(Request $request){
        try{
            $data['client_company'] = ClientCompany::with([
                'company:id,name,company_code'
            ])->select('id', 'company_id', 'name', 'phone_number', 'client_company_code', 'location')->find($request->id);
            return response()->json([
                'success' => true,
                'message' => 'Single Client Company Info',
                'data' => $data,
            ], 200);
        }
        catch (\Exception $e){
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 200);
        }
    }
    public function update(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'company_id' => 'required',
                'name' => 'required',
                'phone_number' => 'required|numeric|digits:11',
                'location' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'success' => 401,
                    'message' => "Validation Error.",
                    'error' => $validator->errors(),
                ], 401);
            }
            $data = ClientCompany::where('id', $request->id)->update([
                'company_id' => $request->company_id,
                'name' => $request->name,
                'phone_number' => $request->phone_number,
                'location' => $request->location,
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Client Company Updated Successfully',
                'data' => $data,
            ], 200);
        }
        catch (\Exception $e){
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 200);
        }
    }
}
