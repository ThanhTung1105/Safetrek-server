<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Guardian;
use Illuminate\Http\Request;

class GuardianController extends Controller
{
    /**
     * Get all guardians for authenticated user
     */
    public function index(Request $request)
    {
        $guardians = Guardian::where('user_id', $request->user()->id)->get();

        return response()->json([
            'success' => true,
            'data' => $guardians,
        ]);
    }

    /**
     * Add a new guardian (emergency contact)
     */
    public function store(Request $request)
    {
        $request->validate([
            'contact_name' => 'required|string|max:255',
            'contact_phone_number' => 'required|string|max:20',
        ]);

        $user = $request->user();

        // Check if user already has 5 guardians (limit)
        $currentCount = Guardian::where('user_id', $user->id)->count();
        if ($currentCount >= 5) {
            return response()->json([
                'success' => false,
                'message' => 'Maximum 5 guardians allowed',
            ], 400);
        }

        $guardian = Guardian::create([
            'user_id' => $user->id,
            'contact_name' => $request->contact_name,
            'contact_phone_number' => $request->contact_phone_number,
            'status' => 'pending',
        ]);

        // TODO: Send invitation to guardian via SMS/Email

        return response()->json([
            'success' => true,
            'message' => 'Guardian added successfully',
            'data' => $guardian,
        ], 201);
    }

    /**
     * Update guardian status
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,accepted,rejected',
        ]);

        $guardian = Guardian::findOrFail($id);

        // Verify guardian belongs to user
        if ($guardian->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $guardian->update([
            'status' => $request->status,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Guardian status updated',
            'data' => $guardian,
        ]);
    }

    /**
     * Remove a guardian
     */
    public function destroy(Request $request, $id)
    {
        $guardian = Guardian::findOrFail($id);

        if ($guardian->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $guardian->delete();

        return response()->json([
            'success' => true,
            'message' => 'Guardian removed successfully',
        ]);
    }
}
