<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Company;
use App\Models\Employee;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public static function generateRandomString($length = 20)
    {
        $characters = 'ABCDEFGHIJKLMNPQRSTUVWXYZabcdefghijklmnopurstuvwz';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    function returnUniqueCompanyId()
    {
        $lastCompany = Company::orderBy('id', 'desc')->first();
        if (!empty($lastCompany)) {
            $get_serial = $lastCompany->id + 1;
            $random_string = 'CO' . strtoupper($this->generateRandomString(3));
            $get_serial = strtoupper(base_convert(base_convert($get_serial, 36, 10) + 1, 10, 36));
            $company_id = $random_string . $get_serial;
        } else {
            $company_id = 'CORMJ1';
        }
        return $company_id;
    }

    function returnUniqueBranchId()
    {
        $lastBranch = Branch::orderBy('id', 'desc')->first();
        if (!empty($lastBranch)) {
            $get_serial = $lastBranch->id + 1;
            $random_string = 'BR' . strtoupper($this->generateRandomString(3));
            $get_serial = strtoupper(base_convert(base_convert($get_serial, 36, 10) + 1, 10, 36));
            $branch_id = $random_string . $get_serial;
        } else {
            $branch_id = 'BR0001';
        }
        return $branch_id;
    }

}
