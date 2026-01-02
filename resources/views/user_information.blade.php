<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thông tin chi tiết - <?php echo $user->full_name; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-[#f8f9fa] p-8">
    <div class="max-w-6xl mx-auto">
        <a href="<?php echo route('admin.dashboard'); ?>" class="flex items-center text-gray-500 mb-6 hover:text-blue-600 transition">
            <i class="fas fa-arrow-left mr-2"></i> Quay lại danh sách
        </a>

        <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 flex items-center justify-between mb-6">
            <div class="flex items-center gap-6">
                <div class="w-20 h-20 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-3xl font-bold">
                    <?php echo substr($user->full_name, 0, 1); ?>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-800"><?php echo $user->full_name; ?> 
                        <span class="text-xs bg-blue-50 text-blue-500 px-2 py-1 rounded ml-2">User</span>
                        <span class="text-xs <?php echo $user->is_pin_setup ? 'bg-green-50 text-green-500' : 'bg-orange-50 text-orange-500'; ?> px-2 py-1 rounded ml-1">
                            <?php echo $user->is_pin_setup ? 'PIN đã cài đặt' : 'Chưa cài đặt PIN'; ?>
                        </span>
                    </h2>
                    <p class="text-gray-400 mt-1">ID: #<?php echo $user->id; ?></p>
                    <div class="flex gap-10 mt-3 text-sm text-gray-600">
                        <span><i class="fas fa-phone mr-2"></i> <?php echo $user->phone_number; ?></span>
                        <span><i class="fas fa-envelope mr-2"></i> <?php echo $user->email ?? 'Chưa cập nhật'; ?></span>
                        <span><i class="fas fa-calendar mr-2 text-gray-400"></i> Tham gia: <?php echo $user->created_at->format('d/m/Y'); ?></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-6">
            <div class="col-span-2 space-y-6">
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100">
                    <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-user-shield text-green-500"></i> Người bảo vệ (<?php echo count($guardians); ?>/5)
                    </h3>
                    <div class="space-y-4">
                        <?php foreach($guardians as $g): ?>
                        <div class="flex justify-between items-center p-4 bg-gray-50 rounded-2xl">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center text-green-600 font-bold"><?php echo substr($g['contact_name'] ?? $g['name'], 0, 1); ?></div>
                                <div>
                                    <p class="font-semibold text-gray-700"><?php echo $g['contact_name'] ?? $g['name']; ?></p>
                                    <p class="text-xs text-gray-400"><?php echo $g['contact_phone_number'] ?? $g['phone']; ?></p>
                                </div>
                            </div>
                            <span class="text-[10px] font-black uppercase px-3 py-1 rounded-lg <?php echo in_array($g['status'], ['accepted', 'Đã chấp nhận']) ? 'bg-green-100 text-green-600' : 'bg-orange-100 text-orange-600'; ?>">
                                <?php echo $g['status']; ?>
                            </span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100">
                    <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-route text-blue-500"></i> Lịch sử chuyến đi gần đây
                    </h3>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="text-gray-400 text-[10px] uppercase tracking-widest border-b border-gray-50">
                                    <th class="pb-4 font-black">Điểm đến</th>
                                    <th class="pb-4 font-black text-center">Trạng thái</th>
                                    <th class="pb-4 font-black">Bắt đầu</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($trips as $trip)
                                <tr class="group hover:bg-gray-50/50 transition-colors">
                                    <td class="py-4">
                                        <p class="text-sm font-bold text-gray-700">
                                            {{ $trip->destination_name ?? 'Chuyến đi tự do' }}
                                        </p>
                                        <p class="text-[10px] text-gray-400 capitalize">{{ $trip->trip_type }} Mode</p>
                                    </td>
                                    <td class="py-4 text-center">
                                        @if($trip->status == 'panic')
                                            <span class="px-3 py-1 bg-red-500 text-white rounded-full text-[10px] font-black shadow-sm animate-pulse">PANIC!</span>
                                        @elseif($trip->status == 'active')
                                            <span class="px-3 py-1 bg-blue-100 text-blue-600 rounded-full text-[10px] font-black">ĐANG ĐI</span>
                                        @else
                                            <span class="px-3 py-1 bg-gray-100 text-gray-500 rounded-full text-[10px] font-black uppercase">{{ $trip->status }}</span>
                                        @endif
                                    </td>
                                    <td class="py-4">
                                        <p class="text-xs font-bold text-gray-600">
                                            {{ \Carbon\Carbon::parse($trip->start_time)->format('H:i') }}
                                        </p>
                                        <p class="text-[10px] text-gray-400">
                                            {{ \Carbon\Carbon::parse($trip->start_time)->format('d/m/Y') }}
                                        </p>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-span-1 space-y-6">
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100">
                    <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-map-marker-alt text-red-500"></i> Vị trí cuối cùng
                    </h3>
                    <div class="w-full h-64 bg-blue-50 rounded-2xl flex items-center justify-center text-blue-400 mb-4 relative overflow-hidden">
                        <div class="text-center z-10">
                            <i class="fas fa-map-marked-alt text-4xl mb-2"></i>
                            <p class="text-xs font-bold uppercase tracking-widest">Bản đồ vị trí</p>
                        </div>
                        <div class="absolute inset-0 opacity-10 bg-[url('https://www.google.com/maps/vt/pb=!1m4!1m3!1i12!2i2345!3i1234!2m3!1e0!2sm!3i420120488!3m8!2sen!3sus!5e1105!12m4!1e68!2m2!1sset!2sRoadmap!4e0')]"></div>
                    </div>
                    
                    <div class="space-y-3">
                        <div class="bg-gray-50 p-3 rounded-xl flex justify-between items-center">
                            <span class="text-[10px] font-black text-gray-400 uppercase">Mức pin</span>
                            <span class="text-sm font-black text-green-500"><i class="fas fa-battery-three-quarters mr-1"></i> 85%</span>
                        </div>
                        <div class="bg-gray-50 p-3 rounded-xl">
                            <p class="text-[10px] font-black text-gray-400 uppercase mb-1">Thời gian ghi nhận</p>
                            <p class="text-sm font-bold text-gray-700">
                                {{ now()->format('H:i d/m/Y') }}
                            </p>
                        </div>
                    </div>
                    
                    <button class="w-full bg-blue-600 text-white py-4 rounded-2xl mt-6 font-black text-xs uppercase tracking-widest hover:bg-blue-700 transition shadow-lg shadow-blue-200">
                        <i class="fas fa-history mr-2"></i> Xem lịch sử GPS đầy đủ
                    </button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>