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
        $users = User::where('is_admin', 0)->get();
        $today = Carbon::today();

        foreach ($users as $user) {
            $day = Carbon::today()->addYears(-1);
            while ($day->diffInDays($today) > 0) {
                $i = rand(0, 5);
                if ($i !== 5) {
                    Attendance::factory()->for($user)->generateRandomTimesForDate($day)->create();
                }
                $day->addDays(1);
            }
        }
    }
}
