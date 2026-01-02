<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Thêm hàm này để hiển thị trang đăng nhập
    public function showLoginForm() {
        return view('login_admin'); // Trỏ đến file login_admin.php
    }

    public function login(Request $request) {
        $credentials = $request->only('phone_number', 'password');

        if (Auth::attempt($credentials)) {
            // Kiểm tra role admin từ migration
            if (Auth::user()->role === 'admin') {
                $request->session()->regenerate();
                return redirect()->intended('admin/dashboard');
            }

            Auth::logout();
            return back()->withErrors(['role' => 'Bạn không có quyền quản trị.']);
        }

        return back()->withErrors(['phone_number' => 'Thông tin không chính xác.']);
    }

    // Nên thêm hàm logout để admin có thể thoát hệ thống
    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}