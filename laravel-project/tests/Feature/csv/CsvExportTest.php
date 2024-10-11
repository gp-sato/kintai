<?php

namespace Tests\Feature\csv;

use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CsvExportTest extends TestCase
{
    use RefreshDatabase;

    public $admin;
    public $user;

    public function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow(new Carbon('2024-05-15 10:00:00'));

        $this->admin = User::factory()->state(['is_admin' => 1])->create();

        $this->user = User::factory()->state(['is_admin' => 0])->create();

        $attendance = new Attendance();
        $attendance->user_id = $this->user->id;
        $attendance->working_day = '2024-04-01';
        $attendance->start_time = Carbon::create(2024, 4, 1, 13);
        $attendance->finish_time = Carbon::create(2024, 4, 1, 18);
        $attendance->save();

        $attendance = new Attendance();
        $attendance->user_id = $this->user->id;
        $attendance->working_day = '2024-04-02';
        $attendance->start_time = Carbon::create(2024, 4, 2, 13, 25);
        $attendance->finish_time = Carbon::create(2024, 4, 2, 18, 10);
        $attendance->save();

        $attendance = new Attendance();
        $attendance->user_id = $this->user->id;
        $attendance->working_day = '2024-04-04';
        $attendance->start_time = Carbon::create(2024, 4, 4, 12, 50);
        $attendance->finish_time = Carbon::create(2024, 4, 4, 17, 40);
        $attendance->save();
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    public function test_CSV画面表示_正常(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get('/admin/csv');

        $response->assertStatus(200);
    }

    public function test_CSV画面表示_一般ユーザー(): void
    {
        $this->actingAs($this->user);

        $response = $this->get('/admin/csv');

        $response->assertStatus(403);
    }

    public function test_CSV画面表示_未ログイン(): void
    {
        $response = $this->get('/admin/csv');

        $response->assertRedirect('/login');
    }

    public function test_CSVエクスポート_成功(): void
    {
        $param = [
            'download_user_id' => $this->user->id,
            'year' => 2024,
            'month' => 4,
        ];

        $this->actingAs($this->admin);

        $response = $this->get(route('admin.csv.download', $param));

        $response->assertSessionHasNoErrors();

        $filename = "2024年4月分職員勤務実績記録票（" . $this->user->name . "）.csv";
        $response->assertDownload($filename);

        $csvContent = $response->getContent();
        $expectedContent = "2024,4,\"{$this->user->name}\",14:00\r\n日付,出勤時間,退勤時間,勤務時間\r\n1,13:00,18:00,05:00\r\n2,13:30,18:00,04:30\r\n3,,,\r\n4,13:00,17:30,04:30\r\n";
        foreach (range(5, 31) as $i) {
            $expectedContent .= "{$i},,,\r\n";
        }
        $expectedContent = mb_convert_encoding($expectedContent, 'SJIS-win', 'UTF-8');
        $this->assertEquals($expectedContent, $csvContent);
    }
}
