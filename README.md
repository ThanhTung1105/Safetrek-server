# 🛡️ SafeTrek - Personal Safety Backend API

<div align="center">

![SafeTrek Logo](public/images/logo.png)

**Hệ thống backend cho ứng dụng an toàn cá nhân SafeTrek**

[![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://www.php.net)
[![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://www.mysql.com)
[![Sanctum](https://img.shields.io/badge/Sanctum-API_Auth-FF6B6B?style=for-the-badge)](https://laravel.com/docs/sanctum)

</div>

---

## 📋 Giới Thiệu

**SafeTrek** là một hệ thống backend API toàn diện được xây dựng bằng Laravel 11, cung cấp các chức năng bảo mật cho ứng dụng di động giúp người dùng an toàn trong các chuyến đi. Hệ thống tích hợp tracking GPS real-time, cảnh báo khẩn cấp thông minh, và hệ thống Duress PIN độc đáo để bảo vệ người dùng trong các tình huống nguy hiểm.

### ✨ Điểm Nổi Bật

-   🚨 **Panic Button** - Gửi cảnh báo khẩn cấp ngay lập tức
-   🔐 **Duress PIN** - Mã PIN đặc biệt để gửi cảnh báo kín đáo
-   📍 **GPS Tracking** - Theo dõi vị trí real-time mỗi 30 giây
-   ⏰ **Timer Alerts** - Cảnh báo tự động khi hết thời gian chuyến đi
-   👥 **Guardian System** - Quản lý tối đa 5 người liên hệ khẩn cấp
-   🔔 **Smart Notifications** - Push notifications qua Firebase
-   🎯 **Admin Panel** - Giao diện quản trị hiện đại với Leaflet maps

---

## 🚀 Tính Năng Chính

### 🔐 Authentication & Security

-   ✅ Login/Register với số điện thoại
-   ✅ Laravel Sanctum API authentication
-   ✅ Safety PIN & Duress PIN system
-   ✅ Password change endpoint
-   ✅ Role-based access (User/Admin)

### 🚗 Trip Management

-   ✅ Start trip với timer tùy chỉnh (1-1440 phút)
-   ✅ Real-time GPS location updates
-   ✅ End trip với PIN verification
-   ✅ Panic button từ bất kỳ đâu
-   ✅ Trip history với full GPS route

### 👥 Guardian Management

-   ✅ Thêm/xóa guardian (max 5)
-   ✅ Guardian status (pending/accepted/rejected)
-   ✅ Gửi SMS/Email alerts tự động

### 🎛️ Admin Panel

-   ✅ Dashboard tổng quan real-time
-   ✅ User management với search
-   ✅ Trip detail với interactive Leaflet map
-   ✅ GPS route visualization
-   ✅ Alert history tracking

---

## 🛠️ Tech Stack

### Backend Framework

-   **Laravel 11** - PHP framework hiện đại
-   **PHP 8.2+** - Latest PHP version
-   **MySQL 8.0** - Relational database

### Authentication & API

-   **Laravel Sanctum** - API token authentication
-   **RESTful API** - Standard API architecture
-   **CORS** - Cross-origin resource sharing enabled

### Real-time & Notifications

-   **Laravel Queues** - Background job processing
-   **Firebase Cloud Messaging** - Push notifications
-   **SMS Integration** - Emergency alerts (planned)

### Frontend (Admin Panel)

-   **Tailwind CSS** - Utility-first CSS framework
-   **Leaflet.js** - Interactive maps
-   **Font Awesome 6** - Icon library

### Development Tools

-   **Composer** - PHP dependency manager
-   **Git** - Version control
-   **Artisan CLI** - Laravel command-line tool

---


## 🔧 Installation

### Prerequisites

-   PHP >= 8.2
-   Composer
-   MySQL >= 8.0
-   Node.js & NPM (optional, for assets)

### Setup Steps

1. **Clone repository**

```bash
git clone https://github.com/ThanhTung1105/Safetrek-server.git
cd safetrek-server
```

2. **Install dependencies**

```bash
composer install
```

3. **Environment setup**

```bash
cp .env.example .env
php artisan key:generate
```

4. **Configure database**
   Edit `.env` file:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=safetrek
DB_USERNAME=root
DB_PASSWORD=your_password
```

5. **Run migrations**

```bash
php artisan migrate
```

6. **Start development server**

```bash
php artisan serve
```

7. **Start queue worker** (separate terminal)

```bash
php artisan queue:work
```

8. **Start scheduler** (for timer alerts)

```bash
php artisan schedule:work
```

---

## 📡 API Endpoints

### Authentication

```
POST   /api/register              # Register new user
POST   /api/login                 # Login with phone & password
POST   /api/logout                # Logout (revoke token)
GET    /api/me                    # Get authenticated user
POST   /api/setup-pins            # Setup Safety & Duress PINs
POST   /api/change-password       # Change password
POST   /api/update-fcm-token      # Update Firebase token
```

### Trip Management

```
POST   /api/trips/start           # Start new trip with timer
POST   /api/trips/end             # End trip with PIN
POST   /api/trips/panic           # Panic button alert
POST   /api/trips/update-location # Update GPS location
GET    /api/trips/active          # Get current active trip
GET    /api/trips/history         # Get trip history
```

### Guardian Management

```
GET    /api/guardians             # List guardians
POST   /api/guardians             # Add guardian
PUT    /api/guardians/{id}/status # Update status
DELETE /api/guardians/{id}        # Remove guardian
```

📖 **Full API Documentation:** See [`docs/validation_rules.md`](docs/validation_rules.md)

---

## 🗄️ Database Schema

### Core Tables

-   **users** - User accounts & authentication
-   **guardians** - Emergency contacts
-   **trips** - Trip records with status
-   **location_history** - GPS tracking data

**ER Diagram & Details:** See [`docs/database_schema.md`](docs/database_schema.md)

---

## 🎯 Key Workflows

### 1. **Normal Trip (Safety PIN)**

```
User starts trip → Timer counting → Arrives safely → Enters Safety PIN → Trip completed
```

### 2. **Duress PIN Scenario**

```
User in danger → Forced to end trip → Enters Duress PIN →
Silent alert sent to guardians → App shows "normal" response to attacker
```

### 3. **Timer Expired**

```
User starts trip → Time limit reached → No response for 60s →
Automatic alert to all guardians with last known location
```

### 4. **Panic Button**

```
User in immediate danger → Presses panic button →
Instant alert to all guardians → Creates panic trip record
```

---

## 🔐 Security Features

-   ✅ **Duress PIN Logic** - Silent emergency alerts
-   ✅ **PIN Hashing** - Bcrypt encryption
-   ✅ **Token Authentication** - Sanctum secure tokens
-   ✅ **CORS Protection** - API access control
-   ✅ **Admin Middleware** - Role-based access
-   ✅ **Soft Deletes** - Data retention compliance

---

## 📱 Mobile Integration

### Flutter/Dart Setup

```dart
// API Base URL
const String baseUrl = 'http://your-server.com/api';

// Headers for all requests
final headers = {
  'Authorization': 'Bearer $token',
  'Accept': 'application/json',
  'Content-Type': 'application/json',
};
```

**Mobile Developer Guide:** See [`docs/validation_rules.md`](docs/validation_rules.md)

---

## 🧪 Testing

### Test Admin Account

```
Phone: 0123456789
Password: admin123
```

### Test API with cURL

```bash
# Login
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"phone_number":"0987654321","password":"password123"}'

# Start Trip (with token)
curl -X POST http://localhost:8000/api/trips/start \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"destination_name":"Hospital","duration_minutes":30}'
```

---

## 🚀 Deployment

### Production Checklist

-   [ ] Set `APP_ENV=production` in `.env`
-   [ ] Set `APP_DEBUG=false`
-   [ ] Configure proper database credentials
-   [ ] Set up queue worker as daemon
-   [ ] Configure Laravel scheduler cron job
-   [ ] Enable HTTPS
-   [ ] Set up Firebase FCM credentials
-   [ ] Configure CORS for production domains

### Cron Job (for scheduler)

```bash
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

---

## 📊 Performance

-   **GPS Update Frequency:** Every 30 seconds
-   **Timer Check Interval:** Every 60 seconds
-   **Queue Processing:** Real-time background jobs
-   **Database Indexing:** Optimized for queries
-   **API Response Time:** < 200ms average

---

## 🤝 Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository
2. Create feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Open Pull Request

---

## 👥 Team

<table>
  <tr>
    <td align="center">
      <a href="https://github.com/ThanhTung1105">
        <img src="https://github.com/ThanhTung1105.png" width="100px;" alt="Đỗ Thanh Tùng"/><br />
        <sub><b>Đỗ Thanh Tùng</b></sub>
      </a><br />
      Backend Developer
    </td>
    <td align="center">
      <img src="https://via.placeholder.com/100?text=TV" width="100px;" alt="Trần Ngọc Vinh"/><br />
      <sub><b>Trần Ngọc Vinh</b></sub><br />
       Developer
    </td>
    <td align="center">
      <img src="https://via.placeholder.com/100?text=NQ" width="100px;" alt="Nguyễn Ngọc Quỳnh"/><br />
      <sub><b>Nguyễn Ngọc Quỳnh</b></sub><br />
      Developer
    </td>
  </tr>
</table>

---

## 📄 License

This project is proprietary software developed for educational purposes.

---

## 📞 Contact

**Project Link:** [https://github.com/ThanhTung1105/Safetrek-server](https://github.com/ThanhTung1105/Safetrek-server)

---

<div align="center">

**Made with ❤️ by SafeTrek Team**

🛡️ _Your safety, our priority_ 🛡️

</div>
