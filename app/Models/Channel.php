<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Channel extends Model
{
    protected $fillable = [
        'workspace_id', 'created_by', 'name', 'description', 'is_private'
    ];

    protected function casts(): array
    {
        return ['is_private' => 'boolean'];
    }

    public function workspace()
    {
        return $this->belongsTo(Workspace::class);
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'channel_members')
                    ->withPivot('last_read_at');
    }

    public function messages()
    {
        return $this->hasMany(Message::class)->latest();
    }

    public function latestMessage()
    {
        return $this->hasOne(Message::class)->latestOfMany();
    }

    // Unread count for a specific user
    public function unreadCount(int $userId): int
    {
        $member = $this->members()
                       ->where('user_id', $userId)
                       ->first();

        if (!$member) return 0;

        return $this->messages()
                    ->where('created_at', '>', $member->pivot->last_read_at)
                    ->count();
    }
    
}