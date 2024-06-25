<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeDocument;
use App\Models\EmployeeEducationDetail;
use App\Models\EmployeeExperienceDetail;
use App\Models\EmployeeOfficialDetail;
use App\Models\EmployeePromotionDetail;
use App\Models\EmployeeReferenceDetail;
use App\Models\EmployeeTrainingDetail;
use App\Models\User;
use App\Traits\UploadTraits;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Spatie\Permission\Models\Role;
use function Symfony\Component\String\u;

class EmployeeController extends Controller
{
    use UploadTraits;

    public function list(Request $request)
    {
        try {
            $employee = Employee::orderby('id', 'desc')
                ->select('id', 'name', 'phone_number');

            $data['employee'] = $employee->get(); //all data for dropdown

            if ($request->per_page) { //for table with paginate
                $employee = $employee->with([
                    'company:id,name,company_code',
                    'branch:id,name',
                    'official_detail:joining_date,employee_id,designation_id',
                    'designation:name',
                ])
                    ->select('id', 'company_id', 'branch_id', 'name', 'employee_code', 'image', 'phone_number')
                    ->paginate($request->per_page);
                $employee = $employee->appends($request->all());
                $data['employee'] = $employee;

            }

            return response()->json([
                'success' => true,
                'message' => "Employee list",
                'data' => $data
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage(),
            ], 200);
        }
    }

