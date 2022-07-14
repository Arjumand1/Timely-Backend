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

    protected $casts = [

        'started_at' => 'datetime',
        'stopped_at' => 'datetime',

    ];

    public function users()
    {
        $this->belongsTo(User::class);
    }
    protected $dates = [
        'started_at',
        'stopped_at'
    ];
    protected $appends = ['today_time','weekly_time','monthly_time'];

    public function getTodayTimeAttribute()
    {
            $time = $this->total_time;

            $timer = Timer::whereDate('started_at', Carbon::now()->toDateString())
            ->sum('total_time');

            $data = gmdate("H:i:s",$timer);
            return $data;
    }
    public function getWeeklyTimeAttribute()
    {
        $time = $this->total_time;
        $timer = Timer::whereBetween('started_at', [Carbon::now()->startOfWeek()->toDateString(), Carbon::today()->addDay()])
        ->sum('total_time');
        $data = gmdate("H:i:s",$timer);
        return $data;
    }
    public function getMonthlyTimeAttribute()
    {
        $time = $this->total_time;
        $timer = Timer::whereBetween('started_at', [Carbon::now()->startOfMonth()->toDateString(), Carbon::today()->addDay()])
        ->sum('total_time');
        $data = gmdate("H:i:s",$timer);
        return $data;
    }
}
