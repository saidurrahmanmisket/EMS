<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Designation;
use App\Models\VehiclePreviousOwnerOrSeller;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;

class VehicleSellerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function list(Request $request)
    {

        try {
            $vehicle_previous_owner_or_sellers = VehiclePreviousOwnerOrSeller::orderby('id', 'desc');

            $vehicle_previous_owner_or_sellers = $vehicle_previous_owner_or_sellers->paginate(20);
            $vehicle_previous_owner_or_sellers = $vehicle_previous_owner_or_sellers->appends($request->all());


            foreach ($vehicle_previous_owner_or_sellers as $key => $value) {
                Arr::forget($value, [
                    'created_at',
                    'updated_at',
                    'image_or_docs',
                    'company_id',
                ]);
            }

            $data['vehicle_previous_owner_or_sellers'] = $vehicle_previous_owner_or_sellers;

            return response()->json([
                'success' => true,
                'message' => "Vehicle Previous Owner or Sellers List",
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
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'seller_code' => 'required',
                'seller_name' => 'required',
                'image_or_docs' => 'required',
                'company_id' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => "Validator Error.",
                    'error' => $validator->errors(),
                ], 200);
            };

            $company = Company::find($request->company_id);

            if (!$company) {
                return response()->json([
                    'success' => false,
                    'message' => "Not Find Any Company With Id " . $request->company_id,
                ], 200);
            }

            $image_or_docs = $request->file('image_or_docs');

            if ($image_or_docs) {
                $file_name = date('Ymd-his') . '.' . $image_or_docs->getClientOriginalExtension();
                $destinationPathDb = 'images/vehicle_sellers/' . $file_name;
                $destinationPath = storage_path('images/vehicle_sellers/');
                $image_or_docs->move($destinationPath, $file_name);
                $document_url = 'storage/' . $destinationPathDb;
            }
            $data = [
                'seller_code' => $request->seller_code,
                'seller_name' => $request->seller_name,
                'image_or_docs' => $document_url,
                'company_id' => $request->company_id,
            ];

            $data = VehiclePreviousOwnerOrSeller::create($data);


            if ($data) {
                return response()->json([
                    'success' => true,
                    'message' => "Vehicle Seller is Created Successfully",
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => "Not Possible to Vehicle Seller",
                    'data' => $data
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
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit_or_view(Request $request)
    {
        try {
            $vehicle_previous_owner_or_Seller= VehiclePreviousOwnerOrSeller::with([
                'company'
            ])->find($request->id);

            $data=[];

            if($vehicle_previous_owner_or_Seller){

                Arr::forget($vehicle_previous_owner_or_Seller, [
                    'created_at',
                    'updated_at',
                    'company_id',
                    'company.updated_at',
                    'company.created_at'
                    ]);

                $data['vehicle_previous_owner_or_Seller']=$vehicle_previous_owner_or_Seller;

                return response()->json([
                    'success' => true,
                    'message' => "Vehicle Previous Owner or Seller Info",
                    'data' => $data
                ], 200);
            }else{
                return response()->json([
                    'success' => false,
                    'message' => "Not Found Vehicle Previous Owner or Seller Info With Id ".$request->id,
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
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {

        try {
            $validator = Validator::make($request->all(), [
                'id'=>'required',
                'seller_name' => 'required',
                'company_id' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => "Validator Error.",
                    'error' => $validator->errors(),
                ], 200);
            };

            $company = Company::find($request->company_id);

            if(!$company){
                return response()->json([
                    'success' => false,
                    'message' => "Not Find Any Company With Id ".$request->company_id,
                ], 200);
            }

            $vehicle_previous_owner_or_seller = VehiclePreviousOwnerOrSeller::find($request->id);

            if(!$vehicle_previous_owner_or_seller){
                return response()->json([
                    'success' => false,
                    'message' => "Not Find Any Vehicle Previous Owner Or Seller With Id ".$request->id,
                ], 200);
            }

            $image_or_docs = $request->file('image_or_docs');

            if (!empty($image_or_docs)) {
                if (File::exists($vehicle_previous_owner_or_seller->image_or_docs)) {
                    File::delete($vehicle_previous_owner_or_seller->image_or_docs);
                }

                $file_name = date('Ymd-his') . '.' . $image_or_docs->getClientOriginalExtension();
                $destinationPathDb = 'images/vehicle_sellers/' . $file_name;
                $destinationPath = storage_path('images/vehicle_sellers/');
                $image_or_docs->move($destinationPath, $file_name);
                $document_url = 'storage/' . $destinationPathDb;
            } else {
                $document_url = $vehicle_previous_owner_or_seller->image_or_docs;
                $subject = $document_url ;
                $search = url('/').'/'; ;
                $document_url = str_replace($search, '', $subject) ;
            }

            $data = [
                'seller_name' => $request->seller_name,
                'image_or_docs' => $document_url,
                'company_id' => $request->company_id,
            ];
            $is_updated = $vehicle_previous_owner_or_seller->update($data);

            if($is_updated) {
                return response()->json([
                    'success' => true,
                    'message' => "Vehicle Seller is Updated Successfully",

                ], 200);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 200);
        }
    }



    public function get_previous_owner_or_seller_name_list_for_select(Request $request)
    {

        try {
            $vehicle_previous_owner_or_sellers = VehiclePreviousOwnerOrSeller::orderby('seller_name');

            $seller_name=$request->seller_name;

            if($seller_name){
                $vehicle_previous_owner_or_sellers->where('seller_name','LIKE',"%{$seller_name}%");
            }

            $vehicle_previous_owner_or_sellers=$vehicle_previous_owner_or_sellers->get();

            foreach ($vehicle_previous_owner_or_sellers as $key => $value) {
                Arr::forget($value, [
                    'created_at',
                    'updated_at',
                    'image_or_docs',
                    'company_id',
                    'seller_code'
                ]);
            }


            $data['vehicle_previous_owner_or_sellers'] = $vehicle_previous_owner_or_sellers;

            return response()->json([
                'success' => true,
                'message' => "Previous Owner or Seller Name List for Select",
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
