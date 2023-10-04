<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\{Timer,User};

class TimerService extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
    public function dailydataRecord()
    {
         //daily time 
         $daily_time = Timer::where('user_id', auth()->user()->id)->whereDate('captured_at', Carbon::now()->toDateString())->sum('time_diff');
         //weekly time 
         $weekly_time = Timer::where('user_id', auth()->user()->id)
             ->whereBetween('captured_at', [Carbon::now()->startOfWeek()->subDay()->toDateString(), Carbon::today()->addDay()->toDateString()])
             ->sum('time_diff');
         //month time 
         $monthly_time = Timer::where('user_id', auth()->user()->id)
             ->whereBetween('captured_at', [Carbon::now()->startOfMonth()->subDay()->toDateString(), Carbon::today()->addDay()->toDateString()])
             ->sum('time_diff');

         $day_time = (int)$daily_time;
         $week_time = (int)$weekly_time;
         $month_time = (int)$monthly_time;

         //response 
         $messege=trans('message.get_timer');
         $response = [
             'daily_time' => $day_time,
             'weekly_time' => $week_time,
             'monthly_time' => $month_time
         ];

    }
    public function getallUser(){
        if (auth()->user()->role == 0) {
                $data = User::select('id', 'name', 'email')
                    //daily time 
                    ->withSum(['timers as daily_time' => function ($query) {
                        $query->whereDate('captured_at', Carbon::now()->toDateString());
                    }], 'time_diff')
                    //weekly time
                    ->withSum(['timers as weekly_time' => function ($query) {
                        $query->whereBetween('captured_at', [Carbon::now()->startOfWeek()->subDay()->toDateString(), Carbon::today()->addDay()->toDateString()]);
                    }], 'time_diff')
                    //monthly time
                    ->withSum(['timers as monthly_time' => function ($query) {
                        $query->whereBetween('captured_at', [Carbon::now()->startOfMonth()->subDay()->toDateString(), Carbon::today()->addDay()->toDateString()]);
                    }], 'time_diff')
                    ->get();
    }else {
        ['response' => 'Unauthorized'];
    }
}
    
}