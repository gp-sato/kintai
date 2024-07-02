<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Attendance extends Model
{
    use HasFactory;

    protected $table = 'attendance';

    protected $fillable = [
        'start_time',
        'finish_time',
    ];

    protected $casts = [
        'start_time' => 'datetime:H:i',
        'finish_time' => 'datetime:H:i',
    ];

    public function user() {
        return $this->belongsTo(user::class);
    }
}
