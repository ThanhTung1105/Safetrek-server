<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Lấy các chỉ số thống kê
        $totalUsers = User::count();
        $pinSetupCount = User::where('is_pin_setup', true)->count();
        $pinNotSetCount = User::where('is_pin_setup', false)->count();
        
        // Giả sử bạn có bảng trips để đếm số chuyến đang diễn ra
        $activeTripsCount = DB::table('trips')->where('status', 'ongoing')->count();

        // 2. Lấy danh sách người dùng mới nhất (5-8 người để khớp giao diện)
        $users = User::latest()->paginate(8);

        // Trả về view Dashboard_admin.php
        return view('Dashboard_admin', compact(
            'totalUsers', 
            'activeTripsCount', 
            'pinSetupCount', 
            'pinNotSetCount', 
            'users'
        ));
    }
}
