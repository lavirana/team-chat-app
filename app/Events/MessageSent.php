<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'message' => [
                'id'          => $this->message->id,
                'content'     => $this->message->content,
                'type'        => $this->message->type,
                'channel_id'  => $this->message->channel_id,
                'user'        => $this->message->user,
                'attachments' => $this->message->attachments,
                'created_at'  => $this->message->created_at,
            ]
            ];
    }

    public function broadcastAs(): string{
        return 'message.sent';
    }
}
