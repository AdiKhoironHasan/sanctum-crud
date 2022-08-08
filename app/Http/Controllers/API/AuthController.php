<?php

namespace App\Http\Controllers\API;

use App\Helpers\DTO;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\Book;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
            return DTO::ResponseDTO($validator->errors(), null, null, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {

            // DB::transaction(function () {

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);

            // Book::created();

            // });
            $token = $user->createToken('auth_token')->plainTextToken;
        } catch (\Throwable $th) {
            return DTO::ResponseDTO('Register Failed, ' . $th->getMessage(), null, null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return DTO::ResponseDTO('Register Succesfully', $token, $user, Response::HTTP_CREATED);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:8'
        ]);

        if ($validator->fails()) {
            return DTO::ResponseDTO($validator->errors(), null, null, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if (!Auth::attempt($request->only('email', 'password'))) {
            return DTO::ResponseDTO('Unauthorized', null, null, Response::HTTP_UNAUTHORIZED);
        }

        try {
            $user = User::where('email', $request['email'])->firstOrFail();

            $token = $user->createToken('auth_token')->plainTextToken;
        } catch (\Throwable $th) {
            return DTO::ResponseDTO('Login Failed', null, null, Response::HTTP_NOT_FOUND);
        }

        return DTO::ResponseDTO('Login Succesfully', $token, $user, Response::HTTP_OK);
    }

    // method for user logout and delete token
    public function logout(Request $request)
    {
        try {
            $user = $request->user();
            $user->currentAccessToken()->delete();
        } catch (\Throwable $th) {
            return DTO::ResponseDTO('Logout Failed', null, null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return DTO::ResponseDTO('Logout Sucesfully', null, $user, Response::HTTP_OK);
    }
}
