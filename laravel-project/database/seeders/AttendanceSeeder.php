<?php

namespace Database\Seeders;

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
        // 今日、出勤した場合
        \App\Models\Attendance::factory()->create([
            'user_id' => 2,
            'finish_time' => null,
        ]);

        // 昨日、出勤した場合
        \App\Models\Attendance::factory()->create([
            'user_id' => 2,
            'working_day' => Carbon::yesterday()->format('Y-m-d'),
        ]);

        // 今日、遅刻した場合
        \App\Models\Attendance::factory()->create([
            'user_id' => 3,
            'start_time' => Carbon::createFromTimeString('13:30:00'),
            'finish_time' => null,
        ]);

        \App\Models\Attendance::factory()->create([
            'user_id' => 3,
            'working_day' => Carbon::yesterday()->format('Y-m-d'),
        ]);

        \App\Models\Attendance::factory()->create([
            'user_id' => 4,
            'finish_time' => null,
        ]);

        // 昨日、早退した場合
        \App\Models\Attendance::factory()->create([
            'user_id' => 4,
            'working_day' => Carbon::yesterday()->format('Y-m-d'),
            'finish_time' => Carbon::createFromTimeString('15:30:00'),
        ]);

        // 昨日、欠勤した人
        \App\Models\Attendance::factory()->create([
            'user_id' => 5,
            'finish_time' => null,
        ]);

        // 今日、まだ出勤していない人
        \App\Models\Attendance::factory()->create([
            'user_id' => 6,
            'working_day' => Carbon::yesterday()->format('Y-m-d'),
        ]);
    }
}
