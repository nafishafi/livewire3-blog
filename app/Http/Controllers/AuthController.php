<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

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

    public function loadLoginPage(){
        return view("login-page");
    }

    public function LoginUser(Request $request){
        $request->validate([
            'username' => 'required',
            'password' => 'required|min:6|max:8'
        ]);
        // Validation if user valid
        try {
            $userCredentials = $request->only('username', 'password');
            if (Auth::attempt($userCredentials)) {
                return redirect('/home');
            } else {
                return redirect('/login/form')->with('error', 'Wrong User Credentials');
            }
        } catch (\Exception $e) {
            return redirect('/login/form')->with('error', $e -> getMessage());
        }
    }

    // create function to load home page
    public function loadHomePage(){
        return view('user.home-page');
    }

    // logout function
    public function LogoutUser(Request $request){
        Session::flush();
        Auth::logout();
        return redirect('/login/form');
    }
}