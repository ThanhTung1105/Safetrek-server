<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Login with phone number and password (Sanctum)
     */
    public function login(Request $request)
    {
        $request->validate([
            'phone_number' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('phone_number', $request->phone_number)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'phone_number' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Create Sanctum token
        $token = $user->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'user' => $user,
                'token' => $token,
            ],
        ]);
    }

    /**
     * Register new user
     */
    public function register(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'phone_number' => 'required|string|unique:users,phone_number',
            'email' => 'nullable|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'full_name' => $request->full_name,
            'phone_number' => $request->phone_number,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user',
        ]);

        $token = $user->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Registration successful',
            'data' => [
                'user' => $user,
                'token' => $token,
            ],
        ], 201);
    }

    /**
     * Setup Safety and Duress PINs
     */
    public function setupPins(Request $request)
    {
        $request->validate([
            'safety_pin' => 'required|string|size:4|different:duress_pin',
            'duress_pin' => 'required|string|size:4|different:safety_pin',
        ]);

        $user = $request->user();

        $user->update([
            'safety_pin_hash' => $request->safety_pin,
            'duress_pin_hash' => $request->duress_pin,
            'is_pin_setup' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'PINs setup successful',
        ]);
    }



    /**
     * Verify a PIN during a trip and return its type (safety or duress)
     */
    public function verifyTripPin(Request $request)
    {
        $request->validate([
            'pin' => 'required|string|size:4',
        ]);

        $user = $request->user();

        // 1. Check if it's the safety PIN
        if ($user->safety_pin_hash && Hash::check($request->pin, $user->safety_pin_hash)) {
            return response()->json([
                'success' => true,
                'message' => 'Xác thực PIN an toàn thành công.',
                'data' => [
                    'pin_type' => 'safety' // Trả về loại PIN
                ]
            ]);
        }

        // 2. Check if it's the duress PIN
        if ($user->duress_pin_hash && Hash::check($request->pin, $user->duress_pin_hash)) {
            // !! QUAN TRỌNG: Kích hoạt logic gửi cảnh báo khẩn cấp tại đây
            // Ví dụ: event(new DuressPinEntered($user));
            
            return response()->json([
                'success' => true,
                'message' => 'Xác thực PIN ép buộc thành công.',
                'data' => [
                    'pin_type' => 'duress' // Trả về loại PIN
                ]
            ]);
        }

        // 3. If neither PIN matches
        return response()->json([
            'success' => false,
            'message' => 'Mã PIN không đúng.',
        ], 422);
    }
    
        /**
     * Update the user's safety PIN
     */
    public function updateSafetyPin(Request $request)
    {
        $user = $request->user();

        // Validate new safety_pin, ensuring it's different from the current duress_pin
        $request->validate([
            'safety_pin' => ['required', 'string', 'size:4', function ($attribute, $value, $fail) use ($user) {
                if ($user->duress_pin_hash && Hash::check($value, $user->duress_pin_hash)) {
                    $fail('Mã PIN an toàn phải khác với mã PIN nguy hiểm.');
                }
            }],
        ]);

        $user->update([
            'safety_pin_hash' => Hash::make($request->safety_pin),
        ]);

        return response()->json(['success' => true, 'message' => 'Cập nhật PIN an toàn thành công.']);
    }

    /**
     * Update the user's duress PIN
     */
    public function updateDuressPin(Request $request)
    {
        $user = $request->user();

        // Validate new duress_pin, ensuring it's different from the current safety_pin
        $request->validate([
            'duress_pin' => ['required', 'string', 'size:4', function ($attribute, $value, $fail) use ($user) {
                if ($user->safety_pin_hash && Hash::check($value, $user->safety_pin_hash)) {
                    $fail('Mã PIN nguy hiểm phải khác với mã PIN an toàn.');
                }
            }],
        ]);

        $user->update([
            'duress_pin_hash' => Hash::make($request->duress_pin),
        ]);

        return response()->json(['success' => true, 'message' => 'Cập nhật PIN nguy hiểm thành công.']);
    }

    /**
     * Logout (revoke token)
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout successful',
        ]);
    }

    /**
     * Get authenticated user
     */
    public function me(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => $request->user(),
        ]);
    }

    /**
     * Update FCM token for push notifications
     */
    public function updateFcmToken(Request $request)
    {
        $request->validate([
            'fcm_token' => 'required|string',
        ]);

        $request->user()->update([
            'fcm_token' => $request->fcm_token,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'FCM token updated',
        ]);
    }

    /**
     * Change password (requires current password verification)
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        $user = $request->user();

        // Verify current password
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Mật khẩu hiện tại không đúng',
            ], 401);
        }

        // Update to new password
        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Đổi mật khẩu thành công',
        ]);
    }
}
