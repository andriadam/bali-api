<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewsLog extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function User()
    {
        return $this->belongsTo(User::class)->select(['id', 'username']);
    }

    public function News()
    {
        return $this->belongsTo(News::class);
    }
}
