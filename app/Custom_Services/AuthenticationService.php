<?php

namespace App\Custom_Services;

use Exception;

class AuthenticationService
{
    public static function get_upper_user_type_($user_type_)
    {
        $upper_user_type_ = ucfirst($user_type_);
        return $upper_user_type_;
    }

    public static function logout($user_type_)
    {
        try {

        $upper_user_type_ = self::get_upper_user_type_($user_type_);

        if (auth($user_type_)->user()) {

            auth($user_type_)->user()->tokens()->delete();

            return response([
                'success' => true,
                'message' => 'Successfully Logged Out as ' . $upper_user_type_ . '!!',
            ], 200);
        } else {
            return response([
                'success' => false,
                'message' => 'Not Possible to Log Out as ' . $upper_user_type_ . '!!',
            ], 200);
        }

        }
        catch (Exception $e){

            return response([
                'success' => false,
                'message' => $e->getMessage(),
            ], 200);

        }
    }

    public static function login($user_type_model_class, $hash_class, $request, $user_type_)
    {
        try {
            $request->validate([
                'email_or_phone_number' => 'required',
                'password' => 'required',
            ]);

            $upper_user_type_ = self::get_upper_user_type_($user_type_);

            $user = $user_type_model_class::where('email', $request->email_or_phone_number)->first();

            if ($user == null) {
                $user = $user_type_model_class::where('phone_number', $request->email_or_phone_number)->first();
            }

            if (!$user || !$hash_class::check($request->password, $user->password)) {
                return response([
                    'success' => false,
                    'message' => 'The provided credentials are incorrect as ' . $upper_user_type_ . '.',
                ], 200);
            }

            $token = $user->createToken($request->email_or_phone_number)->plainTextToken;

            return response([
                'success' => true,
                $user_type_ => $user,
                'token' => $token,
            ], 200);

        }
        catch (Exception $e){

            return response([
                'success' => false,
                'message' => $e->getMessage(),
            ], 200);
        }
    }

}
