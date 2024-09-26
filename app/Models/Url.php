<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Url extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'visitor_token',
        'long_url',
        'short_code',
    ];

    function clickDetails() {
        return $this->hasMany(ClickDetail::class);
    }
}
