<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $notifications = $request->user()
                                 ->notifications()
                                 ->latest()
                                 ->paginate(20);

        return response()->json($notifications);
    }

    public function markRead(Request $request, $id)
    {
        $request->user()
                ->notifications()
                ->where('id', $id)
                ->update(['read_at' => now()]);

        return response()->json(['message' => 'Marked as read']);
    }

    public function markAllRead(Request $request)
    {
        $request->user()
                ->notifications()
                ->whereNull('read_at')
                ->update(['read_at' => now()]);

        return response()->json(['message' => 'All marked as read']);
    }
}