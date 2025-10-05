<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('notifications.index', compact('notifications'));
    }

    public function markAsRead($id)
    {
        $notification = Notification::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();
        
        $notification->update(['read_status' => 'read']);
        
        return back()->with('success', 'Notification marked as read');
    }

    public function markAllAsRead()
    {
        Notification::where('user_id', Auth::id())
            ->where('read_status', 'unread')
            ->update(['read_status' => 'read']);
        
        return back()->with('success', 'All notifications marked as read');
    }

    public function destroy($id)
    {
        $notification = Notification::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();
        
        $notification->delete();
        
        return back()->with('success', 'Notification deleted');
    }

    public function unreadCount()
    {
        $count = Notification::where('user_id', Auth::id())
            ->where('read_status', 'unread')
            ->count();
        
        return response()->json(['count' => $count]);
    }
}