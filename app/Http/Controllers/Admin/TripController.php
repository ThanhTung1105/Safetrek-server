<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Trip;
use Illuminate\Http\Request;

class TripController extends Controller
{
    /**
     * Display trip detail with full GPS route
     */
    public function show($id)
    {
        // Lấy trip với user và location history
        $trip = Trip::with(['user', 'locationHistory' => function($query) {
            $query->orderBy('timestamp', 'asc'); // Sắp xếp theo thời gian
        }])->findOrFail($id);
        
        // Lấy guardians của user (để hiển thị ai nhận alert)
        $guardians = $trip->user->guardians()->where('status', 'accepted')->get();
        
        // Tính thời gian thực tế của chuyến đi
        $duration = null;
        if ($trip->actual_end_time) {
            $duration = \Carbon\Carbon::parse($trip->start_time)
                ->diffInMinutes(\Carbon\Carbon::parse($trip->actual_end_time));
        }
        
        return view('admin.trip_detail', compact('trip', 'guardians', 'duration'));
    }
}
