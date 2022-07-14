<?php

namespace App\Http\Controllers;

use App\Models\Timer;
use App\Models\User;
use Illuminate\Http\Request;
use App\Repos\ImageRepository;
use Carbon\Carbon;

class TimerController extends Controller
{
    public function store(Request $request, $id)
    {

        $this->validate($request, [

            'image' => 'required',

            'started_at' => 'required'

        ]);


        $user = User::find($id);

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

    public function show($id)
    {
        $data = Timer::where('user_id', $id)
            ->whereDate('started_at', Carbon::now()->toDateString())
            ->select('id', 'image', 'started_at', 'stopped_at', 'total_time')->get();
        return response()->json([$data]);
    }
}
