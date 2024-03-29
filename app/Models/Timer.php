<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Timer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'stopped_at',
        'time_diff',
        'captured_at',       
        'screenshot',
        'task_id',
    ];

 

    public function user()
    {
        return  $this->belongsTo(User::class);
    }
    public function task()
    {
        return  $this->belongsTo(Task::class);
    }

    protected $dates = [
        'captured_at'
    ];
}
