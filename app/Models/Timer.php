<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\CarbonInterval;
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
        'total_time' => 'datetime',
    ];

    public function users()
    {
        $this->belongsTo(User::class);
    }
    protected $dates = [
        'started_at',
        'stopped_at'
    ];
    protected $appends = ['today_time',];

    public function getTodayTimeAttribute()
    {
        $this->created_at = Carbon::now()->startOfDay()->format("Y-m-d H:i:s");
        $data = $this->started_at->diffInSeconds($this->stopped_at);
        $data = gmdate('H:i:s',$data);
        return $data;
    }
    // public function getWeeklyTimeAttribute()
    // {
    //     $this->created_at = Carbon::now()->startOfWeek()->format("Y-m-d H:i:s");
    //     $data = $this->started_at->diffInSeconds($this->stopped_at);
    //     $sum = $data + $this->started_at->diffInSeconds($this->stopped_at);
    //     $data = gmdate('H:i:s', $sum);
    //     return $data;
    // }
    // public function getMonthlyTimeAttribute()
    // {
    //     $this->created_at = Carbon::now()->startOfMonth()->format("Y-m-d H:i:s");
    //     $data = $this->started_at->diffInSeconds($this->stopped_at);
    //     $sum = $data + $this->started_at->diffInSeconds($this->stopped_at);
    //     $data = gmdate('H:i:s', $sum);
    //     return $data;
    // }
}
