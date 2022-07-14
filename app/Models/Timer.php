<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Timer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'started_at',
        'stopped_at',
        'total_time',
        'image',
    ];
    //changing datatype of two fields from string to timezone
    protected $casts = [

        'started_at' => 'datetime',
        'stopped_at' => 'datetime',

    ];

    //one-to-one relation with User model
    public function users()
    {
        $this->belongsTo(User::class);
    }

    protected $dates = [
        'started_at',
        'stopped_at'
    ];
    //calling accessors for getting daily,weekly and monthly time
    protected $appends = ['today_time', 'weekly_time', 'monthly_time'];

    //this should get daily_time
    public function getTodayTimeAttribute()
    {
        $timer = Timer::whereDate('started_at', Carbon::now()->toDateString())
            ->sum('total_time');
        $data = gmdate("H:i:s", $timer);
        return $data;
    }

    //it would give weekly time
    public function getWeeklyTimeAttribute()
    {
        $timer = Timer::whereBetween('started_at', [Carbon::now()->startOfWeek()->toDateString(), Carbon::today()->addDay()])
            ->sum('total_time');
        $data = gmdate("H:i:s", $timer);
        return $data;
    }

    //it would give monthly time
    public function getMonthlyTimeAttribute()
    {
        $timer = Timer::whereBetween('started_at', [Carbon::now()->startOfMonth()->toDateString(), Carbon::today()->addDay()])
            ->sum('total_time');
        $data = gmdate("H:i:s", $timer);
        return $data;
    }
}
