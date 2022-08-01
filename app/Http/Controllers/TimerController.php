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
            $this->validate($request, [
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

            $timer = Timer::where('user_id', $id)->latest()->pluck('started_at')->first();

            if ($timer != NULL || $timer != '' || $timer != []) {
                $time = $timer->diffInSeconds($data->started_at);
                $data->total_time = $time;
            } else {
                $data->total_time = 0;
            }


            $daily_time = Timer::where('user_id', $id)
                ->whereDate('started_at', Carbon::today()->toDateString())
                ->sum('total_time');

            $data->daily_time = $daily_time + $data->total_time;



            $weekly_time = Timer::where('user_id', $id)
                ->whereBetween('started_at', [Carbon::now()->startOfWeek()->subDay()->toDateString(), Carbon::today()->addDay()->toDateString()])
                ->sum('total_time');

            $data->weekly_time = $weekly_time + $data->total_time;

            $monthly_time = Timer::where('user_id', $id)
                ->whereBetween('started_at', [Carbon::now()->startOfMonth()->subDay()->toDateString(), Carbon::today()->addDay()->toDateString()])
                ->sum('total_time');

            $data->monthly_time = $monthly_time + $data->total_time;


            $data->captured_at = Carbon::parse(Str::substr($request['captured_at'], 0, 33));

            $data->save();
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
        //expected response
        return response()->json([$data], 200);
    }

    //get daily data of user
    public function show($id)
    {
        try {
            $data = Timer::where('user_id', $id)
                ->whereDate('started_at', Carbon::now()->toDateString())
                ->select('id', 'daily_time', 'weekly_time', 'monthly_time')->latest()->get();
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
        //if there is no data for today(present day) return previous time
        if ($data->isEmpty()) {
            $time = Timer::where('user_id', $id)->select('weekly_time', 'monthly_time')->orderby('id', 'desc')->first();
            return response()->json($time);
        }

        //it will return the selected data with accessors created in Timer Model
        return response()->json($data, 200);
    }
    //get data of requested date,  screenshots and its captured date
    public function view(Request $request, $date, $id)
    {
        if (auth()->user()->role == 0) {
            try {
                // $ip = $request->ip();
                // // $ip = "136.22.83.240"; //$_SERVER['REMOTE_ADDR'];
                // $ipInfo = file_get_contents('http://ip-api.com/json/' . $ip);
                // $ipInfo = json_decode($ipInfo);
                // $timezone = $ipInfo->timezone;
                // date_default_timezone_set($timezone);
                // date_default_timezone_get();

                $data = Timer::where('user_id', $id)->whereDate('captured_at', $date)->select('image', 'captured_at')->get();
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
            //get screenshots with specific date
            return response()->json($data, 200);
        } else {
            $message = 'Unauthorized';
            return response()->json($message, 403);
        }
    }
    //get all users with their weekly
    public function alldata()
    {
        if (auth()->user()->role == 0) {
            try {

                $data = User::select('id', 'name', 'email')->with(['last_timer' => function ($query) {
                    $query->select('user_id', 'daily_time', 'weekly_time', 'monthly_time');
                }])->get();
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
            //get all users
            return response()->json($data, 200);
        } else {
            $message = 'Unauthorized';
            return response()->json($message, 403);
        }
    }
}
