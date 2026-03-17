<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;       
use App\Models\Workspaces;



class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'status',
        'last_seen_at'
    ];
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_seen_at' => 'datetime',
        ];
    }


    // Relationships

    public function workspaces(){
        return $this->belongsToMany(workspaces::class, 'workspace_members')
             ->withPivot('role')
            ->withTimestamps();
    }

    public function channels(){
        return $this->belongsToMany(channels::class, 'channel_members')
             ->withPivot('last_read_at');
    }

    public function messages(){
        return $this->hasMany(messages::class);
    }

    public function notification(){
        return $this->hasMany(notification::class);
    }

    public function unreadNotificationsCount()
    {
        return $this->notifications()->whereNull('read_at')->count();
    }

     public function isOnline()
    {
        return cache()->has('user-is-online-' . $this->id);
    }


}
