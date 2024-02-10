<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'body', 'published_at'];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    // Define the relationship with the user who created the post
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Define the relationship with comments
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
