<?php

namespace App\Http\Controllers;

use App\Models\Timer;
use App\Models\User;
use App\Providers\TimerService;
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
$messege='stored_timer';
return response()->json([
'Messege'=>$messege,
 'data'=>$data,
'status'=>200,
]);
}
    //get daily data of user
public function show(TimerService $service)
{
            /*//daily time 
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
            ];*/
            
$response=$service->dailydataRecord();

return response()->json([
'data'=>$response,
'status'=> 200,]);
 } 

    //get all users
public function alldata(TimerService $service)
{
       // if (auth()->user()->role == 0) 
{
                /*$data = User::select('id', 'name', 'email')
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
                    ->get();*/
$response=$service->getallUser();
 //expected response
return response()->json([
'data'=>$response,
'status'=> 200]);
} /*else {
            return response()->json(['message' => 'Unauthorized'], 403);
        }*/
}

    //get data of requested date,  screenshots and its captured date
public function view($date, $id)
 {
if (auth()->user()->role == 0) {
$data = Timer::where('user_id', $id)->whereDate('captured_at', $date)->select('screenshot', 'captured_at')->get();
//expected response
  
return response()->json([
'data'=>$data, 
 'status'=>200,]);
} 
else {
return response()->json(['message' => 'Unauthorized'], 403);
}
}
}
