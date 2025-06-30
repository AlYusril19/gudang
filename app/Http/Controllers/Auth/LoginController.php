<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    // Default redirect URL jika role tidak dikenali
    protected $redirectTo = '/';
    protected $redirectAdmin = '/admin/dashboard';
    protected $redirectOperator = '/operator/dashboard';
    protected $notPermitted = '/layouts/app_sneat_error';

    public function showLoginForm()
    {
        $userRole = auth()->user()->role ?? null;
        // Redirect ke dashboard atau halaman lain
        if ($userRole) {
            if ($userRole === 'admin' || $userRole === 'superadmin') {
                return redirect()->intended($this->redirectAdmin);
            }else if ($userRole == 'operator') {
                return redirect()->intended($this->redirectOperator);
            } 
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // Validasi input login
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Mencoba login dengan kredensial yang diberikan
        if (Auth::attempt($request->only('email', 'password'))) {
            $user = Auth::user();

            // Redirect berdasarkan role user
            if ($user->role == 'admin' || $user->role == 'superadmin') {
                return redirect()->intended($this->redirectAdmin);
            } elseif ($user->role == 'operator') {
                return redirect()->intended($this->redirectOperator);
            }

            // Jika role tidak dikenali, redirect ke halaman default
            // return redirect($this->redirectTo);
            return back()->withErrors([
                'email' => 'Permission Denied. This page is not accessible with your current user role. Please log in to the correct application.',
            ]);
        }

        // Jika login gagal, kembalikan ke halaman login dengan pesan error
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/');
    }

    // Login API untuk aplikasi teknisi
    public function apiLogin(Request $request)
    {
        // Validasi input login
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Mencoba login dengan kredensial yang diberikan
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Invalid credentials.'
            ], 401);
        }

        $user = Auth::user();

        // Buat token API untuk user
        $token = $user->createToken('API Token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
            'user' => $user,
        ]);
    }
}