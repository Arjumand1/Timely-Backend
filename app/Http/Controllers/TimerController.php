<?php

namespace App\Http\Controllers;

use App\Models\Timer;
use App\Models\User;
use Illuminate\Http\Request;
use App\Repos\ImageRepository;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class TimerController extends Controller
{
    //this method will store timer data with screenshot
    public function store(Request $request, $id)
    {
      try{
        //validate the request
        $this->validate($request, [
            'image' => 'required',
            'started_at' => 'required'
        ]);

        $attachment_url = (new ImageRepository)
            ->upload_image($request->input('banner_image'), null);
//create timer
        $data = new Timer;
        $data->user_id =$id;
        $data->image=$attachment_url;
        $data->started_at = $request['started_at'];
        $data->stopped_at = $request['stopped_at'];
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
        try{
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
        //it will return the selected data with accessors created in Timer Model
        return response()->json([$data]);
    }

    public function view()
    {
        $fetches = Timer::all();
        return view('image',['fetches'=>$fetches]);
    }
}
