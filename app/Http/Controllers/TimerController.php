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
                'screenshot' => 'required',
                'time_diff' => 'required',
                'captured_at' => 'required'
            ]);

            //create timer
            $data = new Timer;
            $data->user_id = $id;

            //base64 string to image conversion
            preg_match("/data:image\/(.*?);/", $request->screenshot, $image_extension); // extract the image extension
            $image = preg_replace('/data:image\/(.*?);base64,/', '', $request->screenshot); // remove the type part
            $image = str_replace(' ', '+', $image);
            $imageName = 'image_' . Str::random(20) . '.' . @$image_extension[1]; //generating unique file name;
            $data->screenshot = $imageName;
            Storage::disk('public')->put($imageName, base64_decode($image)); // image base64 encoded


            //time difference
            $data->time_diff = $request->time_diff;

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
            //daily time 
            $daily_time = Timer::where('user_id', $id)->whereDate('captured_at', Carbon::now()->toDateString())->sum('time_diff');
            //weekly time 
            $weekly_time = Timer::where('user_id', $id)
                ->whereBetween('captured_at', [Carbon::now()->startOfWeek()->subDay()->toDateString(), Carbon::today()->addDay()->toDateString()])
                ->sum('time_diff');
            //month time 
            $monthly_time = Timer::where('user_id', $id)
                ->whereBetween('captured_at', [Carbon::now()->startOfMonth()->subDay()->toDateString(), Carbon::today()->addDay()->toDateString()])
                ->sum('time_diff');
            //response 
            $response = [
                'daily_time' => $daily_time,
                'weekly_time' => $weekly_time,
                'monthly_time' => $monthly_time
            ];

            return response()->json($response, 200);
            
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

    //get all users
    public function alldata()
    {
        if (auth()->user()->role == 0) {
            try {
                $data = User::select('id', 'name', 'email')->get();

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

    //get data of requested date,  screenshots and its captured date
    public function view($date, $id)
    {
        if (auth()->user()->role == 0) {
            try {
                $data = Timer::where('user_id', $id)->whereDate('captured_at', $date)->select('screenshot', 'captured_at')->get();
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
