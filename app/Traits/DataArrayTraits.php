<?php

namespace App\Traits;
use Intervention\Image\Facades\Image;

trait DataArrayTraits{

    public function processArray($get_value_from_array,$datas){
        if($get_value_from_array){
            return $datas;
        }

        $datas_new = array();

        foreach ($datas as $id => $name) {
            $datas_new[] = array('id' => $id, 'name' => $name);
        }
        return $datas_new;
    }

    public function expenseCategoryArray($get_value_from_array=false){

        $datas = array(
            '1' => 'expense for personal',
            '2' => 'expense for business',
            '3' => 'expense for sale',
        );

       return $this->processArray($get_value_from_array,$datas);
    }

    public function employeeExpenseCategoryArray($get_value_from_array=false){

        $datas = array(
            '1' => 'salary',
            '2' => 'bonus',
            '3' => 'commission',
            '4' => 'ta/da',
        );

        return $this->processArray($get_value_from_array,$datas);
    }

    public function employeeExpenseTypeArray($get_value_from_array=false){

        $datas = array(
            '1' => 'advance',
            '2' => 'regular',
        );

        return $this->processArray($get_value_from_array,$datas);
    }

    public function deductionReasonArray($get_value_from_array=false){

        $datas = array(
            '1' => 'absent',
            '2' => 'leave',
            '3'=> 'late entry',
            '4'=>'adv installment'
        );

        return $this->processArray($get_value_from_array,$datas);
    }
}
