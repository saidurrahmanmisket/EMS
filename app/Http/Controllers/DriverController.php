<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Models\DriverDocument;
use App\Models\DriverEducationDetail;
use App\Models\DriverExperienceDetail;
use App\Models\DriverIncrementDetail;
use App\Models\DriverLicenseDetail;
use App\Models\DriverReferenceDetail;
use App\Models\User;
use App\Traits\UploadTraits;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use App\Http\Controllers\Authorization\AuthorizationController;

class DriverController extends Controller
{
    use UploadTraits;
    public function list(Request $request){
        try{
            $driver = Driver::with([
                'company:id,name',
                'branch:id,name',
                
            ])
                ->select('id', 'name', 'driver_code', 'phone_number', 'image', 'joining_date', 'total_experience', 'company_id', 'branch_id')
                ->orderBy('id', 'desc');
            $driver = $driver->paginate($request->per_page);
            $driver=$driver->appends($request->all());
            $data['driver'] = $driver;

            return response()->json([
                'success' => true,
                'message' => 'Driver List',
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

//        Log::debug($request->all());
        $validator = Validator::make($request->all(), [
            'company_id' => 'required',
            'branch_id' => 'required',
            'driver_code' => 'required|unique:drivers',
            'name' => 'required',
            'fathers_name' => 'required',
            'fathers_contact_number' => 'numeric|digits:11',
            'fathers_nid_number' => 'required',
            'mothers_name' => 'required',
            'mothers_nid_number' => 'required',
            'date_of_birth' => 'required',
            'gender' => 'required',
            'religion' => 'required',
            'phone_number' => 'required|numeric|digits:11|unique:users',
            'email' => 'required|unique:users',
            'nid_no' => 'required',
            'marital_status' => 'required',
            'present_address' => 'required',
            'permanent_address' => 'required',
            'nid_image' => 'file|mimes:png,jpg,svg,jpeg',
            'image' =>  'file|mimes:png,jpg,svg,jpeg',
            'emergency_contact_number' => 'required|numeric|digits:11',
            'joining_date' => 'required',
            'salary' => 'required',
            'trial_period' => 'required',
            'reference.*.referral_contact_number' => 'required|numeric|digits:11'
        ]);
        if($validator->fails() ){
            return response()->json([
                'success' => 401,
                'message' => "Validator Error.",
                'error' => $validator->errors(),
            ], 401);
        };
        \DB::beginTransaction();
        try{
            $data['user'] = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'phone_number' => $request->phone_number,
            ]);
            $user = $data['user'];
            $user_id=$user->id;
            $authorization_controller=new AuthorizationController();
            $authorization_controller->create_role_driver_and_assign_that_role_driver_to_the_created_driver($user_id);
            $nid_image = null;
            $image = null;
            if($request->hasFile('nid_image')){
                $nid_image = $this->uploadImage($request->file('nid_image'), 'driver/driver');
            }
            if($request->hasFile('image')){
                $image = $this->uploadImage($request->file('image'), 'driver/driver');
            }
            $data['driver'] = Driver::create([
                'driver_code' => $request->driver_code,
                'date' => date('Y-m-d'),
                'user_id' => $user->id,
                'company_id' => $request->company_id,
                'branch_id' => $request->branch_id,
                'name' => $request->name,
                'fathers_name' => $request->fathers_name,
                'fathers_contact_number' => $request->fathers_contact_number,
                'fathers_nid_number' => $request->fathers_nid_number,
                'mothers_name' => $request->mothers_name,
                'mothers_nid_number' => $request->mothers_nid_number,
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'religion' => $request->religion,
                'phone_number' => $request->phone_number,
                'email' => $request->email,
                'nid_no' => $request->nid_no,
                'birth_certificate_no' => $request->birth_certificate_no,
                'marital_status' => $request->marital_status,
                'spouse_name' => $request->spouse_name,
                'present_address' => $request->present_address,
                'permanent_address' => $request->permanent_address,
                'nid_address' => $request->nid_address,
                'nid_image' => $nid_image,
                'image' => $image,
                'passport_no' => $request->passport_no,
                'emergency_contact_number' => $request->emergency_contact_number,
                'bio' => $request->bio,
                'joining_date' => $request->joining_date,
                'salary' => $request->salary,
                'trial_period' => $request->trial_period,
                'probable_permanent_date' => $request->probable_permanent_date,
                'probable_permanent_increment_date' => $request->probable_permanent_increment_date,
                'bonus_activate_date' => $request->bonus_activate_date,
            ]);
            $driver = $data['driver'];
            // Education Part Start
            if(is_array($request->education)){
                foreach($request->education as  $educationData){
                    $file = $educationData['education_certificate'] ?? null;
                    $education_certificate = null;
                    if($file !== 'null'){
                        $education_certificate = $this->uploadFile($file, 'driver/driver_education');
                    }
                    $education = array(
                        'driver_id' => $driver->id,
                        'degree_name' => $educationData['degree_name'],
                        'institution_name' => $educationData['institution_name'],
                        'passing_year' => $educationData['passing_year'],
                        'result' => $educationData['result'],
                        'board_name' => $educationData['board_name'],
                        'education_certificate' => $education_certificate,
                        'achieved_reward' => $educationData['achieved_reward'],
                    );
                    $data['education'] = DriverEducationDetail::create($education);
                }
            }
            // Education Part end
            // Experience Part start
            $total_experience = 0;
            if(is_array($request->experience)){
                foreach ($request->experience as $experienceData){
                    $file = $experienceData['experience_certificate'] ?? null;
                    $experience_certificate = null;
                    if($file !== 'null'){
                        $experience_certificate = $this->uploadFile($file, 'driver/driver_experience');
                    }
                    $experience = array(
                        'driver_id' => $driver->id,
                        'year_of_experience' => $experienceData['year_of_experience'],
                        'organization_name' => $experienceData['organization_name'],
                        'experience_certificate' => $experience_certificate,
                    );
                    $data['experience'] = DriverExperienceDetail::create($experience);
                    $total_experience= $total_experience + $experienceData['year_of_experience'];
                }
            }
            $driver_total_experience = Driver::where('id', $driver->id)->update([
                'total_experience' => $total_experience,
            ]);


            // Experience Part End
            // License Part Start
            if(is_array($request->license)){
                foreach($request->license as $licenseData){
                    $license = array(
                        'driver_id' => $driver->id,
                        'license_type' => $licenseData['license_type'],
                        'license_number' => $licenseData['license_number'],
                        'license_issue_date' => $licenseData['license_issue_date'],
                        'license_validate_duration' => $licenseData['license_validate_duration'],
                    );
                    $data['license'] = DriverLicenseDetail::create($license);
                }
            }
            // License Part End
            // Increment Part Start
            if(is_array($request->increment)){
                foreach($request->increment as $incrementData){
                    $file = $incrementData['approval_document'] ?? null;
                    $approval_document = null;
                    if($file !== 'null'){
                        $approval_document = $this->uploadFile($file, 'driver/driver_increment');
                    }
                    $increment = array(
                        'driver_id' => $driver->id,
                        'letter_issue_date' => $incrementData['letter_issue_date'],
                        'effective_date' => $incrementData['effective_date'],
                        'previous_basic' => $incrementData['previous_basic'],
                        'new_basic' => $incrementData['new_basic'],
                        'increment_amount' => $incrementData['increment_amount'],
                        'gross_salary' => $incrementData['gross_salary'],
                        'approval_document' => $approval_document,
                    );
                    $data['increment'] = DriverIncrementDetail::create($increment);
                }
            }
            // Increment Part End
            // Reference Part Start
            if(is_array($request->reference)){
                foreach($request->reference as $referenceData){
                    $reference = array(
                        'driver_id' => $driver->id,
                        'referral_name' => $referenceData['referral_name'],
                        'referral_contact_number' => $referenceData['referral_contact_number'],
                        'relation_with_referral' => $referenceData['relation_with_referral'],
                        'referral_address' => $referenceData['referral_address'],
                    );
                    $data['reference'] = DriverReferenceDetail::create($reference);
                }
            }
            // Reference Part End

            \DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Driver Created Successfully',
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

    public function edit(Request $request){
        try{
            $data['driver'] = Driver::with([
                'company:id,name,company_code',
                'branch:id,name',
                'driver_education:id,driver_id,degree_name,institution_name,passing_year,result,board_name,education_certificate,achieved_reward',
                'driver_experience:id,driver_id,year_of_experience,organization_name,experience_certificate',
                'driver_license:id,driver_id,license_type,license_number,license_issue_date,license_validate_duration',
                'driver_increment:id,driver_id,letter_issue_date,effective_date,previous_basic,new_basic,increment_amount,gross_salary,approval_document',
                'driver_reference:id,driver_id,referral_name,referral_contact_number,relation_with_referral,referral_address'
            ])->find($request->id);

            return response()->json([
                'success' => true,
                'message' => 'Driver Info',
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
    public function update(Request $request){
        Log::debug($request->all());
        $validator = Validator::make($request->all(), [
            'company_id' => 'required',
            'branch_id' => 'required',
            'name' => 'required',
            'fathers_name' => 'required',
            'fathers_nid_number' => 'required',
            'mothers_name' => 'required',
            'mothers_nid_number' => 'required',
            'date_of_birth' => 'required',
            'gender' => 'required',
            'religion' => 'required',
            'phone_number' => 'required|numeric|digits:11',
            'email' => 'required',
            'nid_no' => 'required',
            'marital_status' => 'required',
            'present_address' => 'required',
            'permanent_address' => 'required',
//           'nid_image' => 'file|mimes:png,jpg,svg,jpeg',
//           'image' =>  'file|mimes:png,jpg,svg,jpeg',
            'emergency_contact_number' => 'required|numeric|digits:11',
            'joining_date' => 'required',
            'salary' => 'required',
            'trial_period' => 'required',
            'reference.*.referral_contact_number' => 'required|numeric|digits:11'
        ]);
        if($validator->fails() ){
            return response()->json([
                'success' => 401,
                'message' => "Validator Error.",
                'error' => $validator->errors(),
            ], 401);
        };


        \DB::beginTransaction();
        try{
            $driver = Driver::find($request->id);
            // User Phone Number and Email Unique Verification Code Start
            $phone_number=$request->phone_number;
            $user_id =$driver->user_id;
            $User=User::where('phone_number',$phone_number)->where('id','!=',$user_id)->first();
            if($User){
                return response()->json([
                    'success' =>false,
                    'message' => "There is Already A Phone Number With ".$phone_number,

                ], 200);
            }
            $email=$request->email;
            $User=User::where('email',$email)->where('id','!=',$user_id)->first();
            if($User){
                return response()->json([
                    'success' =>false,
                    'message' => "There is Already A Email With ".$email,
                ], 200);
            }
            // User Phone Number and Email Unique Verification Code End
            $data['user'] = User::where('id', $driver->user_id)->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
            ]);
            // driver update Start
            if ($request->nid_image !== 'null') {
                if (File::exists($driver->nid_image)) {
                    File::delete($driver->nid_image);
                }
                $nid_image = $this->uploadImage($request->file('nid_image'), 'driver/driver');
            }
            else{
                $nid_image = $driver->nid_image;
            }
            if ($request->image !== 'null') {
                if (File::exists($driver->image)) {
                    File::delete($driver->image);
                }
                $image = $this->uploadImage($request->file('image'), 'driver/driver');
            }
            else{
                $image = $driver->image;
            }
            $data['driver'] = Driver::where('id', $request->id)->update([
                'company_id' => $request->company_id,
                'branch_id' => $request->branch_id,
                'name' => $request->name,
                'fathers_name' => $request->fathers_name,
                'fathers_contact_number' => $request->fathers_contact_number,
                'fathers_nid_number' => $request->fathers_nid_number,
                'mothers_name' => $request->mothers_name,
                'mothers_nid_number' => $request->mothers_nid_number,
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'religion' => $request->religion,
                'phone_number' => $request->phone_number,
                'email' => $request->email,
                'nid_no' => $request->nid_no,
                'birth_certificate_no' => $request->birth_certificate_no,
                'marital_status' => $request->marital_status,
                'spouse_name' => $request->spouse_name,
                'present_address' => $request->present_address,
                'permanent_address' => $request->permanent_address,
                'nid_address' => $request->nid_address,
                'nid_image' => $nid_image,
                'image' => $image,
                'passport_no' => $request->passport_no,
                'emergency_contact_number' => $request->emergency_contact_number,
                'bio' => $request->bio,
                'joining_date' => $request->joining_date,
                'salary' => $request->salary,
                'trial_period' => $request->trial_period,
                'probable_permanent_date' => $request->probable_permanent_date,
                'probable_permanent_increment_date' => $request->probable_permanent_increment_date,
                'bonus_activate_date' => $request->bonus_activate_date,
            ]);
            // Education Part Start
            if(is_array($request->education)){
                foreach($request->education as  $educationData){
                    if(isset($educationData['id'])){
                        $education_id = $educationData['id'];
                        $foundEducation = DriverEducationDetail::where('id', $education_id)->first();
                        $file = $educationData['education_certificate'];
                        if($file !== 'null'){
                            if (File::exists($foundEducation->education_certificate)) {
                                File::delete($foundEducation->education_certificate);
                            }
                            $education_certificate = $this->uploadFile($file, 'driver/driver_education');
                        }
                        else{
                            $education_certificate = $foundEducation->education_certificate;
                        }
                        $foundEducation->update([
                            'degree_name' => $educationData['degree_name'],
                            'institution_name' => $educationData['institution_name'],
                            'passing_year' => $educationData['passing_year'],
                            'result' => $educationData['result'],
                            'board_name' => $educationData['board_name'],
                            'education_certificate' => $education_certificate,
                            'achieved_reward' => $educationData['achieved_reward'],
                        ]);
                    }
                    else{
                        $education_certificate = null;
                        $file = $educationData['education_certificate'] ?? null;
                        if($file !== 'null'){
                            $education_certificate = $this->uploadFile($file, 'driver/driver_education');
                        }
                        $education = DriverEducationDetail::create([
                            'driver_id' => $driver->id,
                            'degree_name' => $educationData['degree_name'],
                            'institution_name' => $educationData['institution_name'],
                            'passing_year' => $educationData['passing_year'],
                            'result' => $educationData['result'],
                            'board_name' => $educationData['board_name'],
                            'education_certificate' => $education_certificate,
                            'achieved_reward' => $educationData['achieved_reward'],
                        ]);
                    }
                }
            }
            if(is_array($request->remove_education)){
                foreach($request->remove_education as $removeData) {
                    if(isset($removeData['id'])){
                        $remove_id = DriverEducationDetail::find($removeData['id']);
                        if($remove_id){
                            if (File::exists($remove_id->education_certificate)) {
                                File::delete($remove_id->education_certificate);
                            }
                        }
                        $remove= DriverEducationDetail::where('id', $removeData['id'])->delete();
                    }
                }
            }
            // Education Part end
            // Experience Part start
            if(is_array($request->experience)){
                foreach ($request->experience as $experienceData){
                    if(isset($experienceData['id'])){
                        $experience_id = $experienceData['id'];
                        $foundExperience = DriverExperienceDetail::where('id', $experience_id)->first();
                        $file = $experienceData['experience_certificate'];
                        if($file !== 'null'){
                            if (File::exists($foundExperience->experience_certificate)) {
                                File::delete($foundExperience->experience_certificate);
                            }
                            $experience_certificate = $this->uploadFile($file, 'driver/driver_experience');
                        }
                        else{
                            $experience_certificate = $foundExperience->experience_certificate;
                        }
                        $foundExperience->update([
                            'year_of_experience' => $experienceData['year_of_experience'],
                            'organization_name' => $experienceData['organization_name'],
                            'experience_certificate' => $experience_certificate,
                        ]);
                    }
                    else{
                        $experience_certificate = null;
                        $file = $experienceData['experience_certificate'] ?? null;
                        if($file !== 'null'){
                            $experience_certificate = $this->uploadFile($file, 'driver/driver_experience');
                        }
                        $experience = DriverExperienceDetail::create([
                            'driver_id' => $driver->id,
                            'year_of_experience' => $experienceData['year_of_experience'],
                            'organization_name' => $experienceData['organization_name'],
                            'experience_certificate' => $experience_certificate,
                        ]);
                    }
                }
            }
            if(is_array($request->remove_experience)){
                foreach($request->remove_experience as $removeData) {
                    if(isset($removeData['id'])){
                        $remove_id = DriverExperienceDetail::find($removeData['id']);
                        if($remove_id){
                            if (File::exists($remove_id->experience_certificate)) {
                                File::delete($remove_id->experience_certificate);
                            }
                        }
                        $remove= DriverExperienceDetail::where('id', $removeData['id'])->delete();
                    }
                }
            }
            $total_experience = DriverExperienceDetail::where('driver_id', $driver->id)->sum('year_of_experience');
            $driver_total_experience = Driver::where('id', $driver->id)->update([
                'total_experience' => $total_experience,
            ]);
            // Experience Part End
            // License Part Start
            if(is_array($request->license)){
                foreach($request->license as $licenseData){
                    if(isset($licenseData['id'])){
                        $license_id = $licenseData['id'];
                        $foundLicense = DriverLicenseDetail::where('id', $license_id)->first();
                        $foundLicense->update([
                            'license_type' => $licenseData['license_type'],
                            'license_number' => $licenseData['license_number'],
                            'license_issue_date' => $licenseData['license_issue_date'],
                            'license_validate_duration' => $licenseData['license_validate_duration'],
                        ]);
                    }
                    else{
                        $license = DriverLicenseDetail::create([
                            'driver_id' => $driver->id,
                            'license_type' => $licenseData['license_type'],
                            'license_number' => $licenseData['license_number'],
                            'license_issue_date' => $licenseData['license_issue_date'],
                            'license_validate_duration' => $licenseData['license_validate_duration'],
                        ]);
                    }
                }
            }
            if(is_array($request->remove_license)){
                foreach($request->remove_license as $removeData) {
                    if(isset($removeData['id'])){
                        $remove= DriverLicenseDetail::where('id', $removeData['id'])->delete();
                    }
                }
            }
            // License Part End
            // Increment Part Start
            if(is_array($request->increment)){
                foreach($request->increment as $incrementData){
                    if(isset($incrementData['id'])){
                        $increment_id = $incrementData['id'];
                        $foundIncrement = DriverIncrementDetail::where('id', $increment_id)->first();
                        $file = $incrementData['approval_document'];
                        if($file !== 'null'){
                            if (File::exists($foundIncrement->approval_document)) {
                                File::delete($foundIncrement->approval_document);
                            }
                            $approval_document = $this->uploadFile($file, 'driver/driver_increment');
                        }
                        else{
                            $approval_document = $foundIncrement->approval_document;
                        }
                        $foundIncrement->update([
                            'letter_issue_date' => $incrementData['letter_issue_date'],
                            'effective_date' => $incrementData['effective_date'],
                            'previous_basic' => $incrementData['previous_basic'],
                            'new_basic' => $incrementData['new_basic'],
                            'increment_amount' => $incrementData['increment_amount'],
                            'gross_salary' => $incrementData['gross_salary'],
                            'approval_document' => $approval_document,
                        ]);
                    }
                    else{
                        $approval_document = null;
                        $file = $incrementData['approval_document'] ?? null;
                        if($file !== 'null'){
                            $approval_document = $this->uploadFile($file, 'driver/driver_increment');
                        }
                        $increment = DriverIncrementDetail::create([
                            'driver_id' => $driver->id,
                            'letter_issue_date' => $incrementData['letter_issue_date'],
                            'effective_date' => $incrementData['effective_date'],
                            'previous_basic' => $incrementData['previous_basic'],
                            'new_basic' => $incrementData['new_basic'],
                            'increment_amount' => $incrementData['increment_amount'],
                            'gross_salary' => $incrementData['gross_salary'],
                            'approval_document' => $approval_document,
                        ]);
                    }
                }
            }
            if(is_array($request->remove_increment)){
                foreach($request->remove_increment as $removeData) {
                    if(isset($removeData['id'])){
                        $remove_id = DriverIncrementDetail::find($removeData['id']);
                        if($remove_id){
                            if (File::exists($remove_id->approval_document)) {
                                File::delete($remove_id->approval_document);
                            }
                        }
                        $remove= DriverIncrementDetail::where('id', $removeData['id'])->delete();
                    }
                }
            }
            // Increment Part End
            // Reference Part Start
            if(is_array($request->reference)){
                foreach($request->reference as $referenceData){
                    $reference_id = $referenceData['id'];
                    $foundReference = DriverReferenceDetail::where('id', $reference_id)->first();
                    $foundReference->update([
                        'referral_name' => $referenceData['referral_name'],
                        'referral_contact_number' => $referenceData['referral_contact_number'],
                        'relation_with_referral' => $referenceData['relation_with_referral'],
                        'referral_address' => $referenceData['referral_address'],
                    ]);
                }
            }
            // Reference Part End

            \DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Driver Updated Successfully',
                'data' => $data,
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
    public function getDriverDocument(Request $request)
    {
        try {
            $data['Personal_Details_Document']=Driver::where('id', $request->id)
                ->select('id','nid_image', 'image')
                ->get();
            $data['Education_Information_Document']=DriverEducationDetail::where('driver_id', $request->id)->whereNotNull('education_certificate')
                ->select('id','education_certificate')
                ->get();
            $data['Experience_Information_Document']=DriverExperienceDetail::where('driver_id', $request->id)->whereNotNull('experience_certificate')
                ->select('id','experience_certificate')
                ->get();
            $data['Increment_Information_Document']=DriverIncrementDetail::where('driver_id', $request->id)->whereNotNull('approval_document')
                ->select('id','approval_document')
                ->get();
            $data['Others_Documents']=DriverDocument::where('driver_id', $request->id)
                ->select('id', 'document')
                ->get();
            return response()->json([
                'success' => true,
                'message' => "Driver Documents",
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
                            $document = $this->uploadFile($file, 'driver_document');
                        }
                        else{
                            $document = $this->uploadImage($file, 'driver_document');
                        }
                    }
                    $other_document = array(
                        'driver_id' => $documentData['id'],
                        'document' => $document,
                    );
                    $data['document'] = DriverDocument::create($other_document);
                }
            }
            return response()->json([
                'success' => true,
                'message' => "Driver Documents Uploaded Successfully",
                'data' => $data
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 200);
        }

    }

    public function deleteOthersDocument(Request $request){
        try{
            $others_document = DriverDocument::find($request->id);
            if($others_document){
                if(File::exists($others_document->document)){
                    File::delete($others_document->document);
                }
            }
            $data = $others_document->delete();
            return response()->json([
                'success' => true,
                'message' => 'Others Document Deleted Successfully',
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
    public function deletePersonalDetailsDocument(Request $request)
    {
        try {
            $driver_document = Driver::find($request->id);
            if($request->column == 'nid_image'){
                if ($driver_document) {
                    if (File::exists($driver_document->nid_image)) {
                        File::delete($driver_document->nid_image);
                    }
                }
                $data = Driver::where('id', $request->id)->update([
                    'nid_image' => null,
                ]);
                return response()->json([
                    'success' => true,
                    'message' => "Driver Nid Image Deleted Successfully",
                    'data' => $data
                ], 200);
            }
            else{
                if ($driver_document) {
                    if (File::exists($driver_document->image)) {
                        File::delete($driver_document->image);
                    }
                }
                $data = Driver::where('id', $request->id)->update([
                    'image' => null,
                ]);
                return response()->json([
                    'success' => true,
                    'message' => "Driver Image Deleted Successfully",
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
    public function deleteEducationDocument(Request $request)
    {
        try {
            $education_document = DriverEducationDetail::find($request->id);
            if ($education_document) {
                if (File::exists($education_document->education_certificate)) {
                    File::delete($education_document->education_certificate);
                }
            }
            $data = DriverEducationDetail::where('id', $request->id)->update([
                'education_certificate' => null,
            ]);
            return response()->json([
                'success' => true,
                'message' => "Education Certificate Deleted Successfully",
                'data' => $data
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 200);
        }
    }
    public function deleteExperienceDocument(Request $request)
    {
        try {
            $experience_document = DriverExperienceDetail::find($request->id);
            if ($experience_document) {
                if (File::exists($experience_document->experience_certificate)) {
                    File::delete($experience_document->experience_certificate);
                }
            }
            $data = DriverExperienceDetail::where('id', $request->id)->update([
                'experience_certificate' => null,
            ]);
            return response()->json([
                'success' => true,
                'message' => "Experience Certificate Deleted Successfully",
                'data' => $data
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 200);
        }
    }
    public function deleteIncrementDocument(Request $request)
    {
        try {
            $increment_document = DriverIncrementDetail::find($request->id);
            if ($increment_document) {
                if (File::exists($increment_document->approval_document)) {
                    File::delete($increment_document->approval_document);
                }
            }
            $data = DriverIncrementDetail::where('id', $request->id)->update([
                'approval_document' => null,
            ]);
            return response()->json([
                'success' => true,
                'message' => "Increment Document Deleted Successfully",
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
