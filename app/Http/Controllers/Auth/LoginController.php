<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class LoginController extends Controller
{
    // Add this property to override the default redirect
    protected $redirectTo = '/home'; // Change to your desired route
    
    // Or use a method for dynamic redirects
    protected function redirectTo()
    {
        // You can add logic here
        if (auth()->user()->isAdmin()) {
            return '/admin';
        }
        
        return '/home'; // Your desired redirect path
    }

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // If coming from expired session, show message
        if ($request->get('logout') === 'expired') {
            session()->flash('message', 'Your session expired. Please log in again.');
        }
        
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required|min:8',
        ]);

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password], $request->remember)) {
            return redirect()->intended($this->redirectPath());
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function logout(Request $request)
    {
        // Clear all sessions
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
      // Clear all local storage and session storage
      localStorage.clear();
      sessionStorage.clear();

        // Clear any cached data
        Cache::flush();
        
        return redirect('/login');
    }
} 