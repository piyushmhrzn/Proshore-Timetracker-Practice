<?php

namespace App\Http\Controllers;

use App\Http\Requests\PasswordResetRequest;
use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserStoreRequest;
use App\Mail\UserRegistered;
use Illuminate\Http\Request;
use App\Services\UserService;
use Mockery\Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    public function registerUser(UserStoreRequest $request)
    {
        try {
            $validatedUserRegister = $request->validated();

            // create a function for slug
            $user = UserService::saveUserData($validatedUserRegister);
            $result = [
                'status' => 200,
                'user' => $user,
            ];
        } catch (Exception $e) {
            $result = [
                'status' => 403,
                'message' => $e->getMessage()
            ];
        }
        return response()->json($result, $result['status']);
    }

    public function loginUser(UserLoginRequest $request)
    {
        try {
            $validatedUserCreds = $request->validated();
            $user = UserService::getUserWithCreds($validatedUserCreds);

            // Check if user has an existing access token
            // $existingToken = $user->tokens()->where('name', 'auth_token')->first();
            // // dd($existingToken->token);
            
            // if ($existingToken) {
            //     return response()->json([
            //         'message' => 'User already logged in'
            //     ]);
            // } else {
            //     $token = $user->createToken('auth_token');
            // }

            $token = $user->createToken('auth_token');
            $result = [
                'status' => 200,
                'user' => $user,
                'access_token' => $token->plainTextToken,
                'token_type' => 'Bearer',
            ];

        } catch (Exception $e) {
            $result = [
                'status' => 401,
                'error' => $e->getMessage()
            ];
        }
        return response()->json($result, $result['status']);
    }

    public function logoutUser(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'message' => 'User logged out sucessfully'
        ], 200);

    }

    public function forgotPass(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required | email'
        ]);
        $validatedForgetPass = $request->only('email');
        $status = UserService::forgotPassword($validatedForgetPass);
        if (!$status) {
            return response()->json([
                'message' => 'User with given email address not found'
            ], 404);
        }
        return response()->json([
            'message' => 'Reset email sent successfully'
        ], 200);
    }

    public function resetPass(PasswordResetRequest $request): JsonResponse
    {
        $validatedResetPass = $request->safe()->all();
        $status = UserService::resetPassword($validatedResetPass);
        if (!$status) {
            return response()->json([
                'message' => 'Could not reset password. Please check your token or email address'
            ], 404);
        }
        return response()->json([
            'message' => 'Password reset successfully'
        ], 200);
    }
}
