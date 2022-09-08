<?php

namespace App\Http\Controllers;

use App\Models\Timer;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Exception;

class TimerController extends Controller
{
    //this method will store timer data with screenshot
    public function store(Request $request, $id)
    {
        try {
            //validate the request
            $request->validate([
                'image' => 'required',
                'started_at' => 'required',
                'captured_at' => 'required'
            ]);

            //create timer
            $data = new Timer;
            $data->user_id = $id;

            //base64 string to image conversion
            preg_match("/data:image\/(.*?);/", $request->image, $image_extension); // extract the image extension
            $image = preg_replace('/data:image\/(.*?);base64,/', '', $request->image); // remove the type part
            $image = str_replace(' ', '+', $image);
            $imageName = 'image_' . Str::random(20) . '.' . @$image_extension[1]; //generating unique file name;
            $data->image = $imageName;
            Storage::disk('public')->put($imageName, base64_decode($image)); // image base64 encoded

            $data->started_at = $request['started_at'];
            $data->stopped_at = '00:00:00';

            //total time
            $timer = Timer::where('user_id', $id)
                ->whereDate('started_at', Carbon::today()->toDateString())
                ->latest()->pluck('started_at')->first();
            if ($timer != NULL || $timer != '' || $timer != []) {
                $data->started_at == $data->stopped_at
                    ?  $data->total_time = 0
                    :  $time = $timer->diffInSeconds($data->started_at);
                $data->total_time = $time;
            } else {
                $data->total_time = 0;
            }

            //daily time
            $daily_time = Timer::where('user_id', $id)
                ->whereDate('started_at', Carbon::today()->toDateString())
                ->sum('total_time');
            $data->daily_time = $daily_time + $data->total_time;

            //weekly time
            $weekly_time = Timer::where('user_id', $id)
                ->whereBetween(
                    'started_at',
                    [Carbon::now()->startOfWeek()->subDay()->toDateString(), Carbon::today()->addDay()->toDateString()]
                )
                ->sum('total_time');
            $data->weekly_time = $weekly_time + $data->total_time;

            //monthly time
            $monthly_time = Timer::where('user_id', $id)
                ->whereBetween(
                    'started_at',
                    [Carbon::now()->startOfMonth()->subDay()->toDateString(), Carbon::today()->addDay()->toDateString()]
                )
                ->sum('total_time');
            $data->monthly_time = $monthly_time + $data->total_time;

            //ScreenShot Captured Date
            $data->captured_at = Carbon::parse(Str::substr($request['captured_at'], 0, 33));

            $data->save();
            //expected response
            return response()->json([$data], 200);
        } catch (Exception $e) {
            //throw execption
            $message = $e->getMessage();
            var_dump('Exception Message: ' . $message);

            $code = $e->getCode();
            var_dump('Exception Code: ' . $code);

            $string = $e->__toString();
            var_dump('Exception String: ' . $string);

            exit;
        }
    }

    //get daily data of user
    public function show($id)
    {
        try {            
            $data = Timer::where('user_id', $id)
                ->whereDate('started_at', Carbon::now()->toDateString())
                ->select('id', 'daily_time', 'weekly_time', 'monthly_time')->latest()->get();
            if ($data->isEmpty()) {

                $time = Timer::where('user_id', $id)
                    ->select('daily_time', 'weekly_time', 'monthly_time')
                    ->orderby('id', 'desc')->first();
                if ($time->daily_time > 0) {
                    $time->daily_time = 0;
                };
                return response()->json($time, 200);
            }

            //expected response
            return response()->json($data, 200);
        } catch (Exception $e) {
            //throw exeption
            $message = $e->getMessage();
            var_dump('Exception Message: ' . $message);

            $code = $e->getCode();
            var_dump('Exception Code: ' . $code);

            $string = $e->__toString();
            var_dump('Exception String: ' . $string);

            exit;
        }
    }
    //get data of requested date,  screenshots and its captured date
    public function view($date, $id)
    {
        if (auth()->user()->role == 0) {
            try {
                $data = Timer::where('user_id', $id)->whereDate('captured_at', $date)->select('image', 'captured_at')->get();
                //expected response
                return response()->json($data, 200);
            } catch (Exception $e) {
                //throw exeption
                $message = $e->getMessage();
                var_dump('Exception Message: ' . $message);

                $code = $e->getCode();
                var_dump('Exception Code: ' . $code);

                $string = $e->__toString();
                var_dump('Exception String: ' . $string);

                exit;
            }
        } else {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
    }
    //get all users
    public function alldata()
    {
        if (auth()->user()->role == 0) {
            try {
                $data = User::select('id', 'name', 'email')
                    ->with(['last_timer' => function ($query) {
                        $query->select('user_id', 'daily_time', 'weekly_time', 'monthly_time');
                    }])->get();

                //expected response
                return response()->json($data, 200);
            } catch (Exception $e) {
                //throw exeption
                $message = $e->getMessage();
                var_dump('Exception Message: ' . $message);

                $code = $e->getCode();
                var_dump('Exception Code: ' . $code);

                $string = $e->__toString();
                var_dump('Exception String: ' . $string);

                exit;
            }
        } else {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
    }
}
