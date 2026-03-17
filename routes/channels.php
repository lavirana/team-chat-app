<?php

// routes/channels.php

use App\Models\Channel;
use Illuminate\Support\Facades\Broadcast;

// Presence channel — for chat rooms
// Returns user info if authorized, false if not
Broadcast::channel('channel.{channelId}', function ($user, $channelId) {
    $channel = Channel::find($channelId);
    if (!$channel) return false;

    $isMember = $channel->members()->where('user_id', $user->id)->exists();
    if (!$isMember) return false;

    // Return user data — presence channel needs this
    return [
        'id'     => $user->id,
        'name'   => $user->name,
        'avatar' => $user->avatar,
        'status' => $user->status,
    ];
});

// Private channel for direct messages
Broadcast::channel('dm.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});