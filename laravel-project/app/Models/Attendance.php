<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 打刻丸め
     * 45～14分→0分
     * 15～44分→30分
     *
     * @return Carbon $roundTime | null
     */
    protected static function timeRounding(?Carbon $stampTime)
    {
        if (is_null($stampTime)) {
            return null;
        }

        if ($stampTime->minute >= 45) {
            $roundTime = $stampTime->addHours(1)->minute(0)->second(0);
        } elseif ($stampTime->minute < 15) {
            $roundTime = $stampTime->minute(0)->second(0);
        } else {
            $roundTime = $stampTime->minute(30)->second(0);
        }

        return $roundTime;
    }

    public function roundStartTime(): Attribute
    {
        return new Attribute(
            get: fn () => self::timeRounding($this->start_time)
        );
    }

    public function roundFinishTime(): Attribute
    {
        return new Attribute(
            get: fn () => self::timeRounding($this->finish_time)
        );
    }

    public function workingTime(): Attribute
    {
        return new Attribute(
            get: fn () => ! is_null($this->round_finish_time) ? $this->round_start_time->diffInMinutes($this->round_finish_time) : null
        );
    }
}
