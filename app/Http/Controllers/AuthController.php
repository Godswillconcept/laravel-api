<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // register 
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
        ]);

        if ($request->hasFile('image')) {
            $src = $request->file('image');
            $path = 'profiles'; // 

            $image = $this->saveImage($src, $path);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'image' => $request->hasFile('image') ? $image : null
        ]);

        return response([
            'user' => $user,
            'token' => $user->createToken('secret')->plainTextToken,
        ]);
    }

    // login 
    public function login(Request $request)
    {
        
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response([
                'message' => 'Invalid credentials',
            ]);
        }

        $user = Auth::user();

        return response([
            'user' => $user,
            'token' => $user->createToken('secret')->plainTextToken,
        ]);
    }

    // logout
    public function logout()
    {
        Auth::user()->tokens()->delete();

        return response([
            'message' => 'Successfully logged out',
        ]);
    }

    // user
    public function user()
    {
        return response(
            [
                'user' => Auth::user()
            ]
        );
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string',
        ]);

        $image = $this->saveImage($request->image, 'profiles');

        $user->update([
            'name' => $request->name,
            'image' => $image,
        ]);

        return response(
            [
                'user' => $user,
                'message' => 'User updated'
            ]
        );
    }
}
