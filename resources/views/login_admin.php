<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng nhập Safetrek Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Thêm CSS để xử lý ảnh nền mịn hơn */
        .bg-login {
            background-image: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4)), 
                              url("<?php echo asset('images/background.jpg'); ?>");
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }
    </style>
</head>
<body class="bg-login flex items-center justify-center h-screen font-[sans-serif]">
    
    <div class="bg-white/95 backdrop-blur-sm p-10 rounded-[32px] shadow-2xl w-full max-w-md border border-white/20">
        
        <div class="flex flex-col items-center mb-10">
            <img src="<?php echo asset('images/logo.png'); ?>" 
                 alt="SafeTrek Logo" 
                 class="w-64 h-auto object-contain drop-shadow-md"> <div class="h-1.5 w-16 bg-blue-600 rounded-full mt-6 shadow-sm"></div>
        </div>

        <form action="<?php echo route('admin.login.submit'); ?>" method="POST">
            <?php echo csrf_field(); ?>
            
            <div class="mb-5">
                <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">Số điện thoại</label>
                <input type="text" name="phone_number" 
                       class="w-full px-5 py-4 rounded-2xl border border-gray-200 focus:ring-2 focus:ring-blue-500 outline-none transition bg-gray-50/50 text-gray-800" 
                       placeholder="Nhập số điện thoại" required>
            </div>

            <div class="mb-8">
                <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">Mật khẩu</label>
                <input type="password" name="password" 
                       class="w-full px-5 py-4 rounded-2xl border border-gray-200 focus:ring-2 focus:ring-blue-500 outline-none transition bg-gray-50/50 text-gray-800" 
                       placeholder="••••••••" required>
            </div>

            <button type="submit" 
                    class="w-full bg-[#2563eb] hover:bg-[#1d4ed8] text-white font-black py-5 rounded-2xl shadow-xl shadow-blue-500/30 transition duration-300 transform hover:-translate-y-1 active:scale-[0.98] uppercase tracking-widest text-sm">
                Đăng nhập hệ thống
            </button>
            
            <?php if($errors->any()): ?>
                <div class="mt-6 p-4 bg-red-50 rounded-2xl border border-red-100 animate-pulse">
                    <p class="text-center text-red-600 text-xs font-bold"><?php echo $errors->first(); ?></p>
                </div>
            <?php endif; ?>
        </form>
    </div>

</body>
</html>