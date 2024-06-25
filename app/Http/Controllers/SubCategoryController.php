<?php

namespace App\Http\Controllers;

use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SubCategoryController extends Controller
{
    public function list(Request $request){
        try {
            $sub_category=SubCategory::with([
                'category:id,name'
            ])
                ->orderby('id', 'desc')->select('id', 'name', 'category_id');
            $sub_category = $sub_category->paginate($request->per_page);
            $sub_category=$sub_category->appends($request->all());
            $data['sub_category'] = $sub_category;
            return response()->json([
                'success' => true,
                'message' => "Sub Category List",
                'data' => $data
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 200);
        }
    }
    public function add(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'category_id' => 'required',
                'name' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'success' => 401,
                    'message' => "Validation Error.",
                    'error' => $validator->errors(),
                ], 401);
            }
            $data = SubCategory::create([
                'category_id' => $request->category_id,
                'name' => $request->name,
            ]);
            return response()->json([
                'success' => true,
                'message' => "Sub Category created successfully",
                'data' => $data
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 200);
        }
    }
    public function edit(Request $request){
        try{
            $data = SubCategory::with([
                'category:id,name'
            ])->find($request->id);

            return response()->json([
                'success' => true,
                'message' => "Single Sub Category Info",
                'data' => $data
            ], 200);
        }
        catch (\Exception $e){
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 200);
        }
    }
    public function update(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'category_id' => 'required',
                'name' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'success' => 401,
                    'message' => "Validation Error.",
                    'error' => $validator->errors(),
                ], 401);
            }
            $data = SubCategory::where('id', $request->id)->update([
                'category_id' => $request->category_id,
                'name' => $request->name,
            ]);
            return response()->json([
                'success' => true,
                'message' => "Sub Category Updated Successfully",
                'data' => $data
            ], 200);
        }
        catch (\Exception $e){
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 200);
        }
    }
    public function getSubCategoryListByCategory(Request $request){
        try{
            $data = SubCategory::where('category_id', $request->category_id)
            ->select('id', 'name')->get();
            return response()->json([
                'success' => true,
                'message' => "Sub Category List by Category Id",
                'data' => $data
            ], 200);
        }
        catch (\Exception $e){
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 200);
        }
    }
}
