<?php
namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Message $message) {}

    public function broadcastOn(): array
    {
        return [
            new PresenceChannel('channel.' . $this->message->channel_id),
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
                'user_id'     => $this->message->user_id,
                'user'        => $this->message->user,
                'attachments' => $this->message->attachments,
                'is_edited'   => $this->message->is_edited,
                'created_at'  => $this->message->created_at,
            ]
        ];
    }

    public function broadcastAs(): string
    {
        return 'message.sent';
    }
}