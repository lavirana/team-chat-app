<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Workspaces extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'owner_id',
        'logo'
    ];

protected static function boot()
    {
    // Auto generated slug
    parent::boot();
    static::creating(function ($workspace) {
        $workspace->slug = Str::slug($workspace->name) . '-' . Str::random(6);
    });
}

public function owner()
{
    return $this->belongsTo(User::class, 'owner_id');
}

public function members()
{
    return $this->belongsToMany(User::class, 'workspace_members')
                ->withPivot('role')
                ->withTimestamps();
}
public function channels()
{
    return $this->hasMany(Channel::class);
}
}
