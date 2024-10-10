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

        foreach (range(1, 10) as $d) {
            $date = Carbon::create(2024, 4, $d);
            Attendance::factory()->for($this->user)->generateRandomTimesForDate($date)->create();
        }

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
    }
}
