<?php

namespace App\Traits;
use Intervention\Image\Facades\Image;

trait UploadTraits{

//    public function uploadImage($image, $path){
//        $file_name = date('Ymd-his') . '.' . $image->getClientOriginalExtension();
//        $destinationPath = 'images/' . $path . '/' . $file_name;
//        Image::make($image->getRealPath())->resize(400, 300)->save(storage_path($destinationPath));
//        return 'storage/' . $destinationPath;
//    }
//    public function uploadFile($file, $path){
//        $file_name = date('Ymd-his') . '.' . $file->getClientOriginalExtension();
//        $destinationPathDb = 'images/' . $path . '/' . $file_name;
//        $destinationPath = storage_path('images/' . $path . '/');
//        $file->move($destinationPath, $file_name);
//        return 'storage/' . $destinationPathDb;
//    }
    public function uploadImage($image, $path){
        $file_name=$image->getClientOriginalName();
        $file_name = pathinfo($file_name,PATHINFO_FILENAME);
        $file_name=$file_name.'_'.date('Ymd_his').'.' . $image->getClientOriginalExtension();
        $destinationPath = 'images/' . $path . '/' . $file_name;
        Image::make($image->getRealPath())->resize(400, 300)->save(storage_path($destinationPath));
        return 'storage/' . $destinationPath;
    }
    public function uploadFile($file, $path){
        $file_name=$file->getClientOriginalName();
        $file_name = pathinfo($file_name,PATHINFO_FILENAME);
        $file_name=$file_name.'_'.date('Ymd_his').'.' . $file->getClientOriginalExtension();
        $destinationPathDb = 'images/' . $path . '/' . $file_name;
        $destinationPath = storage_path('images/' . $path . '/');
        $file->move($destinationPath, $file_name);
        return 'storage/' . $destinationPathDb;
    }
    public function expenseSectorArray($get_value_from_array=false){
        $expense_sectors = array(
            '1' => 'product expense',
            '2' => 'employee expense',
            '3' => 'rental expense',
            '4' => 'utility expense',
            '5' => 'vehicle expense',
            '6' => 'driver expense',
            '7' => 'transportation expense',
            '8' => 'others expense',
        );

        if($get_value_from_array){
           return $expense_sectors;
        }

        $expense_sectors_new = array();
        foreach ($expense_sectors as $id => $expense_sector) {
            $expense_sectors_new[] = array('id' => $id, 'name' => $expense_sector);
        }
        return $expense_sectors_new;
    }

}
