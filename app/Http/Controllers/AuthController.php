<?php

namespace App\Http\Controllers;

use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function showLoginForm()
    {
        return $this->showLogin();
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();

            if ($user->status === 'suspended') {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Your account has been suspended. Please contact UVAS Swat System Administrator.',
                ]);
            }

            $user->update(['last_login_at' => now()]);
            $request->session()->regenerate();

            AuditService::log('User Login', 'User', $user->id, ['email' => $user->email]);

            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        if (Auth::check()) {
            AuditService::log('User Logout', 'User', Auth::id());
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    public function showForgotPasswordForm()
    {
        return $this->showForgotPassword();
    }

    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'min:6'],
        ], [
            'email.exists' => 'This email address is not registered in our system.',
        ]);

        $user = \App\Models\User::where('email', $request->email)->first();
        if ($user) {
            $user->update([
                'password' => \Illuminate\Support\Facades\Hash::make($request->password),
            ]);

            return back()->with('status', 'Password has been reset successfully for ' . $request->email . '! You can now log in with your new password.');
        }

        return back()->withErrors(['email' => 'User account not found with email: ' . $request->email]);
    }

    public function sendResetLinkEmail(Request $request)
    {
        return $this->sendResetLink($request);
    }
}
