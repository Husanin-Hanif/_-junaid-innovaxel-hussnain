<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules;

class AuthController extends Controller
{
    /**
     * Handle the login request.
     */
    public function login(Request $request)
    {
        try{
            $request->validate([
                'email' => 'required|email',
                'password' => 'required|string',
            ]);

            // Check if the user exists
            $user = User::where('email', $request->email)->first();

            // Validate password
            if (!$user || $request->password !== $user->password) {
                return response()->json(['message' => 'Invalid credentials'], 401);
            }

            // Generate token for authenticated user
            $token = $user->createToken('API Token')->plainTextToken;

            return response()->json([
                'message' => 'Login successful',
                'token' => $token,
            ]);

        }
        catch(Exception $e){
            Log::error("your errror is ",$e->getmessage());
            return \response()->json(["message"=>'error is ',$e->getmessage()]);
        }


    }

    /**
     * Handle the signup request.
     */
    public function signup(Request $request)
{
    try {

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'string', 'in:user,admin'],
        ]);


        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
         'password' => $request->password,
            'role' => $request->role,
        ]);



        Auth::login($user);

        return response()->json(['message' => 'Sign up successful']);
    } catch (\Exception $e) {

        \Log::error('Error during signup: ' . $e->getMessage());
        return response()->json(['message' => 'An error occurred','error' => $e->getMessage(),], 500);
    }
}

}
