<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Designation;
use App\Models\VehicleDealerOrShowroom;
use App\Models\VehiclePreviousOwnerOrSeller;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;

class DealerOrShowroomController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function list(Request $request)
    {

        try {
            $vehicle_dealer_or_showrooms = VehicleDealerOrShowroom::orderby('id', 'desc');

            $vehicle_dealer_or_showrooms = $vehicle_dealer_or_showrooms->paginate(20);
            $vehicle_dealer_or_showrooms = $vehicle_dealer_or_showrooms->appends($request->all());


            foreach ($vehicle_dealer_or_showrooms as $key => $value) {
                Arr::forget($value, [
                    'created_at',
                    'updated_at',
                    'image_or_docs',
                    'company_id',
                ]);
            }

            $data['vehicle_dealer_or_showrooms'] = $vehicle_dealer_or_showrooms;

            return response()->json([
                'success' => true,
                'message' => "Vehicle Dealer or Showrooms List",
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
                'showroom_code' => 'required',
                'showroom_name' => 'required',
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
                $destinationPathDb = 'images/vehicle_dealer_or_showrooms/' . $file_name;
                $destinationPath = storage_path('images/vehicle_dealer_or_showrooms/');
                $image_or_docs->move($destinationPath, $file_name);
                $document_url = 'storage/' . $destinationPathDb;
            }
            $data = [
                'showroom_code' => $request->showroom_code,
                'showroom_name' => $request->showroom_name,
                'image_or_docs' => $document_url,
                'company_id' => $request->company_id,
            ];

            $data = VehicleDealerOrShowroom::create($data);


            if ($data) {
                return response()->json([
                    'success' => true,
                    'message' => "Vehicle Dealer Or Showroom is Created Successfully",
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => "Not Possible to Create Vehicle Dealer Or Showroom",
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
            $vehicle_dealer_or_showroom= VehicleDealerOrShowroom::with([
                'company'
            ])->find($request->id);

            $data=[];

            if($vehicle_dealer_or_showroom){

                Arr::forget($vehicle_dealer_or_showroom, [
                    'created_at',
                    'updated_at',
                    'company_id',
                    'company.updated_at',
                    'company.created_at'
                    ]);

                $data['vehicle_dealer_or_showroom']=$vehicle_dealer_or_showroom;

                return response()->json([
                    'success' => true,
                    'message' => "Vehicle Dealer or Showroom Info",
                    'data' => $data
                ], 200);
            }else{
                return response()->json([
                    'success' => false,
                    'message' => "Not Found Vehicle Dealer or Showroom Info With Id ".$request->id,
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
                'showroom_name' => 'required',
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

            $vehicle_dealer_or_showroom = VehicleDealerOrShowroom::find($request->id);

            if(!$vehicle_dealer_or_showroom){
                return response()->json([
                    'success' => false,
                    'message' => "Not Find Any Vehicle Dealer or Showroom With Id ".$request->id,
                ], 200);
            }

            $image_or_docs = $request->file('image_or_docs');

            if (!empty($image_or_docs)) {
                if (File::exists($vehicle_dealer_or_showroom->image_or_docs)) {
                    File::delete($vehicle_dealer_or_showroom->image_or_docs);
                }

                $file_name = date('Ymd-his') . '.' . $image_or_docs->getClientOriginalExtension();
                $destinationPathDb = 'images/vehicle_dealer_or_showrooms/' . $file_name;
                $destinationPath = storage_path('images/vehicle_dealer_or_showrooms/');
                $image_or_docs->move($destinationPath, $file_name);
                $document_url = 'storage/' . $destinationPathDb;
            } else {
                $document_url = $vehicle_dealer_or_showroom->image_or_docs;
                $subject = $document_url ;
                $search = url('/').'/'; ;
                $document_url = str_replace($search, '', $subject) ;
            }

            $data = [
                'showroom_name' => $request->showroom_name,
                'image_or_docs' => $document_url,
                'company_id' => $request->company_id,
            ];
            $is_updated = $vehicle_dealer_or_showroom->update($data);

            if($is_updated) {
                return response()->json([
                    'success' => true,
                    'message' => "Vehicle Dealer or Showroom is Updated Successfully",

                ], 200);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 200);
        }
    }



    public function get_dealer_or_showroom_list_for_select(Request $request)
    {

        try {
            $vehicle_dealers_or_showrooms = VehicleDealerOrShowroom::orderby('showroom_name');

            $showroom_name=$request->showroom_name;

            if($showroom_name){
                $vehicle_dealers_or_showrooms->where('showroom_name','LIKE',"%{$showroom_name}%");
            }

            $vehicle_dealers_or_showrooms=$vehicle_dealers_or_showrooms->get();

            foreach ($vehicle_dealers_or_showrooms as $key => $value) {
                Arr::forget($value, [
                    'created_at',
                    'updated_at',
                    'image_or_docs',
                    'company_id',
                    'showroom_code'
                ]);
            }


            $data['vehicle_dealers_or_showrooms'] = $vehicle_dealers_or_showrooms;

            return response()->json([
                'success' => true,
                'message' => "Vehicle Dealer or Showroom Name List for Select",
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
