<?php

namespace Database\Factories;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Attendance>
 */
class AttendanceFactory extends Factory
{
    protected $model = Attendance::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'working_day' => today()->format('Y-m-d'),
            'start_time' => Carbon::createFromTimeString('13:00:00'),
            'finish_time' => Carbon::createFromTimeString('18:00:00'),
        ];
    }

    /**
     * 出退勤時間を生成してstateで返すメソッド
     *
     * @param Carbon $working_day
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function generateRandomTimesForDate($working_day)
    {
        $hours = [
            1300,
            1330,
            1400,
            1430,
            1500,
            1530,
            1600,
            1630,
            1700,
            1730,
            1800,
        ];

        $hourKeys = array_rand($hours, 2);

        $start = $hours[$hourKeys[0]];
        $finish = $hours[$hourKeys[1]];

        return $this->state([
            'working_day' => $working_day,
            'start_time' => Carbon::parse($working_day)->setTime($start / 100, $start % 100),
            'finish_time' => Carbon::parse($working_day)->setTime($finish / 100, $finish % 100),
        ]);
    }
}
