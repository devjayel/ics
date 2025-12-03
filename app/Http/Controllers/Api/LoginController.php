<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Personnel;
use App\Models\Rul;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Inertia\Response;
use Str;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', Password::defaults()],
        ]);

        if (Auth::attempt($credentials)) {

            return response()->json([
                'success' => true,
                'token' => Auth::user()->createToken('api-token')->plainTextToken,
                'user' => Auth::user(),
                'message' => 'Login successful'
            ], 200);
        }


        return response()->json([
            'success' => false,
            'message' => 'The provided credentials do not match our records.'
        ], 422);
    }

    public function loginThroughApp(Request $request){
        $credentials = $request->validate([
            'serial_number' => ['required', 'string'],
        ]);

        $personnel = Personnel::where('serial_number', $credentials['serial_number'])->first();
        if ($personnel) {
            //generate token
            $token = Str::random(60);
            Personnel::where('id', $personnel->id)->update(['token' => $token]);

            return response()->json([
                'success' => true,
                'token' => $token,
                'user' => $personnel,
                'role' => 'personnel',
            ], 200);
        }

        //try on rul
        $rul = Rul::where('serial_number', $credentials['serial_number'])->first();
        if ($rul) {
            //generate token
            $token = Str::random(60);
            Rul::where('id', $rul->id)->update(['token' => $token]);

            return response()->json([
                'success' => true,
                'token' => $token,
                'user' => $rul,
                'role' => 'rul',
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'The provided serial number does not match our records.'
        ], 422);
    }

    public function logoutThroughApp(Request $request){
        $token = $this->extractToken($request);
        if ($token) {
            //try personnel
            $personnel = Personnel::where('token', $token)->first();
            if ($personnel) {
                $personnel->token = null;
                $personnel->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Logout successful'
                ], 200);
            }

            //try rul
            $rul = Rul::where('token', $token)->first();
            if ($rul) {
                $rul->token = null;
                $rul->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Logout successful'
                ], 200);
            }
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid token'
        ], 401);
    }

    public function logout(Request $request){
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout successful'
        ], 200);
    }
}
