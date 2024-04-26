<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class AttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $hours = [13, 14, 15, 16, 17];
        $minutes = [00, 30];

        $users = User::where('is_admin', 0)->get();
        $today = Carbon::today();

        foreach ($users as $user) {
            $day = Carbon::today()->addYears(-1);
            while ($day->diffInDays($today) > 0) {
                $i = rand(0, 10);
                if ($i <= 7):
                    Attendance::factory()->for($user)->create([
                        'working_day' => $day->copy()->format('Y-m-d'),
                        'start_time' => Carbon::parse($day)->setTime(13, 00, 00),
                        'finish_time' => Carbon::parse($day)->setTime(18, 00, 00),
                    ]);
                elseif ($i === 8):
                    Attendance::factory()->for($user)->create([
                        'working_day' => $day->copy()->format('Y-m-d'),
                        'start_time' => Carbon::parse($day)->setTime($hours[array_rand($hours, 1)], $minutes[array_rand($minutes, 1)], 00),
                        'finish_time' => Carbon::parse($day)->setTime(18, 00, 00),
                    ]);
                elseif ($i === 9):
                    $hour = $hours[array_rand($hours, 1)];
                    $minute = $hour === 13 ? 30 : $minutes[array_rand($minutes, 1)];
                    Attendance::factory()->for($user)->create([
                        'working_day' => $day->copy()->format('Y-m-d'),
                        'start_time' => Carbon::parse($day)->setTime(13, 00, 00),
                        'finish_time' => Carbon::parse($day)->setTime($hour, $minute, 00),
                    ]);
                endif;

                $day->addDays(1);
            }
        }
    }
}
