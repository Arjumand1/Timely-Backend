<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Timer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'started_at',
        'stopped_at',
        'captured_at',
        'total_time',
        'daily_time',
        'weekly_time',
        'monthly_time',
        'image',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'stopped_at' => 'datetime',
    ];

    public function users()
    {
        return  $this->belongsTo(User::class);
    }

    protected $dates = [
        'started_at',
        'stopped_at',
        'captured_at'
    ];
}
