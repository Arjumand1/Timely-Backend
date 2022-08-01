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
    //changing datatype of two fields from string to timezone
    protected $casts = [

        'started_at' => 'datetime',
        'stopped_at' => 'datetime',

        // 'captured_at' => 'datetime'

    ];

    //one-to-one relation with User model
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
