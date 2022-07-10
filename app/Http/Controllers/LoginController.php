<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login(Request $request) {
        $credentials = $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, true)) {
            $request->session()->regenerate();

            return $request->user();
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function logout() {
        Auth::logout();
    }

    public function authMe(Request $request) {
        if (!$request->user()) {
            return [
                'isAuth' => false,
                'message' => 'Not authorized',
            ];
        }

        return [
            'isAuth' => true,
            'userData' => [
                'id' => $request->user()->id,
                'name' => $request->user()->name,
                'login' => $request->user()->login,
                'photo' => $request->user()->photo,
            ]
        ];
    }
}
