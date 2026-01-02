<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Safetrek Admin - Trang chủ</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; font-family: 'Inter', sans-serif; }
    </style>
</head>
<body>
    <div class="flex">
        <div class="w-64 h-screen bg-white border-r border-gray-100 p-6 fixed flex flex-col">
            <div class="flex flex-col items-center mb-10">
            <img src="<?php echo asset('images/logo.png'); ?>" 
                 alt="SafeTrek Logo" 
                 class="w-64 h-auto object-contain drop-shadow-md"> <div class="h-1.5 w-16 bg-blue-600 rounded-full mt-6 shadow-sm"></div>
        </div>
            
            <nav class="flex-1 px-4 space-y-3">
                <a href="<?php echo route('admin.dashboard'); ?>" 
                class="group relative flex items-center gap-4 p-4 rounded-2xl overflow-hidden
                        bg-gradient-to-r from-blue-600 to-blue-500 
                        text-white shadow-[0_10px_20px_-5px_rgba(37,99,235,0.4)]
                        transition-all duration-500 ease-out
                        hover:shadow-[0_15px_30px_-5px_rgba(37,99,235,0.5)] 
                        hover:-translate-y-1">
                    
                    <div class="absolute inset-0 w-1/2 h-full bg-white/20 skew-x-[-25deg] -translate-x-full group-hover:animate-[shimmer_1.5s_infinite]"></div>

                    <div class="flex items-center justify-center w-10 h-10 bg-white/20 rounded-xl backdrop-blur-md shadow-inner">
                        <i class="fas fa-th-large text-lg group-hover:scale-110 transition-transform duration-300"></i>
                    </div>
                    
                    <div class="flex flex-col">
                        <span class="text-sm font-black tracking-widest uppercase">Trang chủ</span>
                        <span class="text-[10px] text-blue-100 font-medium opacity-80">Tổng quan hệ thống</span>
                    </div>

                    <i class="fas fa-chevron-right ml-auto text-xs opacity-0 -translate-x-2 group-hover:opacity-100 group-hover:translate-x-0 transition-all duration-300"></i>
                </a>
            </nav>

            <style>
            @keyframes shimmer {
                100% { transform: translateX(300%) skewX(-25deg); }
            }
            </style>

            <div class="pt-6 border-t-2 border-gray-200 mt-auto"> 
                <form action="<?php echo route('admin.logout'); ?>" method="POST" id="logout-form">
                    <?php echo csrf_field(); ?>
                    <button type="submit" 
                            class="group flex items-center gap-3 p-4 w-full rounded-2xl 
                                text-red-500 font-bold text-sm tracking-wide
                                hover:bg-red-500 hover:text-white 
                                transition-all duration-300 ease-in-out
                                hover:shadow-lg hover:shadow-red-200 active:scale-95">
                        
                        <div class="w-8 h-8 rounded-lg bg-red-50 flex items-center justify-center 
                                    group-hover:bg-white/20 transition-colors">
                            <i class="fas fa-sign-out-alt"></i>
                        </div>
                        
                        <span>ĐĂNG XUẤT</span>
                    </button>
                </form>
            </div>
        </div>

        <div class="ml-64 flex-1 p-8">
            <header class="mb-8">
                <h1 class="text-2xl font-bold text-gray-800">Quản lý người dùng</h1>
                <p class="text-gray-500 text-sm">Danh sách tất cả người dùng trong hệ thống</p>
            </header>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">
                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                    <p class="text-gray-400 text-[11px] font-bold uppercase mb-2">Tổng người dùng</p>
                    <p class="text-3xl font-bold text-gray-800"><?php echo $totalUsers; ?></p>
                </div>
                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                    <p class="text-blue-500 text-[11px] font-bold uppercase mb-2">Đang có chuyến đi</p>
                    <p class="text-3xl font-bold text-blue-600"><?php echo $activeTripsCount ?? 0; ?></p>
                </div>
                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                    <p class="text-green-500 text-[11px] font-bold uppercase mb-2">Đã cài đặt PIN</p>
                    <p class="text-3xl font-bold text-green-600"><?php echo $pinSetupCount; ?></p>
                </div>
                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                    <p class="text-orange-500 text-[11px] font-bold uppercase mb-2">Chưa cài đặt PIN</p>
                    <p class="text-3xl font-bold text-orange-600"><?php echo $pinNotSetCount; ?></p>
                </div>
            </div>

            <div class="bg-white rounded-[32px] border border-gray-100 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-50">
                    <div class="relative max-w-sm">
                        <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-300"></i>
                        <input type="text" placeholder="Tìm kiếm người dùng..." class="w-full pl-12 pr-4 py-2.5 bg-gray-50 rounded-xl border-none focus:ring-2 focus:ring-blue-500 outline-none text-sm transition">
                    </div>
                </div>

                <table class="w-full text-left">
                    <thead class="bg-gray-50/50 text-gray-400 text-[10px] font-bold uppercase tracking-wider">
                        <tr>
                            <th class="px-8 py-4">ID</th>
                            <th class="px-8 py-4">Họ và tên</th>
                            <th class="px-8 py-4">Số điện thoại</th>
                            <th class="px-8 py-4">Email</th>
                            <th class="px-8 py-4">Mã Pin</th>
                            <th class="px-8 py-4"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <?php foreach($users as $user): ?>
                        <tr class="hover:bg-gray-50/50 transition group">
                            <td class="px-8 py-5 text-gray-400 text-sm">#<?php echo $user->id; ?></td>
                            <td class="px-8 py-5">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-xs">
                                        <?php echo strtoupper(substr($user->full_name, 0, 1)); ?>
                                    </div>
                                    <span class="font-bold text-gray-700 text-sm"><?php echo $user->full_name; ?></span>
                                </div>
                            </td>
                            <td class="px-8 py-5 text-gray-600 text-sm"><?php echo $user->phone_number; ?></td>
                            <td class="px-8 py-5 text-gray-500 text-sm"><?php echo $user->email ?? '---'; ?></td>
                            <td class="px-8 py-5">
                                <span class="px-3 py-1 rounded-full text-[10px] font-bold <?php echo $user->is_pin_setup ? 'bg-green-50 text-green-600' : 'bg-orange-50 text-orange-600'; ?>">
                                    <?php echo $user->is_pin_setup ? 'Đã cài đặt' : 'Chưa cài đặt'; ?>
                                </span>
                            </td>
                            <td class="px-8 py-5 text-right">
                                <a href="<?php echo route('admin.users.show', $user->id); ?>" class="text-gray-300 group-hover:text-blue-600 transition">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>