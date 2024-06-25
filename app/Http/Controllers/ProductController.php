<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Traits\UploadTraits;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    use UploadTraits;
    public function list(Request $request){
        try{
            $product = Product::with([
                'company:id,name',
                'unit:id,name'
            ])
                ->orderBy('id', 'desc')
                ->select('id', 'company_id', 'name', 'unit_id', 'product_code');
            $product = $product->paginate($request->per_page);
            $product=$product->appends($request->all());
            $data['product'] = $product;
            return response()->json([
                'success' => true,
                'message' => 'Product List',
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
    public function add(Request $request){
        try{
            $validator = Validator::make($request->all(),[
                'company_id' => 'required',
                'name' => 'required',
                'unit_id' => 'required',
                'product_code' => 'required|unique:products',
                'image' => 'required|mimes:png,jpg,svg,jpeg',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'success' => 401,
                    'message' => "Validation Error.12",
                    'error' => $validator->errors(),
                ], 401);
            }
            if($request->hasFile('image')){
                $image = $this->uploadImage($request->file('image'), 'product');
            }
            $data = Product::create([
                'company_id' => $request->company_id,
                'name' => $request->name,
                'unit_id' => $request->unit_id,
                'product_code' => $request->product_code,
                'image' => $image,
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Product Created Successfully',
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
            $data['product'] = Product::with([
                'company:id,name',
                'unit:id,name'
            ])->select('id', 'company_id', 'name', 'unit_id', 'product_code', 'image')->find($request->id);
            return response()->json([
                'success' => true,
                'message' => 'Product Info',
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
                'unit_id' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'success' => 401,
                    'message' => "Validation Error.",
                    'error' => $validator->errors(),
                ], 401);
            }
//            return $request->all();
            $product = Product::find($request->id);
            if ($request->image !== 'null') {
                $image_type = $request->image->getClientOriginalExtension();
                if($image_type != "jpg" && $image_type != "png" && $image_type != "jpeg" && $image_type != "svg"){
                    return response()->json([
                        'success' => false,
                        'message' => "Image type must be png, jpg, jpeg, svg",
                    ], 200);
                }
                if (File::exists($product->image)) {
                    File::delete($product->image);
                }
                $image = $this->uploadImage($request->file('image'), 'product');
            }
            else{
                $image = $product->image;
            }
            $data = Product::where('id', $request->id)->update([
                'company_id' => $request->company_id,
                'name' => $request->name,
                'unit_id' => $request->unit_id,
                'image' => $image,
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Product Updated Successfully',
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
    public function getProductByCompany(Request $request){
        try {
            $data = Product::with([
                'unit:id,name'
            ])->where('company_id', $request->company_id)
                ->select('id','name','unit_id')
                ->get();

            return response()->json([
                'success' => true,
                'message' => "Product Info",
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
