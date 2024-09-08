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

    public function showLoginForm()
    {
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
            if ($user->role == 'admin') {
                return redirect()->intended($this->redirectAdmin);
            } elseif ($user->role == 'operator') {
                return redirect()->intended($this->redirectOperator);
            }

            // Jika role tidak dikenali, redirect ke halaman default
            return redirect($this->redirectTo);
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
}

