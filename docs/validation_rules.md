# 📋 SafeTrek API Validation Rules

## 🎯 Validation Strategy

**Principle:** ✅ **Validate on BOTH sides**
- **Frontend (Mobile App):** Better UX, instant feedback, reduce server load
- **Backend (Server):** Security, data integrity, prevent malicious requests

---

## 🔐 Authentication API

### 1. **POST /api/register** - User Registration

**Backend Validation:**
```php
'full_name' => 'required|string|max:255',
'phone_number' => 'required|string|unique:users,phone_number',
'email' => 'nullable|email|unique:users,email',
'password' => 'required|string|min:6|confirmed',
```

**Mobile Implementation (Dart):**
```dart
String? validateRegister({
  required String fullName,
  required String phoneNumber,
  String? email,
  required String password,
  required String passwordConfirmation,
}) {
  // Full name
  if (fullName.isEmpty) return 'Vui lòng nhập họ tên';
  if (fullName.length > 255) return 'Họ tên tối đa 255 ký tự';
  
  // Phone number
  if (phoneNumber.isEmpty) return 'Vui lòng nhập số điện thoại';
  // Optional: Validate Vietnamese phone format
  if (!RegExp(r'^(0|\+84)[0-9]{9,10}$').hasMatch(phoneNumber)) {
    return 'Số điện thoại không hợp lệ';
  }
  
  // Email (optional)
  if (email != null && email.isNotEmpty) {
    if (!RegExp(r'^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$').hasMatch(email)) {
      return 'Email không hợp lệ';
    }
  }
  
  // Password
  if (password.isEmpty) return 'Vui lòng nhập mật khẩu';
  if (password.length < 6) return 'Mật khẩu tối thiểu 6 ký tự';
  if (password != passwordConfirmation) return 'Mật khẩu xác nhận không khớp';
  
  return null; // Valid
}
```

**Error Responses:**
- Phone already exists: `422` with message
- Email already exists: `422` with message

---

### 2. **POST /api/login** - User Login

**Backend Validation:**
```php
'phone_number' => 'required|string',
'password' => 'required|string',
```

**Mobile Implementation:**
```dart
String? validateLogin({
  required String phoneNumber,
  required String password,
}) {
  if (phoneNumber.isEmpty) return 'Vui lòng nhập số điện thoại';
  if (password.isEmpty) return 'Vui lòng nhập mật khẩu';
  return null;
}
```

---

### 3. **POST /api/setup-pins** - Setup Safety & Duress PINs

**Backend Validation:**
```php
'safety_pin' => 'required|string|size:4|different:duress_pin',
'duress_pin' => 'required|string|size:4|different:safety_pin',
```

**Mobile Implementation:**
```dart
String? validatePinSetup({
  required String safetyPin,
  required String duressPin,
}) {
  // Safety PIN
  if (safetyPin.isEmpty) return 'Vui lòng nhập PIN an toàn';
  if (safetyPin.length != 4) return 'PIN phải có đúng 4 số';
  if (!RegExp(r'^[0-9]{4}$').hasMatch(safetyPin)) {
    return 'PIN chỉ được chứa số';
  }
  
  // Duress PIN
  if (duressPin.isEmpty) return 'Vui lòng nhập PIN nguy hiểm';
  if (duressPin.length != 4) return 'PIN phải có đúng 4 số';
  if (!RegExp(r'^[0-9]{4}$').hasMatch(duressPin)) {
    return 'PIN chỉ được chứa số';
  }
  
  // Must be different
  if (safetyPin == duressPin) {
    return 'PIN an toàn và PIN nguy hiểm phải khác nhau';
  }
  
  return null;
}
```

---

### 4. **POST /api/verify-trip-pin** - Verify PIN During Trip

**Backend Validation:**
```php
'pin' => 'required|string|size:4',
```

**Mobile Implementation:**
```dart
String? validatePin(String pin) {
  if (pin.isEmpty) return 'Vui lòng nhập PIN';
  if (pin.length != 4) return 'PIN phải có đúng 4 số';
  if (!RegExp(r'^[0-9]{4}$').hasMatch(pin)) {
    return 'PIN chỉ được chứa số';
  }
  return null;
}
```

---

### 5. **POST /api/update-fcm-token** - Update Firebase Token

**Backend Validation:**
```php
'fcm_token' => 'required|string',
```