    public function add(Request $request)
    {
        //        Log::debug($request->all());
        $validator = Validator::make($request->all(), [
            'company_id' => 'required',
            'branch_id' => 'required',
            'employee_code' => 'required|unique:employees',
            'name' => 'required',
            'fathers_name' => 'required',
            'father_contact_number' => 'numeric|digits:11',
            'mothers_name' => 'required',
            'date_of_birth' => 'required',
            'gender' => 'required',
            'religion' => 'required',
            'phone_number' => 'required|numeric|digits:11|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required',
            'marital_status' => 'required',
            'present_address' => 'required',
            'permanent_address' => 'required',
            'nid_image' => 'file|mimes:png,jpg,svg,jpeg',
            'image' => 'file|mimes:png,jpg,svg,jpeg',
            'emergency_contact_number' => 'numeric|digits:11',
            'joining_date' => 'required',
            'department_id' => 'required',
            'designation_id' => 'required',
            'salary' => 'required',
            'probation_period' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => 401,
                'message' => "Validator Error.",
                'error' => $validator->errors(),
            ], 401);
        };
        \DB::beginTransaction();
        try {
            $data['user'] = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'phone_number' => $request->phone_number,
            ]);
            $user = $data['user'];

            $nid_image = null;
            $image = null;
            if ($request->hasFile('nid_image')) {
                $nid_image = $this->uploadImage($request->file('nid_image'), 'employee');
            }
            if ($request->hasFile('image')) {
                $image = $this->uploadImage($request->file('image'), 'employee');
            }
            $data['employee'] = Employee::create([
                'employee_code' => $request->employee_code,
                'user_id' => $user->id,
                'date' => date('Y-m-d'),
                'company_id' => $request->company_id,
                'branch_id' => $request->branch_id,
                'name' => $request->name,
                'fathers_name' => $request->fathers_name,
                'father_contact_number' => $request->father_contact_number,
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
                'passport_no' => $request->passport_no,
                'marital_status' => $request->marital_status,
                'spouse_name' => $request->spouse_name,
                'present_address' => $request->present_address,
                'permanent_address' => $request->permanent_address,
                'nid_image' => $nid_image,
                'image' => $image,
                'emergency_contact_number' => $request->emergency_contact_number,
                'bio' => $request->bio,
            ]);
            $employee = $data['employee'];

            $data['official_details'] = EmployeeOfficialDetail::create([
                'employee_id' => $employee->id,
                'joining_date' => $request->joining_date,
                'department_id' => $request->department_id,
                'designation_id' => $request->designation_id,
                'salary' => $request->salary,
                'probation_period' => $request->probation_period,
                'probable_permanent_date' => $request->probable_permanent_date,
                'probable_permanent_increment_date' => $request->probable_permanent_increment_date,
                'bonus_active_date' => $request->bonus_active_date,
            ]);

            $educationDetailsArray = array();
            if (is_array($request->education)) {
                foreach ($request->education as $educationData) {
                    $file = $educationData['education_certificate'] ?? null;
                    $education_certificate = null;
                    if ($file !== 'null') {
                        $education_certificate = $this->uploadFile($file, 'education');
                    }
                    $education = array(
                        'employee_id' => $employee->id,
                        'degree_name' => $educationData['degree_name'],
                        'institute_name' => $educationData['institute_name'],
                        'passing_year' => $educationData['passing_year'],
                        'result' => $educationData['result'],
                        'board_name' => $educationData['board_name'],
                        'education_certificate' => $education_certificate,
                        'award' => $educationData['award'],
                    );
                    $data = EmployeeEducationDetail::create($education);
                    array_push($educationDetailsArray, $data);
                }
            }
            $experienceDetailsArray = array();
            if (is_array($request->experience)) {
                foreach ($request->experience as $experienceData) {
                    $experience_certificate = null;
                    $file = $experienceData['experience_certificate'];
                    if ($file !== 'null') {
                        $experience_certificate = $this->uploadFile($file, 'experience');
                    }
                    $experience = array(
                        'employee_id' => $employee->id,
                        'year_of_experience' => $experienceData['year_of_experience'],
                        'organization_name' => $experienceData['organization_name'],
                        'experience_certificate' => $experience_certificate,
                    );
                    $data = EmployeeExperienceDetail::create($experience);
                    array_push($experienceDetailsArray, $data);
                }
            }
            $trainingDetailsArray = array();
            if (is_array($request->training)) {
                foreach ($request->training as $trainingData) {
                    $training_certificate = null;
                    $file = $trainingData['training_certificate'];
                    if ($file !== 'null') {
                        $training_certificate = $this->uploadFile($file, 'training');
                    }
                    $training = array(
                        'employee_id' => $employee->id,
                        'training_name' => $trainingData['training_name'],
                        'training_period' => $trainingData['training_period'],
                        'training_location' => $trainingData['training_location'],
                        'training_certificate' => $training_certificate,
                        'about_training' => $trainingData['about_training'],
                    );
                    $data = EmployeeTrainingDetail::create($training);
                    array_push($trainingDetailsArray, $data);
                }
            }
            $promotionDetailArray = array();
            if (is_array($request->promotion)) {
                foreach ($request->promotion as $promotionData) {
                    $approval_document = null;
                    $file = $promotionData['approval_document'];
                    if ($file !== 'null') {
                        $approval_document = $this->uploadFile($file, 'promotion');
                    }
                    $promotion = array(
                        'employee_id' => $employee->id,
                        'issue_date' => $promotionData['issue_date'],
                        'effective_date' => $promotionData['effective_date'],
                        'previous_designation_id' => $promotionData['previous_designation_id'],
                        'promoted_designation_id' => $promotionData['promoted_designation_id'],
                        'previous_basic' => $promotionData['previous_basic'],
                        'new_basic' => $promotionData['new_basic'],
                        'increment_amount' => $promotionData['increment_amount'],
                        'gross_salary' => $promotionData['gross_salary'],
                        'approval_document' => $approval_document,
                    );
                    $data = EmployeePromotionDetail::create($promotion);
                    array_push($promotionDetailArray, $data);
                }
            }
            $referenceDetailArray = array();
            if (is_array($request->reference)) {
                if (count($request->reference) >= 2) {
                    foreach ($request->reference as $referenceData) {
                        $reference = array(
                            'employee_id' => $employee->id,
                            'referral_name' => $referenceData['referral_name'],
                            'referral_contact_number' => $referenceData['referral_contact_number'],
                            'relation_with_referral' => $referenceData['relation_with_referral'],
                            'referral_address' => $referenceData['referral_address'],
                        );
                        $data = EmployeeReferenceDetail::create($reference);
                        array_push($referenceDetailArray, $data);
                    }
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => "At least 2 reference is required",
                    ], 200);
                }
            }

            \DB::commit();
            return response()->json([
                'success' => true,
                'message' => "Employee created successfully",
                'data' => $data
            ], 200);
        } catch (\Exception $e) {
            \DB::rollback();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 200);
        }
    }

    public function edit(Request $request)
    {
        try {
            $data['employee'] = Employee::with([
                'company:id,name,company_code',
                'branch:id,name',
                'official_detail:id,joining_date,employee_id,salary,probation_period,probable_permanent_date,probable_permanent_increment_date,bonus_active_date',
                'designation:name',
                'department:name',
                'education:id,employee_id,degree_name,institute_name,passing_year,result,board_name,education_certificate,award',
                'experience:id,employee_id,year_of_experience,organization_name,experience_certificate',
                'training:id,employee_id,training_name,training_period,training_location,training_certificate,about_training',
                'promotion:id,employee_id,issue_date,effective_date,previous_designation_id,promoted_designation_id,previous_basic,new_basic,increment_amount,gross_salary,approval_document',
                'previous_designation:name',
                'promoted_designation:name',
                'reference:id,employee_id,referral_name,referral_contact_number,relation_with_referral,referral_address'
            ])->find($request->id);

            return response()->json([
                'success' => true,
                'message' => "Employee Info",
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
            'fathers_name' => 'required',
            'father_contact_number' => 'numeric|digits:11',
            'mothers_name' => 'required',
            'date_of_birth' => 'required',
            'gender' => 'required',
            'religion' => 'required',
            'phone_number' => 'required|numeric|digits:11',
            'email' => 'required',
            'password' => 'required',
            'marital_status' => 'required',
            'present_address' => 'required',
            'permanent_address' => 'required',
            //            'nid_image' => 'file|mimes:png,jpg,svg,jpeg',
            //            'image' =>  'file|mimes:png,jpg,svg,jpeg',
            'emergency_contact_number' => 'numeric|digits:11',
            'joining_date' => 'required',
            'department_id' => 'required',
            'designation_id' => 'required',
            'salary' => 'required',
            'probation_period' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => 401,
                'message' => "Validator Error.",
                'error' => $validator->errors(),
            ], 401);
        };
        \DB::beginTransaction();
        try {
            $employee = Employee::find($request->id);
            // User Phone Number and Email Unique Verification Code Start
            $phone_number = $request->phone_number;
            $user_id = $employee->user_id;
            $User = User::where('phone_number', $phone_number)->where('id', '!=', $user_id)->first();
            if ($User) {
                return response()->json([
                    'success' => false,
                    'message' => "There is Already A Phone Number With " . $phone_number,

                ], 200);
            }
            $email = $request->email;
            $User = User::where('email', $email)->where('id', '!=', $user_id)->first();
            if ($User) {
                return response()->json([
                    'success' => false,
                    'message' => "There is Already A Email With " . $email,
                ], 200);
            }
            // User Phone Number and Email Unique Verification Code End
            $data = User::where('id', $employee->user_id)->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
            ]);

            if ($request->nid_image !== 'null') {
                if (File::exists($employee->nid_image)) {
                    File::delete($employee->nid_image);
                }
                $nid_image = $this->uploadImage($request->file('nid_image'), 'employee');
            } else {
                $nid_image = $employee->nid_image;
            }
            if ($request->image !== 'null') {
                if (File::exists($employee->image)) {
                    File::delete($employee->image);
                }
                $image = $this->uploadImage($request->file('image'), 'employee');
            } else {
                $image = $employee->image;
            }
            $data = Employee::where('id', $request->id)->update([
                'name' => $request->name,
                'company_id' => $request->company_id,
                'branch_id' => $request->branch_id,
                'fathers_name' => $request->fathers_name,
                'father_contact_number' => $request->father_contact_number,
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
                'passport_no' => $request->passport_no,
                'marital_status' => $request->marital_status,
                'spouse_name' => $request->spouse_name,
                'present_address' => $request->present_address,
                'permanent_address' => $request->permanent_address,
                'nid_image' => $nid_image,
                'image' => $image,
                'emergency_contact_number' => $request->emergency_contact_number,
                'bio' => $request->bio,
            ]);
            $data = EmployeeOfficialDetail::where('employee_id', $request->id)->update([
                'joining_date' => $request->joining_date,
                'department_id' => $request->department_id,
                'designation_id' => $request->designation_id,
                'salary' => $request->salary,
                'probation_period' => $request->probation_period,
                'probable_permanent_date' => $request->probable_permanent_date,
                'probable_permanent_increment_date' => $request->probable_permanent_increment_date,
                'bonus_active_date' => $request->bonus_active_date,
            ]);
            // Education Part Start
            $educationDetailsUpdateArray = [];
            $educationDetailsCreateArray = [];
            foreach ($request->education as $educationData) {
                if (isset($educationData['id'])) {
                    $education_id = $educationData['id'];
                    $foundEducation = EmployeeEducationDetail::where('id', $education_id)->first();
                    $file = $educationData['education_certificate'];
                    if ($file !== 'null') {
                        if (File::exists($foundEducation->education_certificate)) {
                            File::delete($foundEducation->education_certificate);
                        }
                        $education_certificate = $this->uploadFile($file, 'education');
                    } else {
                        $education_certificate = $foundEducation->education_certificate;
                    }
                    $foundEducation->update([
                        'degree_name' => $educationData['degree_name'],
                        'institute_name' => $educationData['institute_name'],
                        'passing_year' => $educationData['passing_year'],
                        'result' => $educationData['result'],
                        'board_name' => $educationData['board_name'],
                        'education_certificate' => $education_certificate,
                        'award' => $educationData['award'],
                    ]);
                    $educationDetailsUpdateArray[] = $foundEducation;
                } else {
                    $education_certificate = null;
                    $file = $educationData['education_certificate'];
                    if ($file !== 'null') {
                        $education_certificate = $this->uploadFile($file, 'education');
                    }
                    $education = EmployeeEducationDetail::create([
                        'employee_id' => $employee->id,
                        'degree_name' => $educationData['degree_name'],
                        'institute_name' => $educationData['institute_name'],
                        'passing_year' => $educationData['passing_year'],
                        'result' => $educationData['result'],
                        'board_name' => $educationData['board_name'],
                        'education_certificate' => $education_certificate,
                        'award' => $educationData['award'],
                    ]);
                    if ($education) {
                        $educationDetailsCreateArray[] = $education;
                    }
                }
            }
            if (is_array($request->remove_education)) {
                foreach ($request->remove_education as $removeData) {
                    if (isset($removeData['id'])) {
                        $remove_id = EmployeeEducationDetail::find($removeData['id']);
                        if ($remove_id) {
                            if (File::exists($remove_id->education_certificate)) {
                                File::delete($remove_id->education_certificate);
                            }
                        }
                        $remove = EmployeeEducationDetail::where('id', $removeData['id'])->delete();
                    }
                }
            }
            // Education Part End
            // Experience Part Start
            $experienceDetailsUpdateArray = [];
            $experienceDetailsCreateArray = [];

            foreach ($request->experience as $experienceData) {
                if (isset($experienceData['id'])) {
                    $experience_id = $experienceData['id'];
                    $foundExperience = EmployeeExperienceDetail::where('id', $experience_id)->first();
                    $file = $experienceData['experience_certificate'];
                    if ($file !== 'null') {
                        if (File::exists($foundExperience->experience_certificate)) {
                            File::delete($foundExperience->experience_certificate);
                        }
                        $experience_certificate = $this->uploadFile($file, 'experience');
                    } else {
                        $experience_certificate = $foundExperience->experience_certificate;
                    }
                    $foundExperience->update([
                        'year_of_experience' => $experienceData['year_of_experience'],
                        'organization_name' => $experienceData['organization_name'],
                        'experience_certificate' => $experience_certificate,
                    ]);
                    $experienceDetailsUpdateArray[] = $foundExperience;
                } else {
                    $experience_certificate = null;
                    $file = $experienceData['experience_certificate'];
                    if ($file !== 'null') {
                        $experience_certificate = $this->uploadFile($file, 'experience');
                    }
                    $experience = EmployeeExperienceDetail::create([
                        'employee_id' => $employee->id,
                        'year_of_experience' => $experienceData['year_of_experience'],
                        'organization_name' => $experienceData['organization_name'],
                        'experience_certificate' => $experience_certificate,
                    ]);
                    if ($experience) {
                        $experienceDetailsCreateArray[] = $experience;
                    }
                }
            }
            if (is_array($request->remove_experience)) {
                foreach ($request->remove_experience as $removeData) {
                    if (isset($removeData['id'])) {
                        $remove_id = EmployeeExperienceDetail::find($removeData['id']);
                        if ($remove_id) {
                            if (File::exists($remove_id->experience_certificate)) {
                                File::delete($remove_id->experience_certificate);
                            }
                        }
                        $remove = EmployeeExperienceDetail::where('id', $removeData['id'])->delete();
                    }
                }
            }
            // Experience Part End
            // Training part Start
            $trainingDetailsUpdateArray = [];
            $trainingDetailsCreateArray = [];
            foreach ($request->training as $trainingData) {
                if (isset($trainingData['id'])) {
                    $training_id = $trainingData['id'];
                    $foundTraining = EmployeeTrainingDetail::where('id', $training_id)->first();
                    $file = $trainingData['training_certificate'];
                    if ($file !== 'null') {
                        if (File::exists($foundTraining->training_certificate)) {
                            File::delete($foundTraining->training_certificate);
                        }
                        $training_certificate = $this->uploadFile($file, 'training');
                    } else {
                        $training_certificate = $foundTraining->training_certificate;
                    }
                    $foundTraining->update([
                        'training_name' => $trainingData['training_name'],
                        'training_period' => $trainingData['training_period'],
                        'training_location' => $trainingData['training_location'],
                        'training_certificate' => $training_certificate,
                        'about_training' => $trainingData['about_training'],
                    ]);
                    $trainingDetailsUpdateArray[] = $foundTraining;
                } else {
                    $training_certificate = null;
                    $file = $trainingData['training_certificate'];
                    if ($file !== 'null') {
                        $training_certificate = $this->uploadFile($file, 'training');
                    }
                    $training = EmployeeTrainingDetail::create([
                        'employee_id' => $employee->id,
                        'training_name' => $trainingData['training_name'],
                        'training_period' => $trainingData['training_period'],
                        'training_location' => $trainingData['training_location'],
                        'training_certificate' => $training_certificate,
                        'about_training' => $trainingData['about_training'],
                    ]);
                    if ($training) {
                        $trainingDetailsCreateArray[] = $training;
                    }
                }
            }
            if (is_array($request->remove_training)) {
                foreach ($request->remove_training as $removeData) {
                    if (isset($removeData['id'])) {
                        $remove_id = EmployeeTrainingDetail::find($removeData['id']);
                        if ($remove_id) {
                            if (File::exists($remove_id->training_certificate)) {
                                File::delete($remove_id->training_certificate);
                            }
                        }
                        $remove = EmployeeTrainingDetail::where('id', $removeData['id'])->delete();
                    }
                }
            }
            // Training Part End
            // Promotion Part Start
            $promotionDetailUpdateArray = [];
            $promotionDetailCreateArray = [];
            foreach ($request->promotion as $promotionData) {
                if (isset($promotionData['id'])) {
                    $promotion_id = $promotionData['id'];
                    $foundPromotion = EmployeePromotionDetail::where('id', $promotion_id)->first();
                    $file = $promotionData['approval_document'];
                    if ($file !== 'null') {
                        if (File::exists($foundPromotion->approval_document)) {
                            File::delete($foundPromotion->approval_document);
                        }
                        $approval_document = $this->uploadFile($file, 'promotion');
                    } else {
                        $approval_document = $foundPromotion->approval_document;
                    }
                    $foundPromotion->update([
                        'issue_date' => $promotionData['issue_date'],
                        'effective_date' => $promotionData['effective_date'],
                        'previous_designation_id' => $promotionData['previous_designation_id'],
                        'promoted_designation_id' => $promotionData['promoted_designation_id'],
                        'previous_basic' => $promotionData['previous_basic'],
                        'new_basic' => $promotionData['new_basic'],
                        'increment_amount' => $promotionData['increment_amount'],
                        'gross_salary' => $promotionData['gross_salary'],
                        'approval_document' => $approval_document,
                    ]);
                    $promotionDetailUpdateArray[] = $foundPromotion;
                } else {
                    $approval_document = null;
                    $file = $promotionData['approval_document'];
                    if ($file !== 'null') {
                        $approval_document = $this->uploadFile($file, 'promotion');
                    }
                    $promotion = EmployeePromotionDetail::create([
                        'employee_id' => $employee->id,
                        'issue_date' => $promotionData['issue_date'],
                        'effective_date' => $promotionData['effective_date'],
                        'previous_designation_id' => $promotionData['previous_designation_id'],
                        'promoted_designation_id' => $promotionData['promoted_designation_id'],
                        'previous_basic' => $promotionData['previous_basic'],
                        'new_basic' => $promotionData['new_basic'],
                        'increment_amount' => $promotionData['increment_amount'],
                        'gross_salary' => $promotionData['gross_salary'],
                        'approval_document' => $approval_document,
                    ]);
                    if ($promotion) {
                        $promotionDetailCreateArray[] = $promotion;
                    }
                }
            }
            if (is_array($request->remove_promotion)) {
                foreach ($request->remove_promotion as $removeData) {
                    if (isset($removeData['id'])) {
                        $remove_id = EmployeePromotionDetail::find($removeData['id']);
                        if ($remove_id) {
                            if (File::exists($remove_id->approval_document)) {
                                File::delete($remove_id->approval_document);
                            }
                        }
                        $remove = EmployeePromotionDetail::where('id', $removeData['id'])->delete();
                    }
                }
            }
            // Promotion Part End
            // Reference Part Start
            $referenceDetailUpdateArray = [];
            $referenceDetailCreateArray = [];
            if (count($request->reference) >= 2) {
                foreach ($request->reference as $referenceData) {
                    if (isset($referenceData['id'])) {
                        $reference_id = $referenceData['id'];
                        $foundReference = EmployeeReferenceDetail::where('id', $reference_id)->first();
                        $foundReference->update([
                            'referral_name' => $referenceData['referral_name'],
                            'referral_contact_number' => $referenceData['referral_contact_number'],
                            'relation_with_referral' => $referenceData['relation_with_referral'],
                            'referral_address' => $referenceData['referral_address'],
                        ]);
                        $referenceDetailUpdateArray[] = $foundReference;
                    } else {
                        $reference = EmployeeReferenceDetail::create([
                            'employee_id' => $employee->id,
                            'referral_name' => $referenceData['referral_name'],
                            'referral_contact_number' => $referenceData['referral_contact_number'],
                            'relation_with_referral' => $referenceData['relation_with_referral'],
                            'referral_address' => $referenceData['referral_address'],
                        ]);
                        if ($reference) {
                            $referenceDetailCreateArray[] = $reference;
                        }
                    }
                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => "At least 2 reference is required",
                ], 200);
            }

            \DB::commit();
            return response()->json([
                'success' => true,
                'message' => "Employee Updated Successfully",
                'data' => $data
            ], 200);
        } catch (\Exception $e) {
            \DB::rollback();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 200);
        }
    }

    public function getEmployeeListByBranch(Request $request)
    {
        try {
            $data = Employee::with(['designation' => function ($query) {
                $query->select('name');
            }])
                ->where('branch_id', $request->branch_id)
                ->select('id', 'name')
                ->get();


            return response()->json([
                'success' => true,
                'message' => "Employee Info",
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 200);
        }
    }

    public function getEmployeeDetailsToAssignRole(Request $request)
    {
        try {
            $data = Employee::with([
                'designation:name'
            ])
                ->select('id', 'employee_code', 'name', 'email', 'user_id')
                ->find($request->employee_id);
            return response()->json([
                'success' => true,
                'message' => "Employee Detail to Assign Role",
                'data' => $data,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 200);
        }
    }

    public function getSingleEmployeeDetailsByUser(Request $request)
    {
        try {
            $employee = Employee::with([
                'company:id,name,company_code',
                'branch:id,name',
                'designation:name',
            ])
                ->where('user_id', $request->user_id)
                ->select('id', 'name', 'employee_code', 'email', 'phone_number', 'company_id', 'branch_id')
                ->first();

            $user = User::find($request->user_id);
            $role_name = $user->getRoleNames();
            $role_name = $role_name[0];
            $role = Role::where('name', $role_name)->first();
            $role_id = $role->id;

            $data = [
                'user_id' => $request->user_id,
                'company_name' => $employee->company->name,
                'company_code' => $employee->company->company_code,
                'branch_name' => $employee->branch->name,
                'employee_name' => $employee->name,
                'designation' => $employee->designation[0]->name,
                'employee_code' => $employee->employee_code,
                'email' => $employee->email,
                'phone_number' => $employee->phone_number,
                'remark' => $user->remarks,
                'role_info' => [
                    'id' => $role_id,
                    'role_name' => $role_name,
                ]
            ];
            return response()->json([
                'success' => true,
                'message' => "Employee Detail to view",
                'data' => $data,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 200);
        }
    }

    public function getEmployeeDocument(Request $request)
    {
        try {
            $data['Personal_Details_Document'] = Employee::where('id', $request->id)
                ->select('id', 'nid_image', 'image')
                ->get();
            $data['Education_Information_Document'] = EmployeeEducationDetail::where('employee_id', $request->id)->whereNotNull('education_certificate')
                ->select('id', 'education_certificate')
                ->get();
            $data['Experience_Information_Document'] = EmployeeExperienceDetail::where('employee_id', $request->id)->whereNotNull('experience_certificate')
                ->select('id', 'experience_certificate')
                ->get();
            $data['Training_Information_Document'] = EmployeeTrainingDetail::where('employee_id', $request->id)->whereNotNull('training_certificate')
                ->select('id', 'training_certificate')
                ->get();
            $data['Promotion_Information_Document'] = EmployeePromotionDetail::where('employee_id', $request->id)->whereNotNull('approval_document')
                ->select('id', 'approval_document')
                ->get();
            $data['Others_Documents'] = EmployeeDocument::where('employee_id', $request->id)
                ->select('id', 'document')
                ->get();
            return response()->json([
                'success' => true,
                'message' => "Employee Documents",
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
                            $document = $this->uploadFile($file, 'employee_document');
                        } else {
                            $document = $this->uploadImage($file, 'employee_document');
                        }
                    }
                    $other_document = array(
                        'employee_id' => $documentData['id'],
                        'document' => $document,
                    );
                    $data['document'] = EmployeeDocument::create($other_document);
                }
            }
            return response()->json([
                'success' => true,
                'message' => "Employee Documents Uploaded Successfully",
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
            $others_document = EmployeeDocument::find($request->id);
            if ($others_document) {
                if (File::exists($others_document->document)) {
                    File::delete($others_document->document);
                }
            }
            $data = $others_document->delete();
            return response()->json([
                'success' => true,
                'message' => "Employee Others Document Deleted Successfully",
                'data' => $data
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 200);
        }
    }

    public function deletePersonalDetailsDocument(Request $request)
    {
        try {
            $employee_document = Employee::find($request->id);
            if ($request->column == 'nid_image') {
                if ($employee_document) {
                    if (File::exists($employee_document->nid_image)) {
                        File::delete($employee_document->nid_image);
                    }
                }
                $data = Employee::where('id', $request->id)->update([
                    'nid_image' => null,
                ]);
                return response()->json([
                    'success' => true,
                    'message' => "Employee Nid Image Deleted Successfully",
                    'data' => $data
                ], 200);
            } else {
                if ($employee_document) {
                    if (File::exists($employee_document->image)) {
                        File::delete($employee_document->image);
                    }
                }
                $data = Employee::where('id', $request->id)->update([
                    'image' => null,
                ]);
                return response()->json([
                    'success' => true,
                    'message' => "Employee Image Deleted Successfully",
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
            $education_document = EmployeeEducationDetail::find($request->id);
            if ($education_document) {
                if (File::exists($education_document->education_certificate)) {
                    File::delete($education_document->education_certificate);
                }
            }
            $data = EmployeeEducationDetail::where('id', $request->id)->update([
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
            $experience_document = EmployeeExperienceDetail::find($request->id);
            if ($experience_document) {
                if (File::exists($experience_document->experience_certificate)) {
                    File::delete($experience_document->experience_certificate);
                }
            }
            $data = EmployeeExperienceDetail::where('id', $request->id)->update([
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

    public function deleteTrainingDocument(Request $request)
    {
        try {
            $training_document = EmployeeTrainingDetail::find($request->id);
            if ($training_document) {
                if (File::exists($training_document->training_certificate)) {
                    File::delete($training_document->training_certificate);
                }
            }
            $data = EmployeeTrainingDetail::where('id', $request->id)->update([
                'training_certificate' => null,
            ]);
            return response()->json([
                'success' => true,
                'message' => "Training Certificate Deleted Successfully",
                'data' => $data
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 200);
        }
    }

    public function deletePromotionDocument(Request $request)
    {
        try {
            $promotion_document = EmployeePromotionDetail::find($request->id);
            if ($promotion_document) {
                if (File::exists($promotion_document->approval_document)) {
                    File::delete($promotion_document->approval_document);
                }
            }
            $data = EmployeePromotionDetail::where('id', $request->id)->update([
                'approval_document' => null,
            ]);
            return response()->json([
                'success' => true,
                'message' => "Promotion Document Deleted Successfully",
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
