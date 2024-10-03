<?php

namespace Tests\Feature\csv;

use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class CsvImportTest extends TestCase
{
    use RefreshDatabase;

    public $admin;
    public $user;
    public $file;

    public function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow(new Carbon('2024-05-15 10:00:00'));

        $this->admin = User::factory()->state(['is_admin' => 1])->create();

        $this->user = User::factory()->state(['is_admin' => 0])->create();

        $content = <<<EOF
        2024,2
        日付,出勤時間,退勤時間
        1,13:00,18:00
        2,13:00,18:00
        3,,
        4,13:00,18:00
        EOF;

        $this->file = UploadedFile::fake()->createWithContent('importCsv.csv', $content);
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

    public function test_CSVインポート_成功(): void
    {
        $this->actingAs($this->admin);

        $response = $this->from('/admin/csv')
                        ->post('/admin/csv', [
                            'user_id' => $this->user->id,
                            'csv_file' => $this->file,
                        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect('/admin/csv');

        $attendance = Attendance::where('user_id', $this->user->id)
                        ->orderBy('working_day', 'ASC')
                        ->get();

        $this->assertEquals('2024-02-01', $attendance[0]->working_day);
        $this->assertEquals(new Carbon('2024-02-01 13:00:00'), $attendance[0]->start_time);
        $this->assertEquals(new Carbon('2024-02-01 18:00:00'), $attendance[0]->finish_time);

        $this->assertEquals('2024-02-02', $attendance[1]->working_day);
        $this->assertEquals(new Carbon('2024-02-02 13:00:00'), $attendance[1]->start_time);
        $this->assertEquals(new Carbon('2024-02-02 18:00:00'), $attendance[1]->finish_time);

        $this->assertEquals('2024-02-04', $attendance[2]->working_day);
        $this->assertEquals(new Carbon('2024-02-04 13:00:00'), $attendance[2]->start_time);
        $this->assertEquals(new Carbon('2024-02-04 18:00:00'), $attendance[2]->finish_time);
    }
}
