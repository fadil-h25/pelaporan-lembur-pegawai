<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function index()
    {
        return view('auth.login'); // Mengarah ke resources/views/auth/login.blade.php
    }

    public function login(Request $request)
    {
        $creds = $request->validate(['email' => 'required|email', 'password' => 'required']);
        if (Auth::attempt($creds)) {
            $request->session()->regenerate();

            return redirect()->intended('/dashboard');
        }

        return back()->with('error', 'Email atau Password salah!');
    }

    public function logout()
    {
        Auth::logout();

        return redirect('/login');
    }
}
