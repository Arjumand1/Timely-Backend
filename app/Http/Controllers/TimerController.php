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
                'started_at' => 'required'
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
            $time = $data->started_at->diffInSeconds($data->stopped_at);
            $data->total_time = $time;

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
        return response()->json([$data]);
    }

    //this method will get timer info e.g daily time,weekly time, monthly time,screenshot image
    public function show($id)
    {
        try {

            $data = Timer::where('user_id', $id)
                ->whereDate('started_at', Carbon::now()->toDateString())
                ->select('id', 'image', 'started_at', 'stopped_at', 'total_time')->get();
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
            $time = Timer::where('user_id', $id)->select('total_time')->orderby('id', 'desc')->first();
            return response()->json($time);
        }

        //it will return the selected data with accessors created in Timer Model
        return response()->json($data);
    }

    public function view($date)
    {
        try {
            $data = Timer::whereDate('created_at', $date)->select('image', 'created_at')->get();
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
        return response()->json([$data]);
    }
    public function alldata()
    {
        try {
            $data = User::select('id', 'name', 'email')->with('last_timer')->get();
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
        return response()->json([$data]);
    }
}
