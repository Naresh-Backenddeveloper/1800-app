<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{

    public function login(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'username' => 'required|email',
                'password' => 'required',
            ]);

            $user = User::where('email', $request->username)
                ->whereIn('role', ['ADMIN'])
                ->first();

            if (!$user || !$user->verifyPassword($request->password)) {
                return back()->withInput()
                    ->with('error', 'Invalid username or password.');
            }

            Auth::login($user);
            return redirect('/_admin/secure');
        }

        // Show login view
        return view('login');
    }
}
