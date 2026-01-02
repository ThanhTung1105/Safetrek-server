<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    /**
     * Get safety tips and news
     * TODO: Implement with actual news/posts model
     */
    public function index(Request $request)
    {
        // Mock data for now
        $news = [
            [
                'id' => 1,
                'title' => 'Safety Tips for Walking Alone at Night',
                'content' => 'Always stay in well-lit areas, be aware of your surroundings...',
                'category' => 'safety_tip',
                'created_at' => now()->subDays(2),
            ],
            [
                'id' => 2,
                'title' => 'How to Use SafeTrek Effectively',
                'content' => 'Set up your emergency contacts, test your PINs regularly...',
                'category' => 'guide',
                'created_at' => now()->subDays(5),
            ],
        ];

        return response()->json([
            'success' => true,
            'data' => $news,
        ]);
    }
}
