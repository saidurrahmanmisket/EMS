<?php

namespace App\Http\Controllers\Auth;

use App\Custom_Services\AuthenticationService;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;


class UserAuthController extends Controller
{

    public function logout()
    {
        return AuthenticationService::logout(user_type_:'user');
    }

    public function login(Request $request)
    {
        return AuthenticationService::login(user_type_model_class:User::class, hash_class:Hash::class, request:$request, user_type_:'user');
    }

}
