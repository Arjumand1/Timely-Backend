<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Timer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'started_at',
        'stopped_at',
        'image',
    ];

    protected $casts = [

        'started_at'=>'datetime',
        'stopped_at'=>'datetime',

    ];

     public function users()
     {
        $this->belongsTo(User::class);
     }
}
