<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller {
  public function register(Request $request) {
    try {
      $rules = [
        'name' => 'required|string|max:255',
        'lastname' => 'required|string|max:255',
        'email' => 'required|string|email|unique:users|max:255',
        'password' => 'required|string|min:8'
      ];
      $validator = Validator::make($request->input(), $rules);
      if ($validator->fails()) {
        return response()->json([
          'status' => false,
          'message' => 'validation error',
          'errors' => $validator->errors()
        ], 401);
      }
      $user = User::create([
        'name' => $request->name,
        'lastname' => $request->lastname,
        'email' => $request->email,
        'password' => Hash::make($request->password)
      ]);
      return response()->json([
        'status' => true,
        'message' => 'User Created Successfully',
        'token' => $user->createToken("API TOKEN")->plainTextToken
      ], 200);
    } catch (\Throwable $th) {
      return response()->json([
        'status' => false,
        'message' => $th->getMessage()
      ], 500);
    }
  }


  public function login(Request $request) {
    try {
      $rules = [
        'email' => 'required|email',
        'password' => 'required'
      ];
      $validator = Validator::make($request->input(), $rules);
      if ($validator->fails()) {
        // return response()->json(['status' => false, 'errors' => $validator->errors()->all()], 401);
        return response()->json([
          'status' => false,
          'message' => 'validation error',
          'errors' => $validator->errors()
        ], 401);
      }
      if (!Auth::attempt($request->only('email', 'password'))) {
        return response()->json([
          'status' => false,
          'message' => 'Email & Password does not match with our record.',
        ], 401);
      }
      $user = User::where('email', $request['email'])->firstOrFail();

      return response()->json([
        'status' => true,
        'message' => 'User Logged In Successfully',
        'token' => $user->createToken("API TOKEN")->plainTextToken,
        'user' => $user
      ], 200);
    } catch (\Throwable $th) {
      return response()->json([
        'status' => false,
        'message' => $th->getMessage()
      ], 500);
    }
  }

  public function logout(Request $request) {
    $request->user()->tokens()->delete();
    return response()->json([
      'status' => true,
      'message' => 'Has cerrado satisfactoriamente tu sesion',
    ], 200);
  }
}
