# 🔧 FIX: Session Expiration When Ending Trip

## 🐛 **Problem:**
User gets "Session expired, please login again" error when ending a trip with Safety PIN.

---

## 📊 **Root Cause Analysis:**

### **Backend Configuration:**
✅ **Sanctum token expiration:** `null` (NEVER expires)  
✅ **API route:** Protected by `auth:sanctum` middleware  
✅ **Database:** `personal_access_tokens` table stores tokens

**Conclusion:** Backend is configured correctly. Issue is on **mobile app side**.

---

## 🔍 **Mobile App Checklist:**

### **1. Token Storage:**
```dart
// ✅ CORRECT: Save token after login
SharedPreferences prefs = await SharedPreferences.getInstance();
await prefs.setString('auth_token', response['token']);
```

### **2. Token Sending:**
```dart
// ✅ CORRECT: Send token with EVERY API request
final headers = {
  'Authorization': 'Bearer ${authToken}',
  'Accept': 'application/json',
  'Content-Type': 'application/json',
};

final response = await http.post(
  Uri.parse('$baseUrl/trips/end'),
  headers: headers, // ← CRITICAL!
  body: jsonEncode(data),
);
```

**Common Mistakes:**
- ❌ Forgot to add `Authorization` header
- ❌ Token được save nhưng không retrieved khi call API
- ❌ Token bị clear/null trong quá trình trip

### **3. Token Lifecycle:**
```dart
// Check if token still exists before calling API
String? token = await getStoredToken();
if (token == null || token.isEmpty) {
  // Redirect to login
  Navigator.pushReplacementNamed(context, '/login');
  return;
}
```

---

## 🚀 **Solutions:**

### **Solution 1: Add Debug Logging (Recommended First)**

```dart
Future<http.Response> endTrip(Map<String, dynamic> data) async {
  String? token = await getStoredToken();
  
  // DEBUG: Print token (remove in production!)
  print('🔑 Token: ${token?.substring(0, 20)}...'); 
  print('📍 Calling /trips/end');
  
  if (token == null) {
    print('❌ ERROR: No token found!');
    throw Exception('Not authenticated');
  }
  
  final headers = {
    'Authorization': 'Bearer $token',
    'Accept': 'application/json',
    'Content-Type': 'application/json',
  };
  
  print('📤 Headers: $headers');
  
  final response = await http.post(
    Uri.parse('$baseUrl/trips/end'),
    headers: headers,
    body: jsonEncode(data),
  );
  
  print('📥 Response status: ${response.statusCode}');
  print('📥 Response body: ${response.body}');
  
  return response;
}
```

**Expected Debug Output:**
```
🔑 Token: 18|laravel_sanctum_abc...
📍 Calling /trips/end
📤 Headers: {Authorization: Bearer 18|laravel_sanctum_..., ...}
📥 Response status: 200
📥 Response body: {"success": true, ...}
```

**If you see:**
- ❌ `Token: null` → Token not saved or lost
- ❌ `Response status: 401` → Token invalid or not sent
- ❌ `Response status: 419` → CSRF issue (shouldn't happen with API)

---

### **Solution 2: Axios/Dio Interceptor (If using)**

```dart
// If using Dio package
dio.interceptors.add(InterceptorsWrapper(
  onRequest: (options, handler) async {
    String? token = await getStoredToken();
    if (token != null) {
      options.headers['Authorization'] = 'Bearer $token';
    }
    return handler.next(options);
  },
  onError: (error, handler) {
    if (error.response?.statusCode == 401) {
      // Token expired or invalid
      logout();
      navigateToLogin();
    }
    return handler.next(error);
  },
));
```

---

### **Solution 3: Backend Token Refresh (If needed)**

**Add to Backend (Optional):**
```php
// config/sanctum.php
'expiration' => 60 * 24 * 30, // 30 days instead of null
```

**Then mobile app needs to handle refresh:**
```dart
if (response.statusCode == 401) {
  // Try to refresh token or re-login
  await refreshToken();
  // Retry request
}
```

---

## 🧪 **Testing Steps:**

1. **Fresh Login:**
   ```
   1. Uninstall app → Reinstall
   2. Login with valid credentials
   3. Check SharedPreferences has token
   ```

2. **During Trip:**
   ```
   1. Start trip
   2. Wait 5-10 minutes
   3. End trip with Safety PIN
   4. Check logs for token
   ```

3. **Edge Cases:**
   ```
   - App backgrounded → foregrounded
   - Network disconnected → reconnected
   - Device locked → unlocked
   ```

---

## 📋 **Backend API Response Format:**

### **Success (200):**
```json
{
  "success": true,
  "message": "Trip ended successfully",
  "data": {...}
}
```

### **Unauthenticated (401):**
```json
{
  "message": "Unauthenticated."
}
```

### **Invalid PIN (401):**
```json
{
  "success": false,
  "message": "Invalid PIN code"
}
```

---

## 🎯 **Quick Fix Checklist:**

- [ ] Token is saved after login
- [ ] Token is retrieved before API calls
- [ ] `Authorization: Bearer {token}` header is added
- [ ] Token persists through app lifecycle
- [ ] Debug logs show token is sent
- [ ] Backend returns 200, not 401

---

## 📞 **Need Backend Help?**

If mobile team confirms:
✅ Token is sent correctly  
✅ Still getting 401 error

Then check backend:
```bash
# Check personal_access_tokens table
php artisan tinker
> \App\Models\PersonalAccessToken::latest()->first();

# Check logs
tail -f storage/logs/laravel.log
```

---

**Status:** 🔴 **Mobile team to implement debug logging first**
