<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập Admin - SafeTrek</title>
    
    <!-- Favicons -->
    <link rel="icon" type="image/png" sizes="32x32" href="/images/favicon-32x32.png">
    <link rel="icon" type="image/x-icon" href="/images/favicon.ico">
    <link rel="apple-touch-icon" sizes="180x180" href="/images/apple-touch-icon.png">
    <link rel="manifest" href="/images/site.webmanifest">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .bg-login {
            background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), 
                              url("<?php echo asset('images/background.jpg'); ?>");
            background-size: cover;
            background-position: center center;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }
        
        .glass-effect {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }
        
        .input-focus {
            transition: all 0.3s ease;
        }
        
        .input-focus:focus {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(59, 130, 246, 0.3);
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .fade-in-up {
            animation: fadeInUp 0.6s ease;
        }
    </style>
</head>
<body class="bg-login min-h-screen flex items-center justify-center p-4 font-[sans-serif]">
    
    <div class="glass-effect p-8 md:p-12 rounded-3xl shadow-2xl w-full max-w-md border border-white/50 fade-in-up">
        
        <!-- Logo Section -->
        <div class="text-center mb-8">
            <img src="<?php echo asset('images/logo.png'); ?>" 
                 alt="SafeTrek Logo" 
                 class="w-48 h-auto mx-auto object-contain drop-shadow-lg mb-4">
            <h2 class="text-2xl font-black text-gray-800 mb-2">Quản trị hệ thống</h2>
            <p class="text-sm text-gray-500">Đăng nhập để tiếp tục</p>
            <div class="h-1 w-20 bg-gradient-to-r from-blue-500 to-blue-600 rounded-full mx-auto mt-4"></div>
        </div>

        <!-- Error Messages -->
        <?php if($errors->any()): ?>
            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-2xl animate-pulse">
                <div class="flex items-center gap-2">
                    <i class="fas fa-exclamation-circle text-red-500"></i>
                    <p class="text-sm text-red-600 font-bold"><?php echo $errors->first(); ?></p>
                </div>
            </div>
        <?php endif; ?>

        <!-- Login Form -->
        <form action="<?php echo route('admin.login.submit'); ?>" method="POST" class="space-y-6">
            <?php echo csrf_field(); ?>
            
            <!-- Phone Number Input -->
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2 flex items-center gap-2">
                    <i class="fas fa-phone text-blue-500"></i>
                    Số điện thoại
                </label>
                <div class="relative">
                    <input type="text" 
                           name="phone_number" 
                           class="input-focus w-full px-5 py-4 pl-12 rounded-2xl border-2 border-gray-200 focus:border-blue-500 focus:ring-0 outline-none bg-white/50 text-gray-800 font-semibold" 
                           placeholder="Nhập số điện thoại"
                           autocomplete="tel"
                           required>
                    <i class="fas fa-mobile-alt absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                </div>
            </div>

            <!-- Password Input -->
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2 flex items-center gap-2">
                    <i class="fas fa-lock text-blue-500"></i>
                    Mật khẩu
                </label>
                <div class="relative">
                    <input type="password" 
                           name="password" 
                           id="password"
                           class="input-focus w-full px-5 py-4 pl-12 pr-12 rounded-2xl border-2 border-gray-200 focus:border-blue-500 focus:ring-0 outline-none bg-white/50 text-gray-800 font-semibold" 
                           placeholder="••••••••"
                           autocomplete="current-password"
                           required>
                    <i class="fas fa-key absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <button type="button" 
                            onclick="togglePassword()"
                            class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition">
                        <i class="fas fa-eye" id="toggleIcon"></i>
                    </button>
                </div>
            </div>

            <!-- Submit Button -->
            <button type="submit" 
                    class="w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-black py-5 rounded-2xl shadow-xl shadow-blue-500/30 transition duration-300 transform hover:-translate-y-1 hover:shadow-2xl active:scale-[0.98] uppercase tracking-widest text-sm flex items-center justify-center gap-2">
                <i class="fas fa-sign-in-alt"></i>
                Đăng nhập hệ thống
            </button>
        </form>

        <!-- Footer -->
        <div class="mt-8 text-center">
            <p class="text-xs text-gray-400">
                <i class="fas fa-shield-alt mr-1"></i>
                SafeTrek Admin Panel v1.0
            </p>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }
    </script>

</body>
</html>