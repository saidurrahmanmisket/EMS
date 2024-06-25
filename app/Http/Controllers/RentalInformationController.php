<?php

namespace App\Http\Controllers;

// use App\Models\RentalSpaceCity;
use App\Models\RentalSpaceCity;
use App\Models\RentalSpaceDocument;
use App\Models\RentalSpaceThanaPoZip;
use DB;
use App\Models\RentalSpace;
use Illuminate\Http\Request;
use App\Models\RentalSpaceFee;
use App\Models\RentalSpaceOwner;
use App\Models\RentalSpaceTenant;
use App\Models\RentalSpaceParkingFee;
use Illuminate\Support\Facades\Validator;
use App\Traits\UploadTraits;
use Illuminate\Support\Facades\File;
use App\Http\Resources\RentalResource;




class RentalInformationController extends Controller
{
    use UploadTraits;
    public function index(Request $request)
    {
        try {
            $rentalSpace = RentalSpace::with([
                'company:id,name',
                'branch:id,name',
                'rentalSpaceOwner:rental_space_id,owner_name,owner_phone_number,owner_address',
                'rentalSpaceTenant:rental_space_id,tenant_name',

            ])->orderBy('id', 'desc')
                ->select('id', 'company_id', 'branch_id');
            $rentalSpace = $rentalSpace->paginate($request->per_page);
            $rentalSpace = $rentalSpace->appends($request->all());
            $data['rentalSpace'] = $rentalSpace;

            return response()->json([
                'success' => true,
                'message' => "Rental list",
                'data' => $data
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
            'rental_space_name' => 'required',
            'company_id' => 'required',
            'rental_code' => 'required',
            'branch_id' => 'required',
            'rental_space_remarks' => 'required',
            'rental_space_image' => 'file|mimes:png,jpg,svg,jpeg',
            'rental_space_date' => 'required',
            'rental_space_city_id' => 'required',
            'house_no' => 'required',
            'road_no' => 'required',
            'rental_space_thana_po_zip_id' => 'required',

            'space_size' => 'required',
            'number_of_room' => 'required',
            'space_description' => 'required',
            'rental_space_id' => 'required',
            'owner_name' => 'required',
            'owner_father_name' => 'required',
            'owner_mother_name' => 'required',
            'owner_address' => 'required',
            'owner_phone_number' => 'required|digits:11',
            'owner_nid_no' => 'required',
            'owner_document' => 'required',
            'owner_remarks' => 'required',
            'tenant_name' => 'required',
            'tenant_father_name' => 'required',
            'tenant_mother_name' => 'required',
            'tenant_branch_name' => 'required',
            'tenant_company_name' => 'required',
            'rent_for' => 'required',
            'tenant_address' => 'required',
            'tenant_nid_no' => 'required',
            'tenant_phone_number' => 'required|digits:11',
            'tenant_image' => 'file|mimes:png,jpg,svg,jpeg',
            'tenant_document' => 'required',
            'tenant_remarks' => 'required',
            'fee_advance_amount' => 'required',
            'fee_per_month_rental_cost' => 'required',
            'fee_document' => 'required',
            'agreement_validity_from' => 'required',
            'agreement_validity_to' => 'required',
            'fee_rent_payment_date' => 'required',
            'fee_remarks' => 'required',
            'parking_fee' => 'required',
            'fee_per_month_parking_cost' => 'required',
            'vehicle_allowed_number' => 'required',
            'parking_zone_size' => 'required',
            'parking_remarks' => 'required'
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

            $rental_space_image = null;
            if ($request->hasFile('rental_space_image')) {
                $rental_space_image = $this->uploadImage($request->file('rental_space_image'), 'rentalspace');
            }
            $rentalspace = RentalSpace::create([
                'rental_space_date' => $request->rental_space_date,
                'company_id' => $request->company_id,
                'branch_id' => $request->branch_id,
                'rental_space_name' => $request->rental_space_name,
                'rental_code' => $request->rental_code,
                'rental_space_remarks' => $request->rental_space_remarks,
                'rental_space_city_id' => $request->rental_space_city_id,
                'house_no' => $request->house_no,
                'road_no' => $request->road_no,
                'rental_space_thana_po_zip_id' => $request->rental_space_thana_po_zip_id,
                // 'zip_code' => $request->zip_code,
                'space_size' => $request->space_size,
                'number_of_room' => $request->number_of_room,
                'rental_space_image' => $rental_space_image,
                'space_description' => $request->space_description,
            ]);



            $owner_document = null;
            if ($request->hasFile('owner_document')) {
                $owner_document = $this->uploadFile($request->file('owner_document'), 'ownerdocument');
            }

            $owner = RentalSpaceOwner::create([
                'rental_space_id' => $rentalspace->id,


                'owner_name' => $request->owner_name,
                'owner_father_name' => $request->owner_father_name,
                'owner_mother_name' => $request->owner_mother_name,
                'owner_address' => $request->owner_address,
                'owner_phone_number' => $request->owner_phone_number,
                'owner_nid_no' => $request->owner_nid_no,
                'owner_document' => $owner_document,
                'owner_remarks' => $request->owner_remarks,
            ]);



            $tenant_image = null;
            $tenant_document = null;
            if ($request->hasFile('tenant_image')) {
                $tenant_image = $this->uploadImage($request->file('tenant_image'), 'tenantimage');
            }
            if ($request->hasFile('tenant_document')) {
                $tenant_document = $this->uploadFile($request->file('tenant_document'), 'tenantdocument');
            }
            $tenant = RentalSpaceTenant::create([
                'rental_space_id' => $rentalspace->id,
                'tenant_name' => $request->tenant_name,
                'tenant_father_name' => $request->tenant_father_name,
                'tenant_mother_name' => $request->tenant_mother_name,
                'rent_for' => $request->rent_for,
                'tenant_branch_name' => $request->tenant_branch_name,
                'tenant_company_name' => $request->tenant_company_name,
                'tenant_address' => $request->tenant_address,
                'tenant_nid_no' => $request->tenant_nid_no,
                'tenant_phone_number' => $request->tenant_phone_number,
                'tenant_image' => $tenant_image,
                'tenant_document' => $tenant_document,
                'tenant_remarks' => $request->tenant_remarks,
            ]);



            $fee_document = null;
            if ($request->hasFile('fee_document')) {
                $fee_document = $this->uploadFile($request->file('fee_document'), 'feedocument');
            }
            $fee = RentalSpaceFee::create([
                'rental_space_id' => $rentalspace->id,
                'fee_advance_amount' => $request->fee_advance_amount,
                'fee_per_month_rental_cost' => $request->fee_per_month_rental_cost,
                'fee_document' => $fee_document,
                'agreement_validity_from' => $request->agreement_validity_from,
                'agreement_validity_to' => $request->agreement_validity_to,
                'fee_rent_payment_date' => $request->fee_rent_payment_date,
                'fee_remarks' => $request->fee_remarks,
            ]);



            $parking_document = null;
            if ($request->hasFile('parking_document')) {
                $parking_document = $this->uploadFile($request->file('parking_document'), 'parkingdocument');
            }
            $parking = RentalSpaceParkingFee::create([
                'rental_space_id' => $rentalspace->id,
                'parking_fee' => $request->parking_fee,
                'fee_per_month_parking_cost' => $request->fee_per_month_parking_cost,
                'parking_document' => $parking_document,
                'vehicle_allowed_number' => $request->vehicle_allowed_number,
                'parking_zone_size' => $request->parking_zone_size,
                'parking_remarks' => $request->parking_remarks,
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
            ], 200);
        }
    }
    public function show($id)
    {
        //
    }
    public function edit(Request $request)
    {
        try {
            $data['rentalSpace'] = RentalSpace::with([
                'company:id,name,company_code',
                'branch:id,name',
                'rentalSpaceOwner:id,rental_space_id,owner_name,owner_father_name,owner_mother_name,owner_address,owner_phone_number,owner_nid_no,owner_document,owner_remarks',
                'rentalSpaceTenant:id,rental_space_id,tenant_name,tenant_father_name,tenant_mother_name,rent_for,tenant_branch_name,tenant_company_name,tenant_address,tenant_nid_no,tenant_phone_number,tenant_image,tenant_document,tenant_remarks',
                'rentalSpaceFee:id,rental_space_id,fee_advance_amount,fee_per_month_rental_cost,fee_document,agreement_validity_from,agreement_validity_to,fee_rent_payment_date,fee_remarks',
                'rentalSpaceParkingFee:id,rental_space_id,parking_fee,fee_per_month_parking_cost,parking_document,vehicle_allowed_number,parking_zone_size,parking_remarks',
                'rentalSpaceCity:id,name',
                'rentalSpaceThanaPoZip:id,rental_space_city_id,thana_name,po_name,zip_code',

            ])->find($request->id);

            return response()->json([
                'success' => true,
                'message' => "Rental Info",
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
            'rental_space_name' => 'required',
            'company_id' => 'required',
            'rental_code' => 'required',
            'branch_id' => 'required',
            'rental_space_remarks' => 'required',
            // 'rental_space_image' => 'file|mimes:png,jpg,svg,jpeg',
            'rental_space_date' => 'required',
            'rental_space_city_id' => 'required',
            'house_no' => 'required',
            'road_no' => 'required',
            'rental_space_thana_po_zip_id' => 'required',
            'space_size' => 'required',
            'number_of_room' => 'required',
            'space_description' => 'required',
            'rental_space_id' => 'required',
            'owner_name' => 'required',
            'owner_father_name' => 'required',
            'owner_mother_name' => 'required',
            'owner_address' => 'required',
            'owner_phone_number' => 'required|digits:11',
            'owner_nid_no' => 'required',
            // 'owner_document' => 'required',
            'owner_remarks' => 'required',
            'tenant_name' => 'required',
            'tenant_father_name' => 'required',
            'tenant_mother_name' => 'required',
            'tenant_branch_name' => 'required',
            'tenant_company_name' => 'required',
            'rent_for' => 'required',
            'tenant_address' => 'required',
            'tenant_nid_no' => 'required',
            'tenant_phone_number' => 'required|digits:11',
            // 'tenant_image' => 'file|mimes:png,jpg,svg,jpeg',
            // 'tenant_document' => 'required',
            'tenant_remarks' => 'required',
            'fee_advance_amount' => 'required',
            'fee_per_month_rental_cost' => 'required',
            // 'fee_document' => 'required',
            'agreement_validity_from' => 'required',
            'agreement_validity_to' => 'required',
            'fee_rent_payment_date' => 'required',
            'fee_remarks' => 'required',
            'parking_fee' => 'required',
            'fee_per_month_parking_cost' => 'required',
            'vehicle_allowed_number' => 'required',
            'parking_zone_size' => 'required',
            'parking_remarks' => 'required'
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

            $rentalspace = RentalSpace::find($request->id);

            if(!$rentalspace){
                return response()->json([
                    'success' => false,
                    'message' => 'Not Found The RentalSpace With Id '.$request->id,
                ], 200);
            }
            if (!empty($request->rental_space_image)) {
                if (File::exists($rentalspace->rental_space_image)) {
                    File::delete($rentalspace->rental_space_image);
                }
                $rental_space_image = $this->uploadImage($request->file('rental_space_image'), 'rentalspace');
            } else {
                $rental_space_image = $rentalspace->rental_space_image;
            }



            RentalSpace::where('id', $request->id)->update([
                'rental_space_date' => $request->rental_space_date,
                'company_id' => $request->company_id,
                'branch_id' => $request->branch_id,
                'rental_space_name' => $request->rental_space_name,
                'rental_code' => $request->rental_code,
                'rental_space_remarks' => $request->rental_space_remarks,
                'rental_space_city_id' => $request->rental_space_city_id,
                'house_no' => $request->house_no,
                'road_no' => $request->road_no,
                'rental_space_thana_po_zip_id' => $request->rental_space_thana_po_zip_id,
                // 'zip_code' => $request->zip_code,
                'space_size' => $request->space_size,
                'number_of_room' => $request->number_of_room,
                'rental_space_image' => $rental_space_image,
                'space_description' => $request->space_description,
            ]);

            $owner = RentalSpaceOwner::where('rental_space_id', $request->id)->first();

            if ($owner) {
                if (!empty($request->owner_document)) {
                    if (File::exists($owner->owner_document)) {
                        File::delete($owner->owner_document);
                    }
                    $owner_document = $this->uploadFile($request->file('owner_document'), 'ownerdocument');
                } else {
                    $owner_document = $owner->owner_document;
                }

                $owner->update([
                    'owner_name' => $request->owner_name,
                    'owner_father_name' => $request->owner_father_name,
                    'owner_mother_name' => $request->owner_mother_name,
                    'owner_address' => $request->owner_address,
                    'owner_phone_number' => $request->owner_phone_number,
                    'owner_nid_no' => $request->owner_nid_no,
                    'owner_document' => $owner_document,
                    'owner_remarks' => $request->owner_remarks,
                ]);
            }


            $tenant = RentalSpaceTenant::where('rental_space_id', $request->id)->first();

            if ($tenant) {
                if (!empty($request->tenant_image)) {
                    if (File::exists($tenant->tenant_image)) {
                        File::delete($tenant->tenant_image);
                    }
                    $tenant_image = $this->uploadImage($request->file('tenant_image'), 'tenantimage');
                } else {
                    $tenant_image = $tenant->tenant_image;
                }

                if (!empty($request->tenant_document)) {
                    if (File::exists($tenant->tenant_document)) {
                        File::delete($tenant->tenant_document);
                    }
                    $tenant_document = $this->uploadFile($request->file('tenant_document'), 'tenantdocument');
                } else {
                    $tenant_document = $tenant->tenant_document;
                }



                $tenant->update([
                    'rental_space_id' => $rentalspace->id,
                    'tenant_name' => $request->tenant_name,
                    'tenant_father_name' => $request->tenant_father_name,
                    'tenant_mother_name' => $request->tenant_mother_name,
                    'rent_for' => $request->rent_for,
                    'tenant_branch_name' => $request->tenant_branch_name,
                    'tenant_company_name' => $request->tenant_company_name,
                    'tenant_address' => $request->tenant_address,
                    'tenant_nid_no' => $request->tenant_nid_no,
                    'tenant_phone_number' => $request->tenant_phone_number,
                    'tenant_image' => $tenant_image,
                    'tenant_document' => $tenant_document,
                    'tenant_remarks' => $request->tenant_remarks,
                ]);
            }


            $fee = RentalSpaceFee::where('rental_space_id', $request->id)->first();

            if ($fee) {
                if (!empty($request->fee_document)) {
                    if (File::exists($fee->fee_document)) {
                        File::delete($fee->fee_document);
                    }
                    $fee_document = $this->uploadFile($request->file('fee_document'), 'feedocument');
                } else {
                    $fee_document = $fee->fee_document;
                }

                $fee->update([
                    'rental_space_id' => $rentalspace->id,
                    'fee_advance_amount' => $request->fee_advance_amount,
                    'fee_per_month_rental_cost' => $request->fee_per_month_rental_cost,
                    'fee_document' => $fee_document,
                    'agreement_validity_from' => $request->agreement_validity_from,
                    'agreement_validity_to' => $request->agreement_validity_to,
                    'fee_rent_payment_date' => $request->fee_rent_payment_date,
                    'fee_remarks' => $request->fee_remarks,
                ]);
            }


            $parking = RentalSpaceParkingFee::where('rental_space_id', $request->id)->first();

            if ($parking) {
                if (!empty($request->parking_document)) {
                    if (File::exists($parking->parking_document)) {
                        File::delete($parking->parking_document);
                    }
                    $parking_document = $this->uploadFile($request->file('parking_document'), 'parkingdocument');
                } else {
                    $parking_document = $parking->parking_document;
                }

                $parking->update([
                    'rental_space_id' => $rentalspace->id,
                    'parking_fee' => $request->parking_fee,
                    'fee_per_month_parking_cost' => $request->fee_per_month_parking_cost,
                    'parking_document' => $parking_document,
                    'vehicle_allowed_number' => $request->vehicle_allowed_number,
                    'parking_zone_size' => $request->parking_zone_size,
                    'parking_remarks' => $request->parking_remarks,
                ]);
            } else {
            }



            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Rental Info Updated successfully',
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
    public function citylist(Request $request)
    {
        try {

            $cities = RentalSpaceCity::select('id','name')->orderBy('id', 'desc')->get();

            return response()->json([
                'success' => true,
                'message' => "City list",
                'data' => $cities
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function rentalSpaceThanaPozip(Request $request)
    {
        try {
            $rentalSpaceThanaPozip = RentalSpaceThanaPoZip::
            where('rental_space_city_id',$request->id)
            ->with([
                'rentalSpaceCity:id,name'
            ])->select('id','rental_space_city_id', 'thana_name', 'po_name','zip_code')->orderBy('id', 'desc')->get();


            $data['rentalSpaceThanaPozip'] = $rentalSpaceThanaPozip;

            return response()->json([
                'success' => true,
                'message' => "Rental thana,postoffice,zip list",
                'data' => $data
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    public function getRentalSpaceDocument(Request $request)
    {
        try {
            $data['Rental_Space_Document']=RentalSpace::where('id', $request->id)->whereNotNull('rental_space_image')
                ->select('id','rental_space_image')
                ->get();
            $data['Owner_Information_Document']=RentalSpaceOwner::where('rental_space_id', $request->id)->whereNotNull('owner_document')
                ->select('id','owner_document')
                ->get();
            $data['Tenant_Information_Document']=RentalSpaceTenant::where('rental_space_id', $request->id)->whereNotNull('tenant_image')
                ->select('id','tenant_image')
                ->get();
            $data['Fee_Detail_Document']=RentalSpaceFee::where('rental_space_id', $request->id)->whereNotNull('fee_document')
                ->select('id','fee_document')
                ->get();
            $data['Parking_Fee_Detail_Document']=RentalSpaceParkingFee::where('rental_space_id', $request->id)->whereNotNull('parking_document')
                ->select('id','parking_document')
                ->get();
            $data['Others_Documents']=RentalSpaceDocument::where('rental_space_id', $request->id)
                ->select('id', 'document')
                ->get();
            return response()->json([
                'success' => true,
                'message' => "Rental Space Documents",
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
            if (is_array($request->document)) {
                foreach ($request->document as $documentData) {
                    $file = $documentData['document'] ?? null;
                    if ($file !== 'null') {
                        $file_type = $file->getClientOriginalExtension();
                        if ($file_type != "jpg" && $file_type != "png" && $file_type != "jpeg" && $file_type != "svg") {
                            $document = $this->uploadFile($file, 'rental_space_document');
                        } else {
                            $document = $this->uploadImage($file, 'rental_space_document');
                        }
                    }
                    $other_document = array(
                        'rental_space_id' => $documentData['id'],
                        'document' => $document,
                    );
                    $data['document'] = RentalSpaceDocument::create($other_document);
                }
            }
            return response()->json([
                'success' => true,
                'message' => "Rental Space Documents Uploaded Successfully",
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
            $others_document = RentalSpaceDocument::find($request->id);
            if ($others_document) {
                if (File::exists($others_document->document)) {
                    File::delete($others_document->document);
                }
            }
            $data= $others_document->delete();
            return response()->json([
                'success' => true,
                'message' => "Rental Space Others Document Deleted Successfully",
                'data' => $data
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 200);
        }
    }
    public function deleteRentalSpaceDocument(Request $request)
    {
        try {
            $rental_space_document = RentalSpace::find($request->id);
            if ($rental_space_document) {
                if (File::exists($rental_space_document->rental_space_image)) {
                    File::delete($rental_space_document->rental_space_image);
                }
            }
            $data = RentalSpace::where('id', $request->id)->update([
                'rental_space_image' => null,
            ]);
            return response()->json([
                'success' => true,
                'message' => "Rental Space Image Deleted Successfully",
                'data' => $data
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 200);
        }
    }
    public function deleteOwnerDocument(Request $request)
    {
        try {
            $owner_document = RentalSpaceOwner::find($request->id);
            if ($owner_document) {
                if (File::exists($owner_document->owner_document)) {
                    File::delete($owner_document->owner_document);
                }
            }
            $data = RentalSpaceOwner::where('id', $request->id)->update([
                'owner_document' => null,
            ]);
            return response()->json([
                'success' => true,
                'message' => "Owner Document Deleted Successfully",
                'data' => $data
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 200);
        }
    }
    public function deleteTenantDocument(Request $request)
    {
        try {
            $tenant_document = RentalSpaceTenant::find($request->id);
            if ($tenant_document) {
                if (File::exists($tenant_document->tenant_image)) {
                    File::delete($tenant_document->tenant_image);
                }
            }
            $data = RentalSpaceTenant::where('id', $request->id)->update([
                'tenant_image' => null,
            ]);
            return response()->json([
                'success' => true,
                'message' => "Tenant Image Deleted Successfully",
                'data' => $data
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 200);
        }
    }
    public function deleteFeeDocument(Request $request)
    {
        try {
            $fee_document = RentalSpaceFee::find($request->id);
            if ($fee_document) {
                if (File::exists($fee_document->fee_document)) {
                    File::delete($fee_document->fee_document);
                }
            }
            $data = RentalSpaceFee::where('id', $request->id)->update([
                'fee_document' => null,
            ]);
            return response()->json([
                'success' => true,
                'message' => "Fee Document Deleted Successfully",
                'data' => $data
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 200);
        }
    }
    public function deleteParkingFeeDocument(Request $request)
    {
        try {
            $parking_fee_document = RentalSpaceParkingFee::find($request->id);
            if ($parking_fee_document) {
                if (File::exists($parking_fee_document->parking_document)) {
                    File::delete($parking_fee_document->parking_document);
                }
            }
            $data = RentalSpaceParkingFee::where('id', $request->id)->update([
                'parking_document' => null,
            ]);
            return response()->json([
                'success' => true,
                'message' => "Parking Fee Document Deleted Successfully",
                'data' => $data
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 200);
        }
    }

    public function getRentalSpaceName(Request $request)
    {
        try {

            $data = RentalResource::collection(RentalSpace::with('RentalSpaceOwner')
            ->orderBy('id', 'desc')->get());

            return response()->json([
                'success' => true,
                'message' => "Rental Space Name list",
                'data' => $data
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

}
