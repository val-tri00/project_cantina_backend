<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{

    public function userProfile(Request $request) {
        $user = Auth::user();

        if(!$user){
            return response()->json(['error' => 'Utilizator neautentificat'], 401);
        }

        return response()->json([
            'user' => $user
        ]);
    }



    //intregistrare
    public function register(Request $request)
    {
        return DB::transaction(function () use ($request) {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:6|confirmed',
            ]);
    
            Log::info('🔍 REGISTER ATTEMPT', ['data' => $validatedData]);
    
            $user = User::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password']),
                'role' => 'user',
            ]);
    
            Log::info('✅ REGISTER SUCCESS', ['user' => $user]);

            $token = $user->createToken('authToken')->plainTextToken;
    
            return response()->json([
                'message' => 'Utilizator creat cu succes!',
                'user' => $user,
                'token' => $token,
            ], 201);
        });
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
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                ]
            ], 201);
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
