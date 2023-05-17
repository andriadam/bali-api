<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function User()
    {
        return $this->belongsTo(User::class)->select(['id', 'username']);
    }

    public function comments()
    {
        return $this->hasMany(NewsComment::class);
    }
}
