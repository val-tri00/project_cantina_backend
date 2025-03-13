<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    //intregistrare
    public function register(Request $request){
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response() -> json(['message' => 'User created successfuly'], 201);
    }


    // Login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);
    
        if(Auth::attempt($credentials)) {
            $user = Auth::user();
    
            // ✅ Asigură-te că User.php folosește HasApiTokens corect
            if (!method_exists($user, 'createToken')) {
                logger('⚠️ ERROR: createToken() does not exist on User model.');
                return response()->json(['error' => 'Sanctum is not configured correctly'], 500);
            }
    
            $token = $user->createToken('auth_token')->plainTextToken;
    
            return response()->json([
                'token' => $token,
                'user' => $user
            ], 200);
        }
    
        return response()->json(['error' => 'Unauthorized'], 401);
    }
    
    

    //Logout
    public function logout(Request $request)
    {
        $user = Auth::user();
    
        if (!$user) {
            logger('⚠️ ERROR: User not authenticated');
            return response()->json(['error' => 'User not authenticated'], 401);
        }
    
        // ✅ Verificăm dacă tokens() există
        if (!method_exists($user, 'tokens')) {
            logger('⚠️ ERROR: Method tokens() does not exist on User model.');
            return response()->json(['error' => 'Sanctum is not configured correctly'], 500);
        }
    
        $user->tokens()->delete();
        
        return response()->json(['message' => 'Logged out'], 200);
    }    

}
