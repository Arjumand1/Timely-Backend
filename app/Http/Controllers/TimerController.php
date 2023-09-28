<?php

namespace App\Http\Controllers;

use App\Models\Timer;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Exception;
use Ramsey\Uuid\Type\Integer;

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
               'captured_at' => 'required',
                'task_id'=>'required'
            ]);

            //create timer
            $data = new Timer;
            $data->user_id = auth()->user()->id;
            $data->task_id = $request->task_id;
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
            $data->captured_at = $request->captured_at;

            $data->save();
            //expected response
            $messege=trans('messege.store_timer');
            return response()->json([
                'Messege'=>$message,
                'data'=>$data,
                 'status'=>200,
            ]);
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
    public function show()
    {
        try {
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
            $messege=trans('messge.get_timer');
            $response = [
                'daily_time' => $day_time,
                'weekly_time' => $week_time,
                'monthly_time' => $month_time
            ];



            return response()->json([
                'Messege'=>$message,
                'data'=>$response,
                'status'=> 200,]);
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
                $message=('messege.get_user');
                return response()->json([
                    'Messege'=>$message,
                    'data'=>$data, 
                    'status'=>200,]);
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
