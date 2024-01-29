<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Url extends Model
{
    use HasFactory;
    protected $fillable = [
        'original_url',
        'key',
        'ip',
       'user_id',
        'block_count',
        'blocked_until',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
