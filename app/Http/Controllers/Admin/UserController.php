<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function show($id)
    {
        // 1. Lấy thông tin user cùng với các mối quan hệ từ DB
        // Sử dụng with() để tối ưu hóa truy vấn (Eager Loading)
        $user = User::with(['guardians', 'trips' => function($query) {
            $query->orderBy('created_at', 'desc'); // Sắp xếp chuyến đi mới nhất lên đầu
        }])->findOrFail($id);

        // 2. Lấy danh sách người bảo vệ thực tế
        $guardians = $user->guardians;

        // 3. Lấy danh sách chuyến đi thực tế
        $trips = $user->trips;

        return view('user_information', compact('user', 'guardians', 'trips'));
    }
}