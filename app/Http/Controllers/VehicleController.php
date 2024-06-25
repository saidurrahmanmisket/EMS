<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\VehicleClassType;
use App\Models\VehicleFitnessValidationPeriod;
use App\Models\VehicleFreeServicingValidationPeriod;
use App\Models\VehicleInsuranceValidationPeriod;
use App\Models\VehiclePreviousOwnerOrSellerInformation;
use App\Models\VehiclePurchaseTimeVehiclePaymentInformation;
use App\Models\VehicleRegistrationInformation;
use App\Models\VehicleSubClassType;
use App\Models\VehicleTaxTokenValidationPeriod;
use App\Traits\UploadTraits;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;


class VehicleController extends Controller
{
    use UploadTraits;

    public function add_vehicle(Request $request)
    {
//        Log::debug($request->all());
        $validator = Validator::make($request->all(), [
            'company_id' => 'required',
            'branch_id' => 'required',
            'vehicle_code' => 'required',
//            'vehicle_name' => 'required',
            'vehicle_type_id' => 'required',
            'vehicle_class_type_id' => 'required',
            'vehicle_sub_class_type_id' => 'required',
            'vehicle_color_id' => 'required',
            'vehicle_brand_name_id' => 'required',
            'vehicle_cc' => 'required',
            'vehicle_class_letter_id' => 'required',
            'vehicle_no' => 'required',
            'weight_capacity' => 'required',
            'lifting_capacity' => 'required',
            'vehicle_fuel_type_id' => 'required',
            'manufacturer_year' => 'required',
            'purchase_date_mileage' => 'required',
            'vehicle_registration_date' => 'required',
//            'vehicle_image_or_docs'=>'required',
//            'vehicle_remark'=>'required',
            'previous_owner_address' => 'required',
            'phone_number' => 'required',
//            'vehicle_previous_owner_or_seller_information_image_or_docs'=>'required',
//            'vehicle_previous_owner_or_seller_information_remark'=>'required',
//            'vehicle_previous_owner_or_seller_id'=>'required',
//            'vehicle_dealer_or_seller_showroom_id'=>'required',
            'vehicle_price' => 'required',
            'down_payment' => 'required',
            'installment_number_id' => 'required',
            'first_payment_date' => 'required',
            'provable_installment_finish_date' => 'required',
            'installment_amount' => 'required',
            'total_due' => 'required',
//            'vehicle_purchase_time_vehicle_payment_information_image_or_docs'=>'required',
//            'vehicle_purchase_time_vehicle_payment_information_remark'=>'required',
            'vehicle_registration_information_registration_date' => 'required',
            'vehicle_buying_condition_id' => 'required',
            'tin_certificate' => 'required',
            'vehicle_registration_type_id' => 'required',
            'registration_fee' => 'required',
            'registration_invoice_number' => 'required',
            'sale_certificate' => 'required',
            'invoice_for_payment_of_vat' => 'required',
            'vat_payment_receipt' => 'required',
            'musac_1' => 'required',
            'musac_11_a_or_vat' => 'required',
            'body_vat_invoice' => 'required',
            'receipt_of_deposit_of_applicable_registration_fee' => 'required',
            'registered_new_owner_name' => 'required',
            'new_owner_phone_number' => 'required',
            'new_owner_nid_number' => 'required',
            'ownership_transfer_fee' => 'required',
            'chassis_number' => 'required',
            'engine_no' => 'required',
            'model_no' => 'required',
            'tire_size' => 'required',
//            'vehicle_registration_information_image_or_docs'=>'required',
//            'vehicle_registration_information_remark'=>'required',
        ]);


        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => "Validator Error.",
                'error' => $validator->errors(),
            ], 200);
        }


        \DB::beginTransaction();

        try {

            $data = [];

            //Vehicle table start

            $vehicle_image_or_docs = null;

            if ($request->hasFile('vehicle_image_or_docs')) {
                $vehicle_image_or_docs = $this->uploadFile($request->file('vehicle_image_or_docs'), 'vehicle_images_or_docs');
            }

//               $vehicle_type_array=Vehicle::$vehicle_type_array;
//
//               return  Service1::process_associative_array_and_returning_that_array_like_retrieving_records_from_table($vehicle_type_array);

            $vehicle_class_type_id = $request->vehicle_class_type_id;

            $vehicle_class_type = VehicleClassType::find($vehicle_class_type_id);

            if (!$vehicle_class_type) {
                return response()->json([
                    'success' => false,
                    'message' => "Not Find Any Vehicle Class Type Record With This Id " . $vehicle_class_type_id,
                ], 200);
            }

            $vehicle_class_type_name = $vehicle_class_type->name;

            $vehicle_sub_class_type_id = $request->vehicle_sub_class_type_id;

            $vehicle_sub_class_type = VehicleSubClassType::find($vehicle_sub_class_type_id);

            if (!$vehicle_sub_class_type) {
                return response()->json([
                    'success' => false,
                    'message' => "Not Find Any Vehicle Sub Class Type Record With This Id " . $vehicle_sub_class_type_id,
                ], 200);
            }

            $vehicle_sub_class_type_name = $vehicle_sub_class_type->name;

            $vehicle_no = $request->vehicle_no;

            $vehicle_code = $request->vehicle_code;

            $vehicle_name = $vehicle_class_type_name . '-' . $vehicle_sub_class_type_name . '-' . $vehicle_no . '-' . $vehicle_code;

//            $vehicle_color_array=Vehicle::$vehicle_color_array;
//
//            Service1::process_associative_array_and_returning_that_array_like_retrieving_records_from_table($vehicle_color_array);

//            $brand_name_array=Vehicle::$brand_name_array;
//            Service1::process_associative_array_and_returning_that_array_like_retrieving_records_from_table($brand_name_array);

//            $vehicle_class_letter_array=Vehicle::$vehicle_class_letter_array;
//
//            Service1::process_associative_array_and_returning_that_array_like_retrieving_records_from_table($vehicle_class_letter_array);

//              $vehicle_fuel_type_array=Vehicle::$vehicle_fuel_type_array;
//
//              Service1::process_associative_array_and_returning_that_array_like_retrieving_records_from_table($vehicle_fuel_type_array);


            $vehicle = Vehicle::create([
                'date' => date('Y-m-d'),
                'company_id' => $request->company_id,
                'branch_id' => $request->branch_id,
                'vehicle_code' => $request->vehicle_code,
                'vehicle_name' => $vehicle_name,
                'vehicle_type_id' => $request->vehicle_type_id,
                'vehicle_class_type_id' => $vehicle_class_type_id,
                'vehicle_sub_class_type_id' => $vehicle_sub_class_type_id,
                'vehicle_color_id' => $request->vehicle_color_id,
                'vehicle_brand_name_id' => $request->vehicle_brand_name_id,
                'vehicle_cc' => $request->vehicle_cc,
                'vehicle_class_letter_id' => $request->vehicle_class_letter_id,
                'vehicle_no' => $request->vehicle_no,
                'weight_capacity' => $request->weight_capacity,
                'lifting_capacity' => $request->lifting_capacity,
                'vehicle_fuel_type_id' => $request->vehicle_fuel_type_id,
                'manufacturer_year' => $request->manufacturer_year,
                'purchase_date_mileage' => $request->purchase_date_mileage,
                'registration_date' => $request->vehicle_registration_date,
                'image_or_docs' => $vehicle_image_or_docs,
                'remark' => $request->vehicle_remark,
            ]);


            $vehicle_id = null;

            if ($vehicle) {
                $vehicle_id = $vehicle->id;
            }

            if ($vehicle_id == null) {
                \DB::rollback();
                return response()->json([
                    'success' => false,
                    'message' => "Something Went Wrong & Vehicle Record is Not Created.",
                ], 200);
            }

            $data['vehicle & vehicle details'] = $vehicle;

            //Vehicle table end

            //vehicle_previous_owner_or_seller_informations table start
            $vehicle_previous_owner_or_seller_id = $request->vehicle_previous_owner_or_seller_id;
            $vehicle_dealer_or_seller_showroom_id = $request->vehicle_dealer_or_seller_showroom_id;
            if ($vehicle_previous_owner_or_seller_id && $vehicle_dealer_or_seller_showroom_id) {
                \DB::rollback();
                return response()->json([
                    'success' => false,
                    'message' => "Please Select Previous Owner Or Show Room Name Not Both At a Time",
                ], 200);

            }

            $vehicle_previous_owner_or_seller_information_image_or_docs = null;

            if ($request->hasFile('vehicle_previous_owner_or_seller_information_image_or_docs')) {
                $vehicle_previous_owner_or_seller_information_image_or_docs = $this->uploadFile($request->file('vehicle_previous_owner_or_seller_information_image_or_docs'), 'vehicle_previous_owner_or_seller_information_images_or_docs');
            }

            $vehicle_previous_owner_or_seller_information = VehiclePreviousOwnerOrSellerInformation::create([
                'previous_owner_address' => $request->previous_owner_address,
                'phone_number' => $request->phone_number,
                'image_or_docs' => $vehicle_previous_owner_or_seller_information_image_or_docs,
                'remark' => $request->phone_number,
                'vehicle_previous_owner_or_seller_id' => $request->vehicle_previous_owner_or_seller_id,
                'vehicle_dealer_or_seller_showroom_id' => $request->vehicle_dealer_or_seller_showroom_id,
                'vehicle_id' => $vehicle_id,
            ]);

            if (!$vehicle_previous_owner_or_seller_information) {
                \DB::rollback();
                return response()->json([
                    'success' => false,
                    'message' => "Something Went Wrong & Vehicle PreviousOwner Or Seller Information Record is Not Created.",
                ], 200);
            }

            $data['vehicle_previous_owner_or_seller_information'] = $vehicle_previous_owner_or_seller_information;

            //vehicle_previous_owner_or_seller_informations table end


            //vehicle_purchase_time_vehicle_payment_informations table start

//            $installment_number_array=Vehicle::$installment_number_array;
//
//            Service1::process_associative_array_and_returning_that_array_like_retrieving_records_from_table($installment_number_array);


            $vehicle_purchase_time_vehicle_payment_information_image_or_docs = null;

            if ($request->hasFile('vehicle_purchase_time_vehicle_payment_information_image_or_docs')) {
                $vehicle_purchase_time_vehicle_payment_information_image_or_docs = $this->uploadFile($request->file('vehicle_purchase_time_vehicle_payment_information_image_or_docs'), 'vehicle_purchase_time_vehicle_payment_information_images_or_docs');
            }

            $vehicle_purchase_time_vehicle_payment_information = VehiclePurchaseTimeVehiclePaymentInformation::create([
                'vehicle_price' => $request->vehicle_price,
                'down_payment' => $request->down_payment,
                'installment_number_id' => $request->installment_number_id,
                'first_payment_date' => $request->first_payment_date,
                'provable_installment_finish_date' => $request->provable_installment_finish_date,
                'installment_amount' => $request->installment_amount,
                'total_due' => $request->total_due,
                'image_or_docs' => $vehicle_purchase_time_vehicle_payment_information_image_or_docs,
                'remark' => $request->vehicle_purchase_time_vehicle_payment_information_remark,
                'vehicle_id' => $vehicle_id,
            ]);


            if (!$vehicle_purchase_time_vehicle_payment_information) {
                \DB::rollback();
                return response()->json([
                    'success' => false,
                    'message' => "Something Went Wrong & Vehicle Purchase Time Vehicle Payment Information Record is Not Created.",
                ], 200);
            }

            $data['vehicle_purchase_time_vehicle_payment_information'] = $vehicle_purchase_time_vehicle_payment_information;

            //vehicle_purchase_time_vehicle_payment_informations table end

            //vehicle registration information table start

//            $registration_type_array=Vehicle::$registration_type_array;
//
//            Service1::process_associative_array_and_returning_that_array_like_retrieving_records_from_table($registration_type_array);


//            $vehicle_buying_condition_array=Vehicle::$vehicle_buying_condition_array;
//
//            Service1::process_associative_array_and_returning_that_array_like_retrieving_records_from_table($vehicle_buying_condition_array);

            $vehicle_registration_information_image_or_docs = null;

            if ($request->hasFile('vehicle_registration_information_image_or_docs')) {
                $vehicle_registration_information_image_or_docs = $this->uploadFile($request->file('vehicle_registration_information_image_or_docs'), 'vehicle_registration_information_images_or_docs');
            }

            $vehicle_registration_information = VehicleRegistrationInformation::create([
                'registration_date' => $request->vehicle_registration_information_registration_date,
                'vehicle_buying_condition_id' => $request->vehicle_buying_condition_id,
                'tin_certificate' => $request->tin_certificate,
                'vehicle_registration_type_id' => $request->vehicle_registration_type_id,
                'registration_fee' => $request->registration_fee,
                'registration_invoice_number' => $request->registration_invoice_number,
                'sale_certificate' => $request->sale_certificate,
                'invoice_for_payment_of_vat' => $request->invoice_for_payment_of_vat,
                'vat_payment_receipt' => $request->vat_payment_receipt,
                'musac_1' => $request->musac_1,
                'musac_11_a_or_vat' => $request->musac_11_a_or_vat,
                'body_vat_invoice' => $request->body_vat_invoice,
                'receipt_of_deposit_of_applicable_registration_fee' => $request->receipt_of_deposit_of_applicable_registration_fee,
                'registered_new_owner_name' => $request->registered_new_owner_name,
                'new_owner_phone_number' => $request->new_owner_phone_number,
                'new_owner_nid_number' => $request->new_owner_nid_number,
                'ownership_transfer_fee' => $request->ownership_transfer_fee,
                'chassis_number' => $request->chassis_number,
                'engine_no' => $request->engine_no,
                'model_no' => $request->model_no,
                'tire_size' => $request->tire_size,
                'image_or_docs' => $vehicle_registration_information_image_or_docs,
                'remark' => $request->vehicle_registration_information_remark,
                'vehicle_id' => $vehicle_id
            ]);


            if (!$vehicle_registration_information) {
                \DB::rollback();
                return response()->json([
                    'success' => false,
                    'message' => "Something Went Wrong & Vehicle Registration Information Record is Not Created.",
                ], 200);
            }

            $data['vehicle_registration_information'] = $vehicle_registration_information;

            //vehicle registration information table end

            //vehicle_tax_token_validation_periods table start
            $vehicle_tax_token_validation_periods_array = array();
            if (is_array($request->vehicle_tax_token_validation_periods)) {
                foreach ($request->vehicle_tax_token_validation_periods as $vehicle_tax_token_validation_period) {
                    $vehicle_tax_token_validation_period_image_or_doc = null;
                    $vehicle_tax_token_validation_period_image_or_doc = $vehicle_tax_token_validation_period['vehicle_tax_token_validation_period_image_or_doc'];

                    $vehicle_tax_token_validation_period_image_or_doc_url = null;

                    if ($vehicle_tax_token_validation_period_image_or_doc !== null) {
                        $vehicle_tax_token_validation_period_image_or_doc_url = $this->uploadFile($vehicle_tax_token_validation_period_image_or_doc, 'vehicle_tax_token_validation_period_images_or_docs');
                    }

                    $result = VehicleTaxTokenValidationPeriod::create([
                        'from' => $vehicle_tax_token_validation_period['from'],
                        'to' => $vehicle_tax_token_validation_period['to'],
                        'image_or_docs' => $vehicle_tax_token_validation_period_image_or_doc_url,
                        'vehicle_id' => $vehicle_id
                    ]);

                    array_push($vehicle_tax_token_validation_periods_array, $result);
                }
            }
            $data['vehicle_tax_token_validation_periods'] = $vehicle_tax_token_validation_periods_array;

            //vehicle_tax_token_validation_periods table end

            //vehicle_fitness_validation_periods table start
            $vehicle_fitness_validation_periods_array = array();
            if (is_array($request->vehicle_fitness_validation_periods)) {
                foreach ($request->vehicle_fitness_validation_periods as $vehicle_fitness_validation_period) {
                    $vehicle_fitness_validation_period_image_or_doc = null;
                    $vehicle_fitness_validation_period_image_or_doc = $vehicle_fitness_validation_period['vehicle_fitness_validation_period_image_or_doc'];

                    $vehicle_fitness_validation_period_image_or_doc_url = null;

                    if ($vehicle_fitness_validation_period_image_or_doc !== null) {
                        $vehicle_fitness_validation_period_image_or_doc_url = $this->uploadFile($vehicle_fitness_validation_period_image_or_doc, 'vehicle_fitness_validation_period_images_or_docs');
                    }

                    $result = VehicleFitnessValidationPeriod::create([
                        'from' => $vehicle_fitness_validation_period['from'],
                        'to' => $vehicle_fitness_validation_period['to'],
                        'image_or_docs' => $vehicle_fitness_validation_period_image_or_doc_url,
                        'vehicle_id' => $vehicle_id
                    ]);

                    array_push($vehicle_fitness_validation_periods_array, $result);
                }
            }
            $data['vehicle_fitness_validation_periods'] = $vehicle_fitness_validation_periods_array;

            //vehicle_fitness_validation_periods table end

            //vehicle_insurance_validation_periods table start
            $vehicle_insurance_validation_periods_array = array();
            if (is_array($request->vehicle_insurance_validation_periods)) {
                foreach ($request->vehicle_insurance_validation_periods as $vehicle_insurance_validation_period) {
                    $vehicle_insurance_validation_period_image_or_doc = null;
                    $vehicle_insurance_validation_period_image_or_doc = $vehicle_insurance_validation_period['vehicle_insurance_validation_period_image_or_doc'];

                    $vehicle_insurance_validation_period_image_or_doc_url = null;

                    if ($vehicle_insurance_validation_period_image_or_doc !== null) {
                        $vehicle_insurance_validation_period_image_or_doc_url = $this->uploadFile($vehicle_insurance_validation_period_image_or_doc, 'vehicle_insurance_validation_period_images_or_docs');
                    }

                    $result = VehicleInsuranceValidationPeriod::create([
                        'from' => $vehicle_insurance_validation_period['from'],
                        'to' => $vehicle_insurance_validation_period['to'],
                        'image_or_docs' => $vehicle_insurance_validation_period_image_or_doc_url,
                        'vehicle_id' => $vehicle_id
                    ]);

                    array_push($vehicle_insurance_validation_periods_array, $result);
                }
            }
            $data['vehicle_insurance_validation_periods'] = $vehicle_insurance_validation_periods_array;

            //vehicle_insurance_validation_periods table end

            //vehicle_free_servicing_validation_periods table start
            $vehicle_free_servicing_validation_periods_array = array();
            if (is_array($request->vehicle_free_servicing_validation_periods)) {
                foreach ($request->vehicle_free_servicing_validation_periods as $vehicle_free_servicing_validation_period) {
                    $vehicle_free_servicing_validation_period_image_or_doc = null;
                    $vehicle_free_servicing_validation_period_image_or_doc = $vehicle_free_servicing_validation_period['vehicle_free_servicing_validation_period_image_or_doc'];

                    $vehicle_free_servicing_validation_period_image_or_doc_url = null;

                    if ($vehicle_free_servicing_validation_period_image_or_doc !== null) {
                        $vehicle_free_servicing_validation_period_image_or_doc_url = $this->uploadFile($vehicle_free_servicing_validation_period_image_or_doc, 'vehicle_free_servicing_validation_period_images_or_docs');
                    }

                    $result = VehicleFreeServicingValidationPeriod::create([
                        'from' => $vehicle_free_servicing_validation_period['from'],
                        'to' => $vehicle_free_servicing_validation_period['to'],
                        'image_or_docs' => $vehicle_free_servicing_validation_period_image_or_doc_url,
                        'vehicle_id' => $vehicle_id
                    ]);

                    array_push($vehicle_free_servicing_validation_periods_array, $result);
                }
            }
            $data['vehicle_free_servicing_validation_periods'] = $vehicle_free_servicing_validation_periods_array;

            //vehicle_free_servicing_validation_periods table end

            \DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Vehicle is Created successfully",
            ], 200);

        } catch (\Exception $e) {
            \DB::rollback();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 200);
        }
    }

    public function vehicle_single_view_or_edit(Request $request)
    {
        try {

            $vehicle = Vehicle::find($request->id);

            if (!$vehicle) {
                return response()->json([
                    'success' => false,
                    'message' => "Not Find Any Vehicle With Id " . $request->id
                ], 200);
            }

            $vehicle = Vehicle::with([
                'company:id,name,company_code',
                'branch:id,name',
                'vehicle_class_type:id,name',
                'vehicle_sub_class_type:id,name',
                'vehicle_previous_owner_or_seller_information' => [
                    'vehicle_previous_owner_or_seller',
                    'vehicle_dealer_or_seller_showroom',
                ],
                'vehicle_purchase_time_vehicle_payment_information',
                'vehicle_registration_information',
                'vehicle_tax_token_validation_periods',
                'vehicle_fitness_validation_periods',
                'vehicle_insurance_validation_periods',
                'vehicle_free_servicing_validation_periods'
            ])->find($request->id);

            $data['vehicle'] = $vehicle;


            return response()->json([
                'success' => true,
                'message' => "Vehicle Info",
                'data' => $data
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 200);
        }
    }

    public function vehicle_list(Request $request)
    {
        try {
            $vehicle = Vehicle::with([
                'company:id,name,company_code',
                'branch:id,name',
                'vehicle_registration_information'
            ])
                ->orderby('id', 'desc')
                ->select();

            if ($request->per_page) {
                $per_page = $request->per_page;
            } else {
                $per_page = 20;
            }

            $vehicle = $vehicle->paginate($per_page);
            $vehicle = $vehicle->appends($request->all());


            foreach ($vehicle as $key => $value) {
                Arr::forget($value, [
                    'date',
                    'company_id',
                    'branch_id',
                    'company.company_code',
                    'vehicle_name',
                    'vehicle_type_id',
                    'vehicle_class_type_id',
                    'vehicle_sub_class_type_id',
                    'vehicle_color_id',
                    'vehicle_brand_name_id',
                    'vehicle_cc',
                    'vehicle_class_letter_id',
                    'weight_capacity',
                    'lifting_capacity',
                    'vehicle_fuel_type_id',
                    'manufacturer_year',
                    'purchase_date_mileage',
                    'registration_date',
                    'image_or_docs',
                    'remark',
                    'is_active',
                    'created_at',
                    'updated_at',
                    'vehicle_registration_information.registration_date',
                    'vehicle_registration_information.vehicle_buying_condition_id',
                    'vehicle_registration_information.tin_certificate',
                    'vehicle_registration_information.vehicle_registration_type_id',
                    'vehicle_registration_information.registration_fee',
                    'vehicle_registration_information.registration_invoice_number',
                    'vehicle_registration_information.sale_certificate',
                    'vehicle_registration_information.invoice_for_payment_of_vat',
                    'vehicle_registration_information.vat_payment_receipt',
                    'vehicle_registration_information.musac_1',
                    'vehicle_registration_information.musac_11_a_or_vat',
                    'vehicle_registration_information.musac-11',
                    'vehicle_registration_information.body_vat_invoice',
                    'vehicle_registration_information.receipt_of_deposit_of_applicable_registration_fee',
                    'vehicle_registration_information.new_owner_nid_number',
                    'vehicle_registration_information.ownership_transfer_fee',
                    'vehicle_registration_information.body_vat_invoice',
                    'vehicle_registration_information.receipt_of_deposit_of_applicable_registration_fee',
                    'vehicle_registration_information.new_owner_nid_number',
                    'vehicle_registration_information.ownership_transfer_fee',
                    'vehicle_registration_information.model_no',
                    'vehicle_registration_information.tire_size',
                    'vehicle_registration_information.image_or_docs',
                    'vehicle_registration_information.remark',
                    'vehicle_registration_information.created_at',
                    'vehicle_registration_information.updated_at',
                    'vehicle_registration_information.vehicle_id'
                ]);
            }

            $data['vehicles'] = $vehicle;
            return response()->json([
                'success' => true,
                'message' => "Vehicle List",
                'data' => $data
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 200);
        }
    }

    public function vehicle_update(Request $request){
        $validator = Validator::make($request->all(), [
            'id'=>'required',
            'company_id' => 'required',
            'branch_id' => 'required',
            'vehicle_code' => 'required',
//            'vehicle_name' => 'required',
            'vehicle_type_id' => 'required',
            'vehicle_class_type_id' => 'required',
            'vehicle_sub_class_type_id' => 'required',
            'vehicle_color_id' => 'required',
            'vehicle_brand_name_id' => 'required',
            'vehicle_cc' => 'required',
            'vehicle_class_letter_id' => 'required',
            'vehicle_no' => 'required',
            'weight_capacity' => 'required',
            'lifting_capacity' => 'required',
            'vehicle_fuel_type_id' => 'required',
            'manufacturer_year' => 'required',
            'purchase_date_mileage' => 'required',
            'vehicle_registration_date' => 'required',
//            'vehicle_image_or_docs'=>'required',
//            'vehicle_remark'=>'required',
            'previous_owner_address' => 'required',
            'phone_number' => 'required',
//            'vehicle_previous_owner_or_seller_information_image_or_docs'=>'required',
//            'vehicle_previous_owner_or_seller_information_remark'=>'required',
//            'vehicle_previous_owner_or_seller_id'=>'required',
//            'vehicle_dealer_or_seller_showroom_id'=>'required',
            'vehicle_price' => 'required',
            'down_payment' => 'required',
            'installment_number_id' => 'required',
            'first_payment_date' => 'required',
            'provable_installment_finish_date' => 'required',
            'installment_amount' => 'required',
            'total_due' => 'required',
//            'vehicle_purchase_time_vehicle_payment_information_image_or_docs'=>'required',
//            'vehicle_purchase_time_vehicle_payment_information_remark'=>'required',
            'vehicle_registration_information_registration_date' => 'required',
            'vehicle_buying_condition_id' => 'required',
            'tin_certificate' => 'required',
            'vehicle_registration_type_id' => 'required',
            'registration_fee' => 'required',
            'registration_invoice_number' => 'required',
            'sale_certificate' => 'required',
            'invoice_for_payment_of_vat' => 'required',
            'vat_payment_receipt' => 'required',
            'musac_1' => 'required',
            'musac_11_a_or_vat' => 'required',
            'body_vat_invoice' => 'required',
            'receipt_of_deposit_of_applicable_registration_fee' => 'required',
            'registered_new_owner_name' => 'required',
            'new_owner_phone_number' => 'required',
            'new_owner_nid_number' => 'required',
            'ownership_transfer_fee' => 'required',
            'chassis_number' => 'required',
            'engine_no' => 'required',
            'model_no' => 'required',
            'tire_size' => 'required',
//            'vehicle_registration_information_image_or_docs'=>'required',
//            'vehicle_registration_information_remark'=>'required',
        ]);


        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => "Validator Error.",
                'error' => $validator->errors(),
            ], 200);
        }

        \DB::beginTransaction();

        try{

            $vehicle_id=$request->id;

            $vehicle = Vehicle::find($request->id);

            if(!$vehicle){
                \DB::rollback();
                return response()->json([
                    'success' => false,
                    'message' => "Not Find Any Vehicle With Id ".$vehicle_id,
                ], 200);
            }


           //Vehicle update start

            if ($request->vehicle_image_or_docs !== null) {
                if (File::exists($vehicle->image_or_docs)) {
                    File::delete($vehicle->image_or_docs);
                }

                $vehicle_image_or_docs = $this->uploadFile($request->file('vehicle_image_or_docs'), 'vehicle_images_or_docs');
            }
            else{
                $vehicle_image_or_docs = $vehicle->image_or_docs;
                $subject = $vehicle_image_or_docs ;
                $search = url('/').'/'; ;
                $vehicle_image_or_docs = str_replace($search, '', $subject) ;
            }


            $vehicle_class_type_id = $request->vehicle_class_type_id;

            $vehicle_class_type = VehicleClassType::find($vehicle_class_type_id);

            if (!$vehicle_class_type) {
                \DB::rollback();
                return response()->json([
                    'success' => false,
                    'message' => "Not Find Any Vehicle Class Type Record With This Id " . $vehicle_class_type_id,
                ], 200);
            }

            $vehicle_class_type_name = $vehicle_class_type->name;

            $vehicle_sub_class_type_id = $request->vehicle_sub_class_type_id;

            $vehicle_sub_class_type = VehicleSubClassType::find($vehicle_sub_class_type_id);

            if (!$vehicle_sub_class_type) {
                \DB::rollback();
                return response()->json([
                    'success' => false,
                    'message' => "Not Find Any Vehicle Sub Class Type Record With This Id " . $vehicle_sub_class_type_id,
                ], 200);
            }

            $vehicle_sub_class_type_name = $vehicle_sub_class_type->name;

            $vehicle_no = $request->vehicle_no;

            $vehicle_code = $request->vehicle_code;

            $vehicle_name = $vehicle_class_type_name . '-' . $vehicle_sub_class_type_name . '-' . $vehicle_no . '-' . $vehicle_code;

            $is_updated = Vehicle::where('id', $vehicle_id)->update([
                'company_id' => $request->company_id,
                'branch_id' => $request->branch_id,
                'vehicle_code' => $request->vehicle_code,
                'vehicle_name' => $vehicle_name,
                'vehicle_type_id' => $request->vehicle_type_id,
                'vehicle_class_type_id' => $vehicle_class_type_id,
                'vehicle_sub_class_type_id' => $vehicle_sub_class_type_id,
                'vehicle_color_id' => $request->vehicle_color_id,
                'vehicle_brand_name_id' => $request->vehicle_brand_name_id,
                'vehicle_cc' => $request->vehicle_cc,
                'vehicle_class_letter_id' => $request->vehicle_class_letter_id,
                'vehicle_no' => $request->vehicle_no,
                'weight_capacity' => $request->weight_capacity,
                'lifting_capacity' => $request->lifting_capacity,
                'vehicle_fuel_type_id' => $request->vehicle_fuel_type_id,
                'manufacturer_year' => $request->manufacturer_year,
                'purchase_date_mileage' => $request->purchase_date_mileage,
                'registration_date' => $request->vehicle_registration_date,
                'image_or_docs' => $vehicle_image_or_docs,
                'remark' => $request->vehicle_remark,
            ]);

            if(!$is_updated){
                \DB::rollback();
                return response()->json([
                    'success' => false,
                    'message' => "Not Possible to Update Vehicle"
                ], 200);
            }

            //Vehicle update end

           // vehicle_previous_owner_or_seller_informations update is start

            $vehicle_previous_owner_or_seller_information=VehiclePreviousOwnerOrSellerInformation::where('vehicle_id', $vehicle_id)->first();

            if(!$vehicle_previous_owner_or_seller_information){
                \DB::rollback();
                return response()->json([
                    'success' => false,
                    'message' => "Not find vehicle previous owner or seller information with vehicle id ".$vehicle_id
                ], 200);
            }


            if ($request->vehicle_previous_owner_or_seller_information_image_or_docs !== null) {
                if (File::exists($vehicle_previous_owner_or_seller_information->image_or_docs)) {
                    File::delete($vehicle_previous_owner_or_seller_information->image_or_docs);
                }
                $vehicle_previous_owner_or_seller_information_image_or_docs = $this->uploadFile($request->file('vehicle_previous_owner_or_seller_information_image_or_docs'), 'vehicle_previous_owner_or_seller_information_images_or_docs');
            }
            else{
                $vehicle_previous_owner_or_seller_information_image_or_docs = $vehicle_previous_owner_or_seller_information->image_or_docs;
                $subject = $vehicle_previous_owner_or_seller_information_image_or_docs ;
                $search = url('/').'/'; ;
                $vehicle_previous_owner_or_seller_information_image_or_docs = str_replace($search, '', $subject) ;
            }

            $is_updated = $vehicle_previous_owner_or_seller_information->update([
                'previous_owner_address' => $request->previous_owner_address,
                'phone_number' => $request->phone_number,
                'image_or_docs' => $vehicle_previous_owner_or_seller_information_image_or_docs,
                'remark' => $request->phone_number,
                'vehicle_previous_owner_or_seller_id' => $request->vehicle_previous_owner_or_seller_id,
                'vehicle_dealer_or_seller_showroom_id' => $request->vehicle_dealer_or_seller_showroom_id,
                'vehicle_id' => $vehicle_id,
            ]);

            if(!$is_updated){
                \DB::rollback();
                return response()->json([
                    'success' => false,
                    'message' => "Not Possible to Update Vehicle Previous Owner or Seller Information"
                ], 200);
            }
            // vehicle_previous_owner_or_seller_informations update is end


            //vehicle_purchase_time_vehicle_payment_informations start
            $vehicle_purchase_time_vehicle_payment_information=VehiclePurchaseTimeVehiclePaymentInformation::where('vehicle_id', $vehicle_id)->first();

            if(!$vehicle_purchase_time_vehicle_payment_information){
                \DB::rollback();
                return response()->json([
                    'success' => false,
                    'message' => "Not find vehicle purchase time vehicle payment information with vehicle id ".$vehicle_id
                ], 200);
            }

            if ($request->vehicle_purchase_time_vehicle_payment_information_image_or_docs !== null) {
                if (File::exists($vehicle_purchase_time_vehicle_payment_information->image_or_docs)) {
                    File::delete($vehicle_purchase_time_vehicle_payment_information->image_or_docs);
                }
                $vehicle_purchase_time_vehicle_payment_information_image_or_docs = $this->uploadFile($request->file('vehicle_purchase_time_vehicle_payment_information_image_or_docs'), 'vehicle_purchase_time_vehicle_payment_information_images_or_docs');
            }
            else{
                $vehicle_purchase_time_vehicle_payment_information_image_or_docs = $vehicle_purchase_time_vehicle_payment_information->image_or_docs;
                $subject = $vehicle_purchase_time_vehicle_payment_information_image_or_docs ;
                $search = url('/').'/'; ;
                $vehicle_purchase_time_vehicle_payment_information_image_or_docs = str_replace($search, '', $subject) ;
            }

            $is_updated = $vehicle_previous_owner_or_seller_information->update([
                'vehicle_price' => $request->vehicle_price,
                'down_payment' => $request->down_payment,
                'installment_number_id' => $request->installment_number_id,
                'first_payment_date' => $request->first_payment_date,
                'provable_installment_finish_date' => $request->provable_installment_finish_date,
                'installment_amount' => $request->installment_amount,
                'total_due' => $request->total_due,
                'image_or_docs' => $vehicle_purchase_time_vehicle_payment_information_image_or_docs,
                'remark' => $request->vehicle_purchase_time_vehicle_payment_information_remark,
                'vehicle_id' => $vehicle_id,
            ]);

            if(!$is_updated){
                \DB::rollback();
                return response()->json([
                    'success' => false,
                    'message' => "Not Possible to Update Vehicle Purchase Time Vehicle Payment Information"
                ], 200);
            }

            //vehicle_purchase_time_vehicle_payment_informations end

            //vehicle_registration_informations start
            $vehicle_registration_information=VehicleRegistrationInformation::where('vehicle_id', $vehicle_id)->first();

            if(!$vehicle_registration_information){
                \DB::rollback();
                return response()->json([
                    'success' => false,
                    'message' => "Not find vehicle registration information with vehicle id ".$vehicle_id
                ], 200);
            }

            if ($request->vehicle_registration_information_image_or_docs !== null) {
                if (File::exists($vehicle_registration_information->image_or_docs)) {
                    File::delete($vehicle_registration_information->image_or_docs);
                }
                $vehicle_registration_information_image_or_docs = $this->uploadFile($request->file('vehicle_registration_information_image_or_docs'), 'vehicle_registration_information_images_or_docs');
            }
            else{
                $vehicle_registration_information_image_or_docs = $vehicle_registration_information->image_or_docs;
                $subject = $vehicle_registration_information_image_or_docs ;
                $search = url('/').'/'; ;
                $vehicle_registration_information_image_or_docs = str_replace($search, '', $subject) ;
            }

            $is_updated = $vehicle_previous_owner_or_seller_information->update([
                'registration_date' => $request->vehicle_registration_information_registration_date,
                'vehicle_buying_condition_id' => $request->vehicle_buying_condition_id,
                'tin_certificate' => $request->tin_certificate,
                'vehicle_registration_type_id' => $request->vehicle_registration_type_id,
                'registration_fee' => $request->registration_fee,
                'registration_invoice_number' => $request->registration_invoice_number,
                'sale_certificate' => $request->sale_certificate,
                'invoice_for_payment_of_vat' => $request->invoice_for_payment_of_vat,
                'vat_payment_receipt' => $request->vat_payment_receipt,
                'musac_1' => $request->musac_1,
                'musac_11_a_or_vat' => $request->musac_11_a_or_vat,
                'body_vat_invoice' => $request->body_vat_invoice,
                'receipt_of_deposit_of_applicable_registration_fee' => $request->receipt_of_deposit_of_applicable_registration_fee,
                'registered_new_owner_name' => $request->registered_new_owner_name,
                'new_owner_phone_number' => $request->new_owner_phone_number,
                'new_owner_nid_number' => $request->new_owner_nid_number,
                'ownership_transfer_fee' => $request->ownership_transfer_fee,
                'chassis_number' => $request->chassis_number,
                'engine_no' => $request->engine_no,
                'model_no' => $request->model_no,
                'tire_size' => $request->tire_size,
                'image_or_docs' => $vehicle_registration_information_image_or_docs,
                'remark' => $request->vehicle_registration_information_remark,
                'vehicle_id' => $vehicle_id
            ]);

            if(!$is_updated){
                \DB::rollback();
                return response()->json([
                    'success' => false,
                    'message' => "Not Possible to Update Vehicle Registration Information"
                ], 200);
            }
            //vehicle_registration_informations end



            // vehicle_tax_token_validation_periods Part Start
            $vehicle_tax_token_validation_period_update_array = [];
            $vehicle_tax_token_validation_period_create_array = [];
            foreach($request->vehicle_tax_token_validation_periods as $vehicle_tax_token_validation_period){
                if(isset($vehicle_tax_token_validation_period['id'])){
                    $vehicle_tax_token_validation_period_id = $vehicle_tax_token_validation_period['id'];
                    $found_vehicle_tax_token_validation_period = VehicleTaxTokenValidationPeriod::where('id', $vehicle_tax_token_validation_period_id)->first();
                    $vehicle_tax_token_validation_period_image_or_doc = $vehicle_tax_token_validation_period['vehicle_tax_token_validation_period_image_or_doc'];
                    if($vehicle_tax_token_validation_period_image_or_doc !== null){
                        if (File::exists($found_vehicle_tax_token_validation_period->image_or_docs)) {
                            File::delete($found_vehicle_tax_token_validation_period->image_or_docs);
                        }
                        $vehicle_tax_token_validation_period_image_or_doc_url = $this->uploadFile($vehicle_tax_token_validation_period_image_or_doc, 'vehicle_tax_token_validation_period_images_or_docs');
                    }
                    else{
                        $vehicle_tax_token_validation_period_image_or_doc_url = $found_vehicle_tax_token_validation_period->image_or_docs;
                        $subject = $vehicle_tax_token_validation_period_image_or_doc_url ;
                        $search = url('/').'/'; ;
                        $vehicle_tax_token_validation_period_image_or_doc_url = str_replace($search, '', $subject) ;
                    }
                    $found_vehicle_tax_token_validation_period->update([
                        'from' => $vehicle_tax_token_validation_period['from'],
                        'to' => $vehicle_tax_token_validation_period['to'],
                        'image_or_docs' => $vehicle_tax_token_validation_period_image_or_doc_url,
                        'vehicle_id' => $vehicle_id
                    ]);
                    $vehicle_tax_token_validation_period_update_array[] = $found_vehicle_tax_token_validation_period;
                }
                else{
                    $vehicle_tax_token_validation_period_image_or_doc_url = null;
                    $vehicle_tax_token_validation_period_image_or_doc = $vehicle_tax_token_validation_period['vehicle_tax_token_validation_period_image_or_doc'];
                    if($vehicle_tax_token_validation_period_image_or_doc !== null){
                        $vehicle_tax_token_validation_period_image_or_doc_url = $this->uploadFile($vehicle_tax_token_validation_period_image_or_doc, 'vehicle_tax_token_validation_period_images_or_docs');
                    }
                    $vehicle_tax_token_validation_period = VehicleTaxTokenValidationPeriod::create([
                        'from' => $vehicle_tax_token_validation_period['from'],
                        'to' => $vehicle_tax_token_validation_period['to'],
                        'image_or_docs' => $vehicle_tax_token_validation_period_image_or_doc_url,
                        'vehicle_id' => $vehicle_id
                    ]);
                    if($vehicle_tax_token_validation_period){
                        $vehicle_tax_token_validation_period_create_array[] = $vehicle_tax_token_validation_period;
                    }
                }
            }
            if(is_array($request->remove_vehicle_tax_token_validation_periods)){
                foreach($request->remove_vehicle_tax_token_validation_periods as $remove_vehicle_tax_token_validation_period) {
                    if(isset($remove_vehicle_tax_token_validation_period['id'])){
                        $vehicle_tax_token_validation_period = VehicleTaxTokenValidationPeriod::find($remove_vehicle_tax_token_validation_period['id']);
                        if(!$vehicle_tax_token_validation_period){
                            \DB::rollback();
                            return response()->json([
                                'success' => false,
                                'message' => "Not Find Vehicle Tax Token Validation Period With Id ". $remove_vehicle_tax_token_validation_period['id']
                            ], 200);
                        }
                        if($vehicle_tax_token_validation_period){
                            if (File::exists($vehicle_tax_token_validation_period->image_or_docs)) {
                                File::delete($vehicle_tax_token_validation_period->image_or_docs);
                            }
                        }
                        $remove= $vehicle_tax_token_validation_period->delete();
                    }
                }
            }
            // vehicle_tax_token_validation_periods End

            // vehicle_fitness_validation_periods Part Start
            $vehicle_fitness_validation_period_update_array = [];
            $vehicle_fitness_validation_period_create_array = [];
            foreach($request->vehicle_fitness_validation_periods as $vehicle_fitness_validation_period){
                if(isset($vehicle_fitness_validation_period['id'])){
                    $vehicle_fitness_validation_period_id = $vehicle_fitness_validation_period['id'];
                    $found_vehicle_fitness_validation_period = VehicleFitnessValidationPeriod::where('id', $vehicle_fitness_validation_period_id)->first();
                    $vehicle_fitness_validation_period_image_or_doc = $vehicle_fitness_validation_period['vehicle_fitness_validation_period_image_or_doc'];
                    if($vehicle_fitness_validation_period_image_or_doc !== null){
                        if (File::exists($found_vehicle_fitness_validation_period->image_or_docs)) {
                            File::delete($found_vehicle_fitness_validation_period->image_or_docs);
                        }
                        $vehicle_fitness_validation_period_image_or_doc_url = $this->uploadFile($vehicle_fitness_validation_period_image_or_doc, 'vehicle_fitness_validation_period_images_or_docs');
                    }
                    else{
                        $vehicle_fitness_validation_period_image_or_doc_url = $found_vehicle_fitness_validation_period->image_or_docs;
                        $subject = $vehicle_fitness_validation_period_image_or_doc_url ;
                        $search = url('/').'/'; ;
                        $vehicle_fitness_validation_period_image_or_doc_url = str_replace($search, '', $subject) ;
                    }
                    $found_vehicle_fitness_validation_period->update([
                        'from' => $vehicle_fitness_validation_period['from'],
                        'to' => $vehicle_fitness_validation_period['to'],
                        'image_or_docs' => $vehicle_fitness_validation_period_image_or_doc_url,
                        'vehicle_id' => $vehicle_id
                    ]);
                    $vehicle_fitness_validation_period_update_array[] = $found_vehicle_fitness_validation_period;
                }
                else{
                    $vehicle_fitness_validation_period_image_or_doc_url = null;
                    $vehicle_fitness_validation_period_image_or_doc = $vehicle_fitness_validation_period['vehicle_fitness_validation_period_image_or_doc'];
                    if($vehicle_fitness_validation_period_image_or_doc !== null){
                        $vehicle_fitness_validation_period_image_or_doc_url = $this->uploadFile($vehicle_fitness_validation_period_image_or_doc, 'vehicle_fitness_validation_period_images_or_docs');
                    }
                    $vehicle_fitness_validation_period = VehicleFitnessValidationPeriod::create([
                        'from' => $vehicle_fitness_validation_period['from'],
                        'to' => $vehicle_fitness_validation_period['to'],
                        'image_or_docs' => $vehicle_fitness_validation_period_image_or_doc_url,
                        'vehicle_id' => $vehicle_id
                    ]);
                    if($vehicle_fitness_validation_period){
                        $vehicle_fitness_validation_period_create_array[] = $vehicle_fitness_validation_period;
                    }
                }
            }
            if(is_array($request->remove_vehicle_fitness_validation_periods)){
                foreach($request->remove_vehicle_fitness_validation_periods as $remove_vehicle_fitness_validation_period) {
                    if(isset($remove_vehicle_fitness_validation_period['id'])){
                        $vehicle_fitness_validation_period = VehicleFitnessValidationPeriod::find($remove_vehicle_fitness_validation_period['id']);
                        if(!$vehicle_fitness_validation_period){
                            \DB::rollback();
                            return response()->json([
                                'success' => false,
                                'message' => "Not Find Vehicle Fitness Validation Period With Id ". $remove_vehicle_fitness_validation_period['id']
                            ], 200);
                        }
                        if($vehicle_fitness_validation_period){
                            if (File::exists($vehicle_fitness_validation_period->image_or_docs)) {
                                File::delete($vehicle_fitness_validation_period->image_or_docs);
                            }
                        }
                        $remove= $vehicle_fitness_validation_period->delete();
                    }
                }
            }
            // vehicle_fitness_validation_periods End

            // vehicle_insurance_validation_periods Part Start
            $vehicle_insurance_validation_period_update_array = [];
            $vehicle_insurance_validation_period_create_array = [];
            foreach($request->vehicle_insurance_validation_periods as $vehicle_insurance_validation_period){
                if(isset($vehicle_insurance_validation_period['id'])){
                    $vehicle_insurance_validation_period_id = $vehicle_insurance_validation_period['id'];
                    $found_vehicle_insurance_validation_period = VehicleInsuranceValidationPeriod::where('id', $vehicle_insurance_validation_period_id)->first();
                    $vehicle_insurance_validation_period_image_or_doc = $vehicle_insurance_validation_period['vehicle_insurance_validation_period_image_or_doc'];
                    if($vehicle_insurance_validation_period_image_or_doc !== null){
                        if (File::exists($found_vehicle_insurance_validation_period->image_or_docs)) {
                            File::delete($found_vehicle_insurance_validation_period->image_or_docs);
                        }
                        $vehicle_insurance_validation_period_image_or_doc_url = $this->uploadFile($vehicle_insurance_validation_period_image_or_doc, 'vehicle_insurance_validation_period_images_or_docs');
                    }
                    else{
                        $vehicle_insurance_validation_period_image_or_doc_url = $found_vehicle_insurance_validation_period->image_or_docs;
                        $subject = $vehicle_insurance_validation_period_image_or_doc_url ;
                        $search = url('/').'/'; ;
                        $vehicle_insurance_validation_period_image_or_doc_url = str_replace($search, '', $subject) ;
                    }
                    $found_vehicle_insurance_validation_period->update([
                        'from' => $vehicle_insurance_validation_period['from'],
                        'to' => $vehicle_insurance_validation_period['to'],
                        'image_or_docs' => $vehicle_insurance_validation_period_image_or_doc_url,
                        'vehicle_id' => $vehicle_id
                    ]);
                    $vehicle_insurance_validation_period_update_array[] = $found_vehicle_insurance_validation_period;
                }
                else{
                    $vehicle_insurance_validation_period_image_or_doc_url = null;
                    $vehicle_insurance_validation_period_image_or_doc = $vehicle_insurance_validation_period['vehicle_insurance_validation_period_image_or_doc'];
                    if($vehicle_insurance_validation_period_image_or_doc !== null){
                        $vehicle_insurance_validation_period_image_or_doc_url = $this->uploadFile($vehicle_insurance_validation_period_image_or_doc, 'vehicle_insurance_validation_period_images_or_docs');
                    }
                    $vehicle_insurance_validation_period = VehicleInsuranceValidationPeriod::create([
                        'from' => $vehicle_insurance_validation_period['from'],
                        'to' => $vehicle_insurance_validation_period['to'],
                        'image_or_docs' => $vehicle_insurance_validation_period_image_or_doc_url,
                        'vehicle_id' => $vehicle_id
                    ]);
                    if($vehicle_insurance_validation_period){
                        $vehicle_insurance_validation_period_create_array[] = $vehicle_insurance_validation_period;
                    }
                }
            }
            if(is_array($request->remove_vehicle_insurance_validation_periods)){
                foreach($request->remove_vehicle_insurance_validation_periods as $remove_vehicle_insurance_validation_period) {
                    if(isset($remove_vehicle_insurance_validation_period['id'])){
                        $vehicle_insurance_validation_period = VehicleInsuranceValidationPeriod::find($remove_vehicle_insurance_validation_period['id']);
                        if(!$vehicle_insurance_validation_period){
                            \DB::rollback();
                            return response()->json([
                                'success' => false,
                                'message' => "Not Find Vehicle Insurance Validation Period With Id ". $remove_vehicle_insurance_validation_period['id']
                            ], 200);
                        }
                        if($vehicle_insurance_validation_period){
                            if (File::exists($vehicle_insurance_validation_period->image_or_docs)) {
                                File::delete($vehicle_insurance_validation_period->image_or_docs);
                            }
                        }
                        $remove= $vehicle_insurance_validation_period->delete();
                    }
                }
            }
            // vehicle_insurance_validation_periods End

            // vehicle_free_servicing_validation_periods Part Start
            $vehicle_free_servicing_validation_period_update_array = [];
            $vehicle_free_servicing_validation_period_create_array = [];
            foreach($request->vehicle_free_servicing_validation_periods as $vehicle_free_servicing_validation_period){
                if(isset($vehicle_free_servicing_validation_period['id'])){
                    $vehicle_free_servicing_validation_period_id = $vehicle_free_servicing_validation_period['id'];
                    $found_vehicle_free_servicing_validation_period = VehicleFreeServicingValidationPeriod::where('id', $vehicle_free_servicing_validation_period_id)->first();
                    $vehicle_free_servicing_validation_period_image_or_doc = $vehicle_free_servicing_validation_period['vehicle_free_servicing_validation_period_image_or_doc'];
                    if($vehicle_free_servicing_validation_period_image_or_doc !== null){
                        if (File::exists($found_vehicle_free_servicing_validation_period->image_or_docs)) {
                            File::delete($found_vehicle_free_servicing_validation_period->image_or_docs);
                        }
                        $vehicle_free_servicing_validation_period_image_or_doc_url = $this->uploadFile($vehicle_free_servicing_validation_period_image_or_doc, 'vehicle_free_servicing_validation_period_images_or_docs');
                    }
                    else{
                        $vehicle_free_servicing_validation_period_image_or_doc_url = $found_vehicle_free_servicing_validation_period->image_or_docs;
                        $subject = $vehicle_free_servicing_validation_period_image_or_doc_url ;
                        $search = url('/').'/'; ;
                        $vehicle_free_servicing_validation_period_image_or_doc_url = str_replace($search, '', $subject) ;
                    }
                    $found_vehicle_free_servicing_validation_period->update([
                        'from' => $vehicle_free_servicing_validation_period['from'],
                        'to' => $vehicle_free_servicing_validation_period['to'],
                        'image_or_docs' => $vehicle_free_servicing_validation_period_image_or_doc_url,
                        'vehicle_id' => $vehicle_id
                    ]);
                    $vehicle_free_servicing_validation_period_update_array[] = $found_vehicle_free_servicing_validation_period;
                }
                else{
                    $vehicle_free_servicing_validation_period_image_or_doc_url = null;
                    $vehicle_free_servicing_validation_period_image_or_doc = $vehicle_free_servicing_validation_period['vehicle_free_servicing_validation_period_image_or_doc'];
                    if($vehicle_free_servicing_validation_period_image_or_doc !== null){
                        $vehicle_free_servicing_validation_period_image_or_doc_url = $this->uploadFile($vehicle_free_servicing_validation_period_image_or_doc, 'vehicle_free_servicing_validation_period_images_or_docs');
                    }
                    $vehicle_free_servicing_validation_period = VehicleFreeServicingValidationPeriod::create([
                        'from' => $vehicle_free_servicing_validation_period['from'],
                        'to' => $vehicle_free_servicing_validation_period['to'],
                        'image_or_docs' => $vehicle_free_servicing_validation_period_image_or_doc_url,
                        'vehicle_id' => $vehicle_id
                    ]);
                    if($vehicle_free_servicing_validation_period){
                        $vehicle_free_servicing_validation_period_create_array[] = $vehicle_free_servicing_validation_period;
                    }
                }
            }
            if(is_array($request->remove_vehicle_free_servicing_validation_periods)){
                foreach($request->remove_vehicle_free_servicing_validation_periods as $remove_vehicle_free_servicing_validation_period) {
                    if(isset($remove_vehicle_free_servicing_validation_period['id'])){
                        $vehicle_free_servicing_validation_period = VehicleFreeServicingValidationPeriod::find($remove_vehicle_free_servicing_validation_period['id']);
                        if(!$vehicle_free_servicing_validation_period){
                            \DB::rollback();
                            return response()->json([
                                'success' => false,
                                'message' => "Not Find Vehicle Free Servicing Validation Period With Id ". $remove_vehicle_free_servicing_validation_period['id']
                            ], 200);
                        }
                        if($vehicle_free_servicing_validation_period){
                            if (File::exists($vehicle_free_servicing_validation_period->image_or_docs)) {
                                File::delete($vehicle_free_servicing_validation_period->image_or_docs);
                            }
                        }
                        $remove= $vehicle_free_servicing_validation_period->delete();
                    }
                }
            }
            // vehicle_free_servicing_validation_periods End
            \DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Vehicle is Updated Successfully",
            ], 200);
        }
        catch(\Exception $e){
            \DB::rollback();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 200);
        }

    }

}