**Mobile Implementation:**
```dart
String? validateFcmToken(String token) {
  if (token.isEmpty) return 'FCM token không được để trống';
  return null;
}
```

---

## 🚗 Trip Management API

### 6. **POST /api/trips/start** - Start Trip

**Backend Validation:**
```php
'destination_name' => 'nullable|string|max:255',
'duration_minutes' => 'required|integer|min:1|max:1440', // Max 24h
```

**Mobile Implementation:**
```dart
String? validateStartTrip({
  String? destinationName,
  required int durationMinutes,
}) {
  // Destination (optional)
  if (destinationName != null && destinationName.length > 255) {
    return 'Tên điểm đến tối đa 255 ký tự';
  }
  
  // Duration
  if (durationMinutes < 1) return 'Thời gian tối thiểu 1 phút';
  if (durationMinutes > 1440) return 'Thời gian tối đa 24 giờ (1440 phút)';
  
  return null;
}
```

**Business Logic:**
- User can only have **1 active trip** at a time
- If active trip exists → reject with 400 error

---

### 7. **POST /api/trips/panic** - Panic Button

**Backend Validation:**
```php
'latitude' => 'nullable|numeric|between:-90,90',
'longitude' => 'nullable|numeric|between:-180,180',
'battery_level' => 'nullable|integer|between:0,100',
```

**Mobile Implementation:**
```dart
String? validatePanicButton({
  double? latitude,
  double? longitude,
  int? batteryLevel,
}) {
  // Location (optional)
  if (latitude != null) {
    if (latitude < -90 || latitude > 90) {
      return 'Vĩ độ phải trong khoảng -90 đến 90';
    }
  }
  
  if (longitude != null) {
    if (longitude < -180 || longitude > 180) {
      return 'Kinh độ phải trong khoảng -180 đến 180';
    }
  }
  
  // Battery (optional)
  if (batteryLevel != null) {
    if (batteryLevel < 0 || batteryLevel > 100) {
      return 'Mức pin phải từ 0-100%';
    }
  }
  
  return null;
}
```

---

### 8. **POST /api/trips/update-location** - Update Location

**Backend Validation:**
```php
'trip_id' => 'required|exists:trips,id',
'latitude' => 'required|numeric|between:-90,90',
'longitude' => 'required|numeric|between:-180,180',
'battery_level' => 'nullable|integer|between:0,100',
```

**Mobile Implementation:**
```dart
String? validateLocationUpdate({
  required int tripId,
  required double latitude,
  required double longitude,
  int? batteryLevel,
}) {
  if (tripId <= 0) return 'Trip ID không hợp lệ';
  
  if (latitude < -90 || latitude > 90) {
    return 'Vĩ độ phải trong khoảng -90 đến 90';
  }
  
  if (longitude < -180 || longitude > 180) {
    return 'Kinh độ phải trong khoảng -180 đến 180';
  }
  
  if (batteryLevel != null && (batteryLevel < 0 || batteryLevel > 100)) {
    return 'Mức pin phải từ 0-100%';
  }
  
  return null;
}
```

---

### 9. **POST /api/trips/end** - End Trip

**Backend Validation:**
```php
'trip_id' => 'required|exists:trips,id',
'pin_code' => 'required|string|size:4',
'latitude' => 'nullable|numeric|between:-90,90',
'longitude' => 'nullable|numeric|between:-180,180',
'battery_level' => 'nullable|integer|between:0,100',
```

**Mobile Implementation:**
```dart
String? validateEndTrip({
  required int tripId,
  required String pinCode,
  double? latitude,
  double? longitude,
  int? batteryLevel,
}) {
  if (tripId <= 0) return 'Trip ID không hợp lệ';
  
  // PIN validation
  String? pinError = validatePin(pinCode);
  if (pinError != null) return pinError;
  
  // Location (optional)
  if (latitude != null && (latitude < -90 || latitude > 90)) {
    return 'Vĩ độ không hợp lệ';
  }
  if (longitude != null && (longitude < -180 || longitude > 180)) {
    return 'Kinh độ không hợp lệ';
  }
  
  // Battery (optional)
  if (batteryLevel != null && (batteryLevel < 0 || batteryLevel > 100)) {
    return 'Mức pin không hợp lệ';
  }
  
  return null;
}
```

---

## 👥 Guardian Management API

### 10. **POST /api/guardians** - Add Guardian

