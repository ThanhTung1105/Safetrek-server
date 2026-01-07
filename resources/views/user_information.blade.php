<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thông tin chi tiết - <?php echo $user->full_name; ?></title>
    
    <!-- Favicons -->
    <link rel="icon" type="image/png" sizes="32x32" href="/images/favicon-32x32.png">
    <link rel="icon" type="image/x-icon" href="/images/favicon.ico">
    <link rel="apple-touch-icon" sizes="180x180" href="/images/apple-touch-icon.png">
    <link rel="manifest" href="/images/site.webmanifest">
    
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

        <div class="space-y-6">
            <div class="space-y-6">
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
                        <span class="text-xs font-normal text-gray-400 ml-auto">Click vào chuyến đi để xem GPS chi tiết</span>
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
                                <tr onclick="window.location='{{ route('admin.trips.show', $trip->id) }}'" 
                                    class="group hover:bg-blue-50 transition-colors cursor-pointer">
                                    <td class="py-4">
                                        @if($trip->trip_type == 'panic')
                                            {{-- Panic from Home --}}
                                            <p class="text-sm font-bold text-red-600 group-hover:text-red-700 transition">
                                                🚨 Hoảng loạn (Từ trang chủ)
                                            </p>
                                            <p class="text-[10px] text-red-400">Không có chuyến đi</p>
                                        @elseif($trip->status == 'panic' && $trip->trip_type != 'panic')
                                            {{-- Panic during Trip --}}
                                            <p class="text-sm font-bold text-red-600 group-hover:text-red-700 transition">
                                                🚨 Hoảng loạn trong chuyến đi
                                            </p>
                                            <p class="text-[10px] text-gray-400">{{ $trip->destination_name }}</p>
                                        @elseif($trip->status == 'alerted')
                                            {{-- Timer Expired --}}
                                            <p class="text-sm font-bold text-yellow-600 group-hover:text-yellow-700 transition">
                                                ⏰ Hết thời gian chuyến đi
                                            </p>
                                            <p class="text-[10px] text-gray-400">{{ $trip->destination_name }}</p>
                                        @elseif($trip->status == 'duress_ended')
                                            {{-- Duress PIN --}}
                                            <p class="text-sm font-bold text-orange-600 group-hover:text-orange-700 transition">
                                                ⚠️ Kết thúc bằng Duress PIN
                                            </p>
                                            <p class="text-[10px] text-gray-400">{{ $trip->destination_name }}</p>
                                        @else
                                            {{-- Normal Trip --}}
                                            <p class="text-sm font-bold text-gray-700 group-hover:text-blue-600 transition">
                                                {{ $trip->destination_name ?? 'Chuyến đi tự do' }}
                                            </p>
                                            <p class="text-[10px] text-gray-400 capitalize">{{ $trip->trip_type }} Mode</p>
                                        @endif
                                    </td>
                                    <td class="py-4 text-center">
                                        @if($trip->status == 'panic')
                                            <span class="px-3 py-1 bg-red-500 text-white rounded-full text-[10px] font-black shadow-sm animate-pulse">Hoảng loạn!</span>
                                        @elseif($trip->status == 'alerted')
                                            <span class="px-3 py-1 bg-yellow-500 text-white rounded-full text-[10px] font-black shadow-sm">HẾT GIỜ</span>
                                        @elseif($trip->status == 'duress_ended')
                                            <span class="px-3 py-1 bg-orange-500 text-white rounded-full text-[10px] font-black shadow-sm">Cưỡng ép</span>
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
        </div>
    </div>
</body>
</html>