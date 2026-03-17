<?php

namespace App\Jobs;

use App\Models\Channel;
use App\Models\Message;
use App\Models\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendMessageNotification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Message $message,
        public Channel $channel
    ) {}

    public function handle(): void
    {
        // Notify all channel members except the sender
        $this->channel->members()
                      ->where('user_id', '!=', $this->message->user_id)
                      ->each(function ($member) {
                          Notification::create([
                              'user_id' => $member->id,
                              'type'    => 'new_message',
                              'data'    => [
                                  'message_id'   => $this->message->id,
                                  'channel_id'   => $this->channel->id,
                                  'channel_name' => $this->channel->name,
                                  'sender_name'  => $this->message->user->name,
                                  'preview'      => substr($this->message->content, 0, 50),
                              ],
                          ]);
                      });
    }
}