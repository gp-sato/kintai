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
        $times = [
            '13:30:00',
            '14:00:00',
            '14:30:00',
            '15:00:00',
            '15:30:00',
            '16:00:00',
            '16:30:00',
            '17:00:00',
            '17:30:00',
        ];

        $users = User::where('is_admin', 0)->get();
        $today = Carbon::today();

        foreach ($users as $user) {
            $day = Carbon::today()->addYears(-1);
            while ($day->diffInDays($today) > 0) {
                $i = rand(0, 10);
                if ($i <= 7):
                    Attendance::factory()->for($user)->create([
                        'working_day' => $day->copy()->format('Y-m-d'),
                    ]);
                elseif ($i === 8):
                    Attendance::factory()->for($user)->create([
                        'working_day' => $day->copy()->format('Y-m-d'),
                        'start_time' => Carbon::createFromTimeString($times[array_rand($times, 1)]),
                    ]);
                elseif ($i === 9):
                    Attendance::factory()->for($user)->create([
                        'working_day' => $day->copy()->format('Y-m-d'),
                        'finish_time' => Carbon::createFromTimeString($times[array_rand($times, 1)]),
                    ]);
                endif;

                $day->addDays(1);
            }
        }
    }
}
