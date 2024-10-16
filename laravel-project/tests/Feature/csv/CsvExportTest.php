<?php

namespace Tests\Feature\csv;

use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

        $attendance = collect([
            [
                'working_day' => '2024-04-01',
                'start_time' => Carbon::parse('2024-04-01 13:00'),
                'finish_time' => Carbon::parse('2024-04-01 18:00'),
            ],
            [
                'working_day' => '2024-04-02',
                'start_time' => Carbon::parse('2024-04-02 13:25'),
                'finish_time' => Carbon::parse('2024-04-02 18:10'),
            ],
            [
                'working_day' => '2024-04-04',
                'start_time' => Carbon::parse('2024-04-04 12:50'),
                'finish_time' => Carbon::parse('2024-04-04 17:40'),
            ],
        ]);

        $attendance->each(function ($day) {
            Attendance::factory()->for($this->user)->state($day)->create();
        });
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

        $filename = '2024年4月分職員勤務実績記録票（'.$this->user->name.'）.csv';
        $response->assertDownload($filename);

        $csvContent = $response->getContent();
        $rows = [
            "2024,4,\"{$this->user->name}\",14:00",
            '日付,出勤時間,退勤時間,勤務時間',
            '1,13:00,18:00,05:00',
            '2,13:30,18:00,04:30',
            '3,,,',
            '4,13:00,17:30,04:30',
        ];
        foreach (range(5, 31) as $i) {
            $rows[] = "{$i},,,";
        }
        $expectedContent = implode("\r\n", $rows)."\r\n";
        $expectedContent = mb_convert_encoding($expectedContent, 'SJIS-win', 'UTF-8');
        $this->assertEquals($expectedContent, $csvContent);
    }
}
