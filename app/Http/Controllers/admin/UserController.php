<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::query()
            ->where('role', 'USER')
            ->select('id', 'name', 'email', 'mobile', 'address', 'active_flag', 'created_at as joined_date')
            ->latest()
            ->paginate(15);

        $stats = [
            'total_users'   => User::where('role', 'USER')->count(),
            'active_users'  => User::where('role', 'USER')->where('active_flag', '1')->count(),
            'blocked_users' => User::where('role', 'USER')->where('active_flag', '0')->count(),
            'total_ads'     => Product::count(),
        ];

        return view('admin.users.users', compact('users', 'stats'));
    }
}
