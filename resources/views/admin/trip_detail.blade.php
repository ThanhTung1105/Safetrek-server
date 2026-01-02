<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chi tiết chuyến đi #{{ $trip->id }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        #map { height: 500px; }
    </style>
</head>
<body class="bg-[#f8f9fa] p-8">
    <div class="max-w-7xl mx-auto">
        <!-- Back Button -->
        <a href="{{ route('admin.users.show', $trip->user_id) }}" class="flex items-center text-gray-500 mb-6 hover:text-blue-600 transition">
            <i class="fas fa-arrow-left mr-2"></i> Quay lại thông tin user
        </a>

        <!-- Trip Header -->
        <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 mb-6">
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="text-2xl font-bold mb-2">
                        @if($trip->trip_type == 'panic')
                            {{-- Panic from Home --}}
                            <span class="text-red-600">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                🚨 Cảnh báo khẩn cấp (Từ trang chủ)
                            </span>
                        @elseif($trip->status == 'panic' && $trip->trip_type != 'panic')
                            {{-- Panic during Trip --}}
                            <span class="text-red-600">
                                <i class="fas fa-route text-red-500 mr-2"></i>
                                🚨 Hoảng loạn trong chuyến đi
                            </span>
                        @elseif($trip->status == 'alerted')
                            {{-- Timer Expired --}}
                            <span class="text-yellow-600">
                                <i class="fas fa-clock text-yellow-500 mr-2"></i>
                                ⏰ Hết thời gian chuyến đi
                            </span>
                        @elseif($trip->status == 'duress_ended')
                            {{-- Duress PIN --}}
                            <span class="text-orange-600">
                                <i class="fas fa-key text-orange-500 mr-2"></i>
                                ⚠️ Kết thúc bằng Duress PIN
                            </span>
                        @else
                            {{-- Normal Trip --}}
                            <i class="fas fa-route text-blue-500 mr-2"></i>
                            <span class="text-gray-800">{{ $trip->destination_name ?? 'Chuyến đi tự do' }}</span>
                        @endif
                    </h1>
                    
                    @if(in_array($trip->trip_type, ['timer', 'safety', 'panic']) && $trip->trip_type != 'panic' && $trip->destination_name)
                        <p class="text-gray-500 text-sm mb-2">
                            <i class="fas fa-map-marker-alt mr-1"></i>
                            Điểm đến: {{ $trip->destination_name }}
                        </p>
                    @endif
                    
                    <p class="text-gray-500 text-sm mb-4">Trip ID: #{{ $trip->id }}</p>
                    
                    <div class="flex gap-4 text-sm">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-user text-gray-400"></i>
                            <span class="font-semibold">{{ $trip->user->full_name }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="fas fa-calendar text-gray-400"></i>
                            <span>{{ \Carbon\Carbon::parse($trip->start_time)->format('d/m/Y H:i') }}</span>
                        </div>
                        @if($duration)
                        <div class="flex items-center gap-2">
                            <i class="fas fa-clock text-gray-400"></i>
                            <span>{{ $duration }} phút</span>
                        </div>
                        @endif
                    </div>
                </div>
                
                <!-- Status Badge -->
                <div class="text-right">
                    @if($trip->status == 'panic')
                        <span class="px-4 py-2 bg-red-500 text-white rounded-full text-xs font-black animate-pulse shadow-lg">🚨 PANIC!</span>
                    @elseif($trip->status == 'duress_ended')
                        <span class="px-4 py-2 bg-orange-500 text-white rounded-full text-xs font-black shadow-lg">⚠️ DURESS PIN</span>
                    @elseif($trip->status == 'alerted')
                        <span class="px-4 py-2 bg-yellow-500 text-white rounded-full text-xs font-black shadow-lg">⏰ TIMER EXPIRED</span>
                    @elseif($trip->status == 'active')
                        <span class="px-4 py-2 bg-blue-500 text-white rounded-full text-xs font-black shadow-lg">🔵 ĐANG DIỄN RA</span>
                    @else
                        <span class="px-4 py-2 bg-gray-200 text-gray-700 rounded-full text-xs font-black">✅ {{ strtoupper($trip->status) }}</span>
                    @endif
                    
                    <p class="text-xs text-gray-400 mt-2 uppercase">{{ $trip->trip_type }} Mode</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-6">
            <!-- Map Section -->
            <div class="col-span-2">
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100">
                    <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-map-marked-alt text-blue-500"></i> 
                        Lộ trình GPS đầy đủ
                        <span class="text-xs font-normal text-gray-400">({{ count($trip->locationHistory) }} điểm)</span>
                    </h3>
                    
                    @if(count($trip->locationHistory) > 0)
                        <div id="map" class="rounded-2xl shadow-inner"></div>
                    @else
                        <div class="h-[500px] bg-gray-100 rounded-2xl flex items-center justify-center text-gray-400">
                            <div class="text-center">
                                <i class="fas fa-map-marked-alt text-6xl mb-4"></i>
                                <p class="font-bold">Không có dữ liệu GPS</p>
                                <p class="text-sm">Chuyến đi chưa cập nhật vị trí</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Info Sidebar -->
            <div class="col-span-1 space-y-6">
                <!-- Timeline -->
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100">
                    <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-clock text-green-500"></i> Timeline
                    </h3>
                    
                    <div class="space-y-4">
                        <div class="flex items-start gap-3">
                            <div class="w-2 h-2 bg-green-500 rounded-full mt-2"></div>
                            <div>
                                <p class="text-xs font-black text-gray-400 uppercase">Bắt đầu</p>
                                <p class="text-sm font-bold">{{ \Carbon\Carbon::parse($trip->start_time)->format('H:i d/m/Y') }}</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start gap-3">
                            <div class="w-2 h-2 bg-blue-500 rounded-full mt-2"></div>
                            <div>
                                <p class="text-xs font font-black text-gray-400 uppercase">Dự kiến kết thúc</p>
                                <p class="text-sm font-bold">{{ \Carbon\Carbon::parse($trip->expected_end_time)->format('H:i d/m/Y') }}</p>
                            </div>
                        </div>
                        
                        @if($trip->actual_end_time)
                        <div class="flex items-start gap-3">
                            <div class="w-2 h-2 bg-red-500 rounded-full mt-2"></div>
                            <div>
                                <p class="text-xs font-black text-gray-400 uppercase">Kết thúc thực tế</p>
                                <p class="text-sm font-bold">{{ \Carbon\Carbon::parse($trip->actual_end_time)->format('H:i d/m/Y') }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Guardians Notified -->
                @if(in_array($trip->status, ['panic', 'duress_ended', 'alerted']))
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100">
                    <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-bell text-orange-500"></i> Đã gửi cảnh báo
                    </h3>
                    
                    <div class="space-y-3">
                        @foreach($guardians as $guardian)
                        <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                            <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center text-orange-600 font-bold text-sm">
                                {{ substr($guardian->contact_name, 0, 1) }}
                            </div>
                            <div>
                                <p class="font-semibold text-sm">{{ $guardian->contact_name }}</p>
                                <p class="text-xs text-gray-400">{{ $guardian->contact_phone_number }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Location Stats -->
                @if(count($trip->locationHistory) > 0)
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100">
                    <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-chart-line text-purple-500"></i> Thống kê
                    </h3>
                    
                    <div class="space-y-3">
                        <div class="bg-gray-50 p-3 rounded-xl flex justify-between">
                            <span class="text-xs font-black text-gray-400 uppercase">Số điểm GPS</span>
                            <span class="font-bold">{{ count($trip->locationHistory) }}</span>
                        </div>
                        <div class="bg-gray-50 p-3 rounded-xl flex justify-between">
                            <span class="text-xs font-black text-gray-400 uppercase">Pin cuối</span>
                            <span class="font-bold text-green-500">
                                {{ $trip->locationHistory->last()->battery_level ?? 'N/A' }}%
                            </span>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
    <script>
        @if(count($trip->locationHistory) > 0)
            // Get all location points
            const locations = [
                @foreach($trip->locationHistory as $loc)
                    [{{ $loc->latitude }}, {{ $loc->longitude }}],
                @endforeach
            ];
            
            // Initialize map centered on first location
            const map = L.map('map').setView(locations[0], 14);
            
            // Add OpenStreetMap tiles
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors',
                maxZoom: 19
            }).addTo(map);
            
            // Custom icons
            const startIcon = L.icon({
                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png',
                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-shadow.png',
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowSize: [41, 41]
            });
            
            const endIcon = L.icon({
                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-shadow.png',
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowSize: [41, 41]
            });
            
            // Add start marker
            L.marker(locations[0], {icon: startIcon})
                .addTo(map)
                .bindPopup('<strong>🟢 Điểm bắt đầu</strong><br>{{ \Carbon\Carbon::parse($trip->locationHistory->first()->timestamp)->format("H:i d/m/Y") }}');
            
            // Add end marker
            L.marker(locations[locations.length - 1], {icon: endIcon})
                .addTo(map)
                .bindPopup('<strong>🔴 Điểm cuối</strong><br>{{ \Carbon\Carbon::parse($trip->locationHistory->last()->timestamp)->format("H:i d/m/Y") }}');
            
            // Draw route polyline
            const polyline = L.polyline(locations, {
                color: '#3b82f6',
                weight: 4,
                opacity: 0.7,
                smoothFactor: 1
            }).addTo(map);
            
            // Fit map to show entire route
            map.fitBounds(polyline.getBounds(), {padding: [50, 50]});
            
            // Add intermediate markers every N points
            const markerInterval = Math.max(1, Math.floor(locations.length / 10));
            @foreach($trip->locationHistory as $index => $loc)
                @if($index > 0 && $index < count($trip->locationHistory) - 1 && $index % 5 == 0)
                    L.circleMarker([{{ $loc->latitude }}, {{ $loc->longitude }}], {
                        radius: 4,
                        fillColor: '#60a5fa',
                        color: '#fff',
                        weight: 2,
                        opacity: 1,
                        fillOpacity: 0.8
                    }).addTo(map).bindPopup(`
                        <div style="text-align: center;">
                            <strong>📍 Checkpoint</strong><br>
                            <span style="font-size: 11px;">
                                🔋 {{ $loc->battery_level ?? 'N/A' }}%<br>
                                🕒 {{ \Carbon\Carbon::parse($loc->timestamp)->format('H:i') }}
                            </span>
                        </div>
                    `);
                @endif
            @endforeach
        @endif
    </script>
</body>
</html>
