<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;


class Timer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'started_at',
        'stopped_at',
        'captured_at',
        'total_time',
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
    //calling accessors for getting daily,weekly and monthly time
    protected $appends = ['today_time', 'weekly_time', 'monthly_time'];

    //this should get daily_time
    public function getTodayTimeAttribute()
    {
        $timer = Timer::where('user_id', auth()->user()->id)->whereDate('started_at', Carbon::today()->toDateString())
            ->sum('total_time');
        return $timer;
    }

    //it would give weekly time
    public function getWeeklyTimeAttribute()
    {
        $timer = Timer::where('user_id', auth()->user()->id)->whereBetween('started_at', [Carbon::now()->startOfWeek()->subDay()->toDateString(), Carbon::today()->addDay()->toDateString()])
            ->sum('total_time');
        if (auth()->user()->role == 0) {

            $id = Timer::pluck('user_id');
            // $user = User::find($id)->pluck('id');

            $timer = Timer::where('user_id', )->whereBetween('started_at', [Carbon::now()->startOfWeek()->subDay()->toDateString(), Carbon::today()->addDay()->toDateString()])
                ->sum('total_time');
            return $timer;
        }
        return $timer;
    }

    //it would give monthly time
    public function getMonthlyTimeAttribute()
    {
        $timer = Timer::where('user_id', auth()->user()->id)->whereBetween('started_at', [Carbon::now()->startOfMonth()->subDay()->toDateString(), Carbon::today()->addDay()->toDateString()])
            ->sum('total_time');
        if (auth()->user()->role == 0) {
            $timer = Timer::join('users', 'users.id', '=', 'timers.user_id')->whereBetween('started_at', [Carbon::now()->startOfMonth()->subDay()->toDateString(), Carbon::today()->addDay()->toDateString()])
                ->sum('total_time');
            return $timer;
        }
        return $timer;
    }
}
