<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [
        'channel_id', 'user_id', 'parent_id',
        'content', 'type', 'is_edited'
    ];

    protected function casts(): array
    {
        return ['is_edited' => 'boolean'];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function channel()
    {
        return $this->belongsTo(Channel::class);
    }

    public function attachments()
    {
        return $this->hasMany(MessageAttachment::class);
    }

    public function reactions()
    {
        return $this->hasMany(MessageReaction::class);
    }

    public function replies()
    {
        return $this->hasMany(Message::class, 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo(Message::class, 'parent_id');
    }
}