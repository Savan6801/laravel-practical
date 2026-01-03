<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

use App\Events\UserStatusChanged;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $this->validate($request, [
            'email'   => 'required|email',
            'password' => 'required|min:6'
        ]);

        if (Auth::guard('admin')->attempt(['email' => $request->email, 'password' => $request->password], $request->remember)) {
            $user = Auth::guard('admin')->user();
            $user->update(['is_online' => true]);
            broadcast(new UserStatusChanged($user, 'online'));

            return redirect()->intended(route('admin.dashboard'));
        }

        return back()->withInput($request->only('email', 'remember'));
    }

    public function logout()
    {
        $user = Auth::guard('admin')->user();
        if ($user) {
            $user->update(['is_online' => false]);
            broadcast(new UserStatusChanged($user, 'offline'));
        }

        Auth::guard('admin')->logout();
        return redirect()->route('admin.login');
    }
}
