<?php

namespace App\Http\Controllers;

use App\Models\PasswordReset;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
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

    // forgot password function load page
    public function forgotPassword(){
        return view('forgot-password');
    }
    
    public function forgot(Request $request) {
        $request->validate([
            'email' => 'required'
        ]);
        
        $user = User::where('email', $request->email)->first(); // Ambil satu user
    
        if ($user) { // Cukup cek keberadaan user tanpa count()
            $token = Str::random(40);
            $domain = URL::to('/');
            $url = $domain . '/reset/password?token=' . $token;
    
            $data['url'] = $url;
            $data['email'] = $request->email;
            $data['title'] = 'Password Reset';
            $data['body'] = 'Please click the link below to reset your password';
    
            Mail::send('forgotPasswordMail', ['data' => $data], function ($message) use ($data) {
                $message->to($data['email'])->subject($data['title']);
            });
    
            $passwordReset = new PasswordReset;
            $passwordReset->email = $request->email;
            $passwordReset->token = $token;
            $passwordReset->user_id = $user->id;
            $passwordReset->save();
    
            return back()->with('success', 'Password reset link sent to your email');
        } else {
            return redirect('/forgot/password')->with('error', 'Email does not exist');
        }
    }

    public function loadResetPassword(Request $request){
        $resetData = PasswordReset::where('token', $request->token)->get();
        if(isset($request->token) && count($resetData) > 0){
            $user = User::where('id', $resetData[0]->user_id)->get();
            foreach($user as $user_data){
                
            }
            return view('reset-password', compact('user_data'));
        } else {
            return view('404');
        }
    }

    // perform password reset logic

    public function ResetPassword(Request $request){
        $request->validate([
            'password' => 'required|min:6|max:8|confirmed'
        ]);
        try {
            $user = User::find($request->user_id);
            $user->password = Hash::make($request->password);
            $user->save();

            // dlete reset token
            PasswordReset::where('email', $request->user_email)->delete();
            return redirect('/login/form')->with('success', 'Password reset successfully'); 
        } catch (\Exception $e) {
            return back()->with('error', $e -> getMessage());
        }
    }
    
}