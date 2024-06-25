<?php

namespace App\Custom_Services;

use Exception;

class Service1
{
    public static function process_associative_array_and_returning_that_array_like_retrieving_records_from_table($assosiative_array)
    {
        asort($assosiative_array);

        $final_array = [];

        foreach ($assosiative_array as $key => $val) {
            $final_array[] = [
                "id" => $key,
                "value" => $val
            ];
        }

        return $final_array;
    }

}
