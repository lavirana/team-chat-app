<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function search(Request $request)
    {
        $users = \App\Models\User::where('name', 'like', '%' . $request->query . '%')
                                 ->orWhere('email', 'like', '%' . $request->query . '%')
                                 ->limit(10)
                                 ->get(['id', 'name', 'email', 'avatar', 'status']);

        return response()->json($users);
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'status' => 'required|in:online,offline,away'
        ]);

        $request->user()->update(['status' => $request->status]);
        return response()->json(['message' => 'Status updated']);
    }

    public function uploadAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|max:2048'
        ]);

        $path = $request->file('avatar')->store('avatars', 'public');
        $request->user()->update(['avatar' => $path]);
        return response()->json(['avatar' => $path]);
    }
}