**Backend Validation:**
```php
'contact_name' => 'required|string|max:255',
'contact_phone_number' => 'required|string|max:20',
```

**Mobile Implementation:**
```dart
String? validateAddGuardian({
  required String contactName,
  required String contactPhoneNumber,
}) {
  if (contactName.isEmpty) return 'Vui lòng nhập tên người liên hệ';
  if (contactName.length > 255) return 'Tên tối đa 255 ký tự';
  
  if (contactPhoneNumber.isEmpty) return 'Vui lòng nhập số điện thoại';
  if (contactPhoneNumber.length > 20) return 'Số điện thoại tối đa 20 ký tự';
  
  // Optional: Vietnamese phone format
  if (!RegExp(r'^(0|\+84)[0-9]{9,10}$').hasMatch(contactPhoneNumber)) {
    return 'Số điện thoại không hợp lệ';
  }
  
  return null;
}
```

**Business Logic:**
- Maximum **5 guardians** per user
- Backend returns 400 if limit reached

---

### 11. **PUT /api/guardians/{id}/status** - Update Status

**Backend Validation:**
```php
'status' => 'required|in:pending,accepted,rejected',
```

**Mobile Implementation:**
```dart
String? validateGuardianStatus(String status) {
  const validStatuses = ['pending', 'accepted', 'rejected'];
  if (!validStatuses.contains(status)) {
    return 'Trạng thái không hợp lệ';
  }
  return null;
}
```

---

## 🛠️ Common Validation Helpers (Dart)

```dart
class ValidationHelper {
  // Phone number (Vietnamese format)
  static bool isValidPhone(String phone) {
    return RegExp(r'^(0|\+84)[0-9]{9,10}$').hasMatch(phone);
  }
  
  // Email
  static bool isValidEmail(String email) {
    return RegExp(r'^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$').hasMatch(email);
  }
  
  // PIN (4 digits)
  static bool isValidPin(String pin) {
    return RegExp(r'^[0-9]{4}$').hasMatch(pin);
  }
  
  // GPS coordinates
  static bool isValidLatitude(double lat) {
    return lat >= -90 && lat <= 90;
  }
  
  static bool isValidLongitude(double lng) {
    return lng >= -180 && lng <= 180;
  }
  
  // Battery level
  static bool isValidBattery(int battery) {
    return battery >= 0 && battery <= 100;
  }
}
```

---

## ⚠️ Error Handling

**Backend Response Format:**

**Success (200/201):**
```json
{
  "success": true,
  "message": "...",
  "data": {...}
}
```

**Validation Error (422):**
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "phone_number": ["The phone number has already been taken."],
    "password": ["The password must be at least 6 characters."]
  }
}
```

**Other Errors (400/401/403/404/500):**
```json
{
  "success": false,
  "message": "Error message here"
}
```

---

## 🎯 Best Practices

**Mobile App:**
1. ✅ Validate **before** sending request
2. ✅ Show validation errors immediately (no API call)
3. ✅ Disable submit button if invalid
4. ✅ Handle backend errors gracefully
5. ✅ Show user-friendly messages

**Example:**
```dart
Future<void> startTrip() async {
  // Frontend validation first
  String? error = validateStartTrip(
    destinationName: _destinationController.text,
    durationMinutes: _duration,
  );
  
  if (error != null) {
    // Show error, don't call API
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(content: Text(error)),
    );
    return;
  }
  
  // Call API
  try {
    final response = await _tripService.startTrip(...);
    // Handle success
  } catch (e) {
    // Handle backend error
    if (e is ValidationException) {
      // Show validation errors from backend
    }
  }
}
```

---

## 📊 Validation Summary

| Endpoint | Required Fields | Optional Fields | Special Rules |
|----------|----------------|-----------------|---------------|
| Register | full_name, phone_number, password | email | phone unique, password min 6 |
| Login | phone_number, password | - | - |
| Setup PINs | safety_pin, duress_pin | - | Must be different, size 4 |
| Start Trip | duration_minutes | destination_name | duration 1-1440 min |
| Panic Button | - | lat, lng, battery | - |
| Update Location | trip_id, lat, lng | battery | - |
| End Trip | trip_id, pin_code | lat, lng, battery | - |
| Add Guardian | contact_name, contact_phone_number | - | Max 5 guardians |

---

**Last Updated:** 2026-01-05  
**Version:** 1.0
