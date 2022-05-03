<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ForumComment extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function User()
    {
        return $this->belongsTo(User::class)->select(['id', 'username']);
    }

    public function Forum()
    {
        return $this->belongsTo(ForumComment::class);
    }
}
