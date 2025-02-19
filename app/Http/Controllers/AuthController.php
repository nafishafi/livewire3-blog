<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function loadRegisterForm(){
        return view("register-form");
    }
    
    public function registerUser(Request $request){
        // form validation
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'username' => 'required',
            'password' => 'required|min:6|max:8|confirmed'
        ]);

        try {
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->username = $request->username;
            $user->password = Hash::make($request->password);
            $user->save();
            return redirect('/registration/form')->with('success', 'User created successfully');
        } catch (\Exception $e) {
            return redirect('/registration/form')->with('error', $e -> getMessage());
        }
    }
}