<?php

namespace App\Http\Controllers;

use App\Models\Timer;
use App\Models\User;
use Illuminate\Http\Request;
use App\Repos\ImageRepository;
use Carbon\Carbon;

class TimerController extends Controller
{
    //this method will store timer data with screenshot
    public function store(Request $request, $id)
    {

        $this->validate($request, [
            'image' => 'required',
            'started_at' => 'required'
        ]);

        $user = User::find($id);

        //using ImageRepository to convert base64 string
        $attachment_url = (new ImageRepository)
            ->upload_image($request->input('banner_image'), null);

        $data = new Timer;
        $data->user_id = $id;
        $data->image = $attachment_url;
        $data->started_at = $request['started_at'];
        $data->stopped_at = $request['stopped_at'];
        $time = $data->started_at->diffInSeconds($data->stopped_at);
        $data->total_time = $time;
        $data->save();

        return response()->json([$data]);
    }

    //this method will get timer info e.g daily time,weekly time, monthly time,screenshot image
    public function show($id)
    {
        $data = Timer::where('user_id', $id)
            ->whereDate('started_at', Carbon::now()->toDateString())
            ->select('id', 'image', 'started_at', 'stopped_at', 'total_time')->get();
        return response()->json([$data]);
    }
}
