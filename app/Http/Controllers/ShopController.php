<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use App\Traits\UploadTraits;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class ShopController extends Controller
{
    //
    use UploadTraits;
    public function list(Request $request){
        try{
            $shop = Shop::with([
                'company:id,name'
            ])
                ->orderBy('id', 'desc')
                ->select('id', 'company_id', 'name', 'shop_code', 'owner', 'location', 'phone_number_1', 'phone_number_2');
            $shop = $shop->paginate($request->per_page);
            $shop=$shop->appends($request->all());
            $data['shop'] = $shop;
            return response()->json([
                'success' => true,
                'message' => 'Shop List',
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
            $validator = Validator::make($request->all(), [
                'company_id' => 'required',
                'name' => 'required',
                'shop_code' => 'required|unique:shops',
                'owner' => 'required',
                'location' => 'required',
                'phone_number_1' => 'required|numeric|digits:11',
                'phone_number_2' => 'required|numeric|digits:11',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'success' => 401,
                    'message' => "Validation Error.",
                    'error' => $validator->errors(),
                ], 401);
            }
            $image = null;
            if($request->hasFile('image')){
                $image = $this->uploadFile($request->file('image'), 'shop');
            }
            $data = Shop::create([
                'company_id' => $request->company_id,
                'name' => $request->name,
                'shop_code' => $request->shop_code,
                'owner' => $request->owner,
                'location' => $request->location,
                'phone_number_1' => $request->phone_number_1,
                'phone_number_2' => $request->phone_number_2,
                'image' => $image,
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Shop Created Successfully',
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
            $data['shop'] = Shop::with([
                'company:id,name'
            ])->select('id', 'company_id', 'name', 'shop_code', 'owner', 'location', 'phone_number_1', 'phone_number_2', 'image')->find($request->id);
            return response()->json([
                'success' => true,
                'message' => 'Shop Info',
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
                'owner' => 'required',
                'location' => 'required',
                'phone_number_1' => 'required|numeric|digits:11',
                'phone_number_2' => 'required|numeric|digits:11',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'success' => 401,
                    'message' => "Validation Error.",
                    'error' => $validator->errors(),
                ], 401);
            }
            $shop = Shop::find($request->id);
            if ($request->image !== 'null') {
                if (File::exists($shop->image)) {
                    File::delete($shop->image);
                }
                $image = $this->uploadFile($request->file('image'), 'shop');
            }
            else{
                $image = $shop->image;
            }
            $data = Shop::where('id', $request->id)->update([
                'company_id' => $request->company_id,
                'name' => $request->name,
                'owner' => $request->owner,
                'location' => $request->location,
                'phone_number_1' => $request->phone_number_1,
                'phone_number_2' => $request->phone_number_2,
                'image' => $image,
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Shop Updated Successfully',
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
    public function getShopByCompany(Request $request){
        try {
            $data = Shop::where('company_id', $request->company_id)
                ->select('id','name')
                ->get();
            return response()->json([
                'success' => true,
                'message' => "Shop Info",
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
