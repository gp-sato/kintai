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

    public function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow(new Carbon('2024-04-15 10:00:00'));

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

    public function test_CSVインポート_成功(): void
    {
        $content = <<<EOF
        2024,4
        日付,出勤時間,退勤時間
        1,13:00,18:00
        2,13:00,18:00
        3,,
        15,13:00,18:00
        16,,
        EOF;

        $file = UploadedFile::fake()->createWithContent('importCsv.csv', $content);

        $this->actingAs($this->admin);

        $response = $this->from('/admin/csv')
                        ->post('/admin/csv', [
                            'user_id' => $this->user->id,
                            'csv_file' => $file,
                        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect('/admin/csv');

        $attendance = Attendance::where('user_id', $this->user->id)
                        ->whereYear('working_day', 2024)
                        ->whereMonth('working_day', 4)
                        ->orderBy('working_day', 'ASC')
                        ->get();

        $this->assertCount(3, $attendance);

        $this->assertEquals('2024-04-01', $attendance[0]->working_day);
        $this->assertEquals(new Carbon('2024-04-01 13:00:00'), $attendance[0]->start_time);
        $this->assertEquals(new Carbon('2024-04-01 18:00:00'), $attendance[0]->finish_time);

        $this->assertEquals('2024-04-02', $attendance[1]->working_day);
        $this->assertEquals(new Carbon('2024-04-02 13:00:00'), $attendance[1]->start_time);
        $this->assertEquals(new Carbon('2024-04-02 18:00:00'), $attendance[1]->finish_time);

        $this->assertEquals('2024-04-15', $attendance[2]->working_day);
        $this->assertEquals(new Carbon('2024-04-15 13:00:00'), $attendance[2]->start_time);
        $this->assertEquals(new Carbon('2024-04-15 18:00:00'), $attendance[2]->finish_time);
    }

    public function test_エラー_年が指定されていない(): void
    {
        $content = <<<EOF
        ,4
        日付,出勤時間,退勤時間
        1,13:00,18:00
        2,13:00,18:00
        3,,
        4,13:00,18:00
        5,,
        EOF;

        $file = UploadedFile::fake()->createWithContent('importCsv.csv', $content);

        $this->actingAs($this->admin);

        $response = $this->from('/admin/csv')
                        ->post('/admin/csv', [
                            'user_id' => $this->user->id,
                            'csv_file' => $file,
                        ]);

        $response->assertSessionHas('error');
        $response->assertRedirect('/admin/csv');

        $this->get('/admin/csv')->assertSee('年が指定されていません。');

        $attendance = Attendance::where('user_id', $this->user->id)
                        ->whereYear('working_day', 2024)
                        ->whereMonth('working_day', 4)
                        ->get();
        $this->assertCount(10, $attendance);
    }

    public function test_エラー_年が整数でない(): void
    {
        $content = <<<EOF
        abc,4
        日付,出勤時間,退勤時間
        1,13:00,18:00
        2,13:00,18:00
        3,,
        4,13:00,18:00
        5,,
        EOF;

        $file = UploadedFile::fake()->createWithContent('importCsv.csv', $content);

        $this->actingAs($this->admin);

        $response = $this->from('/admin/csv')
                        ->post('/admin/csv', [
                            'user_id' => $this->user->id,
                            'csv_file' => $file,
                        ]);

        $response->assertSessionHas('error');
        $response->assertRedirect('/admin/csv');

        $this->get('/admin/csv')->assertSee('年が整数ではありません。');

        $attendance = Attendance::where('user_id', $this->user->id)
                        ->whereYear('working_day', 2024)
                        ->whereMonth('working_day', 4)
                        ->get();
        $this->assertCount(10, $attendance);
    }

    public function test_エラー_年が設立より前(): void
    {
        $content = <<<EOF
        2016,4
        日付,出勤時間,退勤時間
        1,13:00,18:00
        2,13:00,18:00
        3,,
        4,13:00,18:00
        5,,
        EOF;

        $file = UploadedFile::fake()->createWithContent('importCsv.csv', $content);

        $this->actingAs($this->admin);

        $response = $this->from('/admin/csv')
                        ->post('/admin/csv', [
                            'user_id' => $this->user->id,
                            'csv_file' => $file,
                        ]);

        $response->assertSessionHas('error');
        $response->assertRedirect('/admin/csv');

        $this->get('/admin/csv')->assertSee('設立年より前を指定しています。');

        $attendance = Attendance::where('user_id', $this->user->id)
                        ->whereYear('working_day', 2024)
                        ->whereMonth('working_day', 4)
                        ->get();
        $this->assertCount(10, $attendance);
    }

    public function test_エラー_年が未来を指定している(): void
    {
        $content = <<<EOF
        2025,4
        日付,出勤時間,退勤時間
        1,13:00,18:00
        2,13:00,18:00
        3,,
        4,13:00,18:00
        5,,
        EOF;

        $file = UploadedFile::fake()->createWithContent('importCsv.csv', $content);

        $this->actingAs($this->admin);

        $response = $this->from('/admin/csv')
                        ->post('/admin/csv', [
                            'user_id' => $this->user->id,
                            'csv_file' => $file,
                        ]);

        $response->assertSessionHas('error');
        $response->assertRedirect('/admin/csv');

        $this->get('/admin/csv')->assertSee('年の指定が未来です。');

        $attendance = Attendance::where('user_id', $this->user->id)
                        ->whereYear('working_day', 2024)
                        ->whereMonth('working_day', 4)
                        ->get();
        $this->assertCount(10, $attendance);
    }

    public function test_エラー_月が指定されていない(): void
    {
        $content = <<<EOF
        2024,
        日付,出勤時間,退勤時間
        1,13:00,18:00
        2,13:00,18:00
        3,,
        4,13:00,18:00
        5,,
        EOF;

        $file = UploadedFile::fake()->createWithContent('importCsv.csv', $content);

        $this->actingAs($this->admin);

        $response = $this->from('/admin/csv')
                        ->post('/admin/csv', [
                            'user_id' => $this->user->id,
                            'csv_file' => $file,
                        ]);

        $response->assertSessionHas('error');
        $response->assertRedirect('/admin/csv');

        $this->get('/admin/csv')->assertSee('月が指定されていません。');

        $attendance = Attendance::where('user_id', $this->user->id)
                        ->whereYear('working_day', 2024)
                        ->whereMonth('working_day', 4)
                        ->get();
        $this->assertCount(10, $attendance);
    }

    public function test_エラー_月が整数でない(): void
    {
        $content = <<<EOF
        2024,def
        日付,出勤時間,退勤時間
        1,13:00,18:00
        2,13:00,18:00
        3,,
        4,13:00,18:00
        5,,
        EOF;

        $file = UploadedFile::fake()->createWithContent('importCsv.csv', $content);

        $this->actingAs($this->admin);

        $response = $this->from('/admin/csv')
                        ->post('/admin/csv', [
                            'user_id' => $this->user->id,
                            'csv_file' => $file,
                        ]);

        $response->assertSessionHas('error');
        $response->assertRedirect('/admin/csv');

        $this->get('/admin/csv')->assertSee('月が整数ではありません。');

        $attendance = Attendance::where('user_id', $this->user->id)
                        ->whereYear('working_day', 2024)
                        ->whereMonth('working_day', 4)
                        ->get();
        $this->assertCount(10, $attendance);
    }

    public function test_エラー_月数が不正(): void
    {
        $content = <<<EOF
        2024,13
        日付,出勤時間,退勤時間
        1,13:00,18:00
        2,13:00,18:00
        3,,
        4,13:00,18:00
        5,,
        EOF;

        $file = UploadedFile::fake()->createWithContent('importCsv.csv', $content);

        $this->actingAs($this->admin);

        $response = $this->from('/admin/csv')
                        ->post('/admin/csv', [
                            'user_id' => $this->user->id,
                            'csv_file' => $file,
                        ]);

        $response->assertSessionHas('error');
        $response->assertRedirect('/admin/csv');

        $this->get('/admin/csv')->assertSee('月数が不正です。');

        $attendance = Attendance::where('user_id', $this->user->id)
                        ->whereYear('working_day', 2024)
                        ->whereMonth('working_day', 4)
                        ->get();
        $this->assertCount(10, $attendance);
    }

    public function test_エラー_年月の指定が未来(): void
    {
        $content = <<<EOF
        2024,5
        日付,出勤時間,退勤時間
        1,13:00,18:00
        2,13:00,18:00
        3,,
        4,13:00,18:00
        5,,
        EOF;

        $file = UploadedFile::fake()->createWithContent('importCsv.csv', $content);

        $this->actingAs($this->admin);

        $response = $this->from('/admin/csv')
                        ->post('/admin/csv', [
                            'user_id' => $this->user->id,
                            'csv_file' => $file,
                        ]);

        $response->assertSessionHas('error');
        $response->assertRedirect('/admin/csv');

        $this->get('/admin/csv')->assertSee('年月の指定が未来です。');

        $attendance = Attendance::where('user_id', $this->user->id)
                        ->whereYear('working_day', 2024)
                        ->whereMonth('working_day', 4)
                        ->get();
        $this->assertCount(10, $attendance);
    }

    public function test_エラー_日が指定されていない(): void
    {
        $content = <<<EOF
        2024,4
        日付,出勤時間,退勤時間
        ,13:00,18:00
        2,13:00,18:00
        3,,
        4,13:00,18:00
        5,,
        EOF;

        $file = UploadedFile::fake()->createWithContent('importCsv.csv', $content);

        $this->actingAs($this->admin);

        $response = $this->from('/admin/csv')
                        ->post('/admin/csv', [
                            'user_id' => $this->user->id,
                            'csv_file' => $file,
                        ]);

        $response->assertSessionHas('error');
        $response->assertRedirect('/admin/csv');

        $this->get('/admin/csv')->assertSee("3行目：日が指定されていません。");

        $attendance = Attendance::where('user_id', $this->user->id)
                        ->whereYear('working_day', 2024)
                        ->whereMonth('working_day', 4)
                        ->get();
        $this->assertCount(10, $attendance);
    }

    public function test_エラー_日が整数でない(): void
    {
        $content = <<<EOF
        2024,4
        日付,出勤時間,退勤時間
        1,13:00,18:00
        g,13:00,18:00
        3,,
        4,13:00,18:00
        5,,
        EOF;

        $file = UploadedFile::fake()->createWithContent('importCsv.csv', $content);

        $this->actingAs($this->admin);

        $response = $this->from('/admin/csv')
                        ->post('/admin/csv', [
                            'user_id' => $this->user->id,
                            'csv_file' => $file,
                        ]);

        $response->assertSessionHas('error');
        $response->assertRedirect('/admin/csv');

        $this->get('/admin/csv')->assertSee("4行目：日が整数ではありません。");

        $attendance = Attendance::where('user_id', $this->user->id)
                        ->whereYear('working_day', 2024)
                        ->whereMonth('working_day', 4)
                        ->get();
        $this->assertCount(10, $attendance);
    }

    public function test_エラー_無効な日付(): void
    {
        $content = <<<EOF
        2024,4
        日付,出勤時間,退勤時間
        1,13:00,18:00
        2,13:00,18:00
        30,,
        31,13:00,18:00
        32,,
        EOF;

        $file = UploadedFile::fake()->createWithContent('importCsv.csv', $content);

        $this->actingAs($this->admin);

        $response = $this->from('/admin/csv')
                        ->post('/admin/csv', [
                            'user_id' => $this->user->id,
                            'csv_file' => $file,
                        ]);

        $response->assertSessionHas('error');
        $response->assertRedirect('/admin/csv');

        $this->get('/admin/csv')->assertSee("6行目：無効な日付です。");

        $attendance = Attendance::where('user_id', $this->user->id)
                        ->whereYear('working_day', 2024)
                        ->whereMonth('working_day', 4)
                        ->get();
        $this->assertCount(10, $attendance);
    }

    public function test_エラー_年月日の指定が未来(): void
    {
        $content = <<<EOF
        2024,4
        日付,出勤時間,退勤時間
        1,13:00,18:00
        2,13:00,18:00
        3,,
        16,13:00,18:00
        17,,
        EOF;

        $file = UploadedFile::fake()->createWithContent('importCsv.csv', $content);

        $this->actingAs($this->admin);

        $response = $this->from('/admin/csv')
                        ->post('/admin/csv', [
                            'user_id' => $this->user->id,
                            'csv_file' => $file,
                        ]);

        $response->assertSessionHas('error');
        $response->assertRedirect('/admin/csv');

        $this->get('/admin/csv')->assertSee("6行目：年月日の指定が未来です。");

        $attendance = Attendance::where('user_id', $this->user->id)
                        ->whereYear('working_day', 2024)
                        ->whereMonth('working_day', 4)
                        ->get();
        $this->assertCount(10, $attendance);
    }

    public function test_エラー_出勤時間が指定されていない(): void
    {
        $content = <<<EOF
        2024,4
        日付,出勤時間,退勤時間
        1,,18:00
        2,13:00,18:00
        3,,
        4,13:00,18:00
        5,,
        EOF;

        $file = UploadedFile::fake()->createWithContent('importCsv.csv', $content);

        $this->actingAs($this->admin);

        $response = $this->from('/admin/csv')
                        ->post('/admin/csv', [
                            'user_id' => $this->user->id,
                            'csv_file' => $file,
                        ]);

        $response->assertSessionHas('error');
        $response->assertRedirect('/admin/csv');

        $this->get('/admin/csv')->assertSee("3行目：出勤時間が指定されていません。");

        $attendance = Attendance::where('user_id', $this->user->id)
                        ->whereYear('working_day', 2024)
                        ->whereMonth('working_day', 4)
                        ->get();
        $this->assertCount(10, $attendance);
    }

    public function test_エラー_出勤時間の形式が正しくない(): void
    {
        $content = <<<EOF
        2024,4
        日付,出勤時間,退勤時間
        1,13:00,18:00
        2,1300,18:00
        3,,
        4,13:00,18:00
        5,,
        EOF;

        $file = UploadedFile::fake()->createWithContent('importCsv.csv', $content);

        $this->actingAs($this->admin);

        $response = $this->from('/admin/csv')
                        ->post('/admin/csv', [
                            'user_id' => $this->user->id,
                            'csv_file' => $file,
                        ]);

        $response->assertSessionHas('error');
        $response->assertRedirect('/admin/csv');

        $this->get('/admin/csv')->assertSee("4行目：出勤時間の形式が正しくありません。");

        $attendance = Attendance::where('user_id', $this->user->id)
                        ->whereYear('working_day', 2024)
                        ->whereMonth('working_day', 4)
                        ->get();
        $this->assertCount(10, $attendance);
    }

    public function test_エラー_出勤時間が整数でない(): void
    {
        $content = <<<EOF
        2024,4
        日付,出勤時間,退勤時間
        1,13:00,18:00
        2,13:00,18:00
        3,,
        4,hh:00,18:00
        5,,
        EOF;

        $file = UploadedFile::fake()->createWithContent('importCsv.csv', $content);

        $this->actingAs($this->admin);

        $response = $this->from('/admin/csv')
                        ->post('/admin/csv', [
                            'user_id' => $this->user->id,
                            'csv_file' => $file,
                        ]);

        $response->assertSessionHas('error');
        $response->assertRedirect('/admin/csv');

        $this->get('/admin/csv')->assertSee("6行目：出勤時間が整数ではありません。");

        $attendance = Attendance::where('user_id', $this->user->id)
                        ->whereYear('working_day', 2024)
                        ->whereMonth('working_day', 4)
                        ->get();
        $this->assertCount(10, $attendance);
    }

    public function test_エラー_出勤時間の時が不正(): void
    {
        $content = <<<EOF
        2024,4
        日付,出勤時間,退勤時間
        1,23:00,23:00
        2,24:00,24:00
        3,,
        4,13:00,18:00
        5,,
        EOF;

        $file = UploadedFile::fake()->createWithContent('importCsv.csv', $content);

        $this->actingAs($this->admin);

        $response = $this->from('/admin/csv')
                        ->post('/admin/csv', [
                            'user_id' => $this->user->id,
                            'csv_file' => $file,
                        ]);

        $response->assertSessionHas('error');
        $response->assertRedirect('/admin/csv');

        $this->get('/admin/csv')->assertSee("4行目：出勤時間の時が不正な値です。");

        $attendance = Attendance::where('user_id', $this->user->id)
                        ->whereYear('working_day', 2024)
                        ->whereMonth('working_day', 4)
                        ->get();
        $this->assertCount(10, $attendance);
    }

    public function test_エラー_出勤時間の分が不正(): void
    {
        $content = <<<EOF
        2024,4
        日付,出勤時間,退勤時間
        1,13:59,18:00
        2,13:60,18:00
        3,,
        4,13:00,18:00
        5,,
        EOF;

        $file = UploadedFile::fake()->createWithContent('importCsv.csv', $content);

        $this->actingAs($this->admin);

        $response = $this->from('/admin/csv')
                        ->post('/admin/csv', [
                            'user_id' => $this->user->id,
                            'csv_file' => $file,
                        ]);

        $response->assertSessionHas('error');
        $response->assertRedirect('/admin/csv');

        $this->get('/admin/csv')->assertSee("4行目：出勤時間の分が不正な値です。");

        $attendance = Attendance::where('user_id', $this->user->id)
                        ->whereYear('working_day', 2024)
                        ->whereMonth('working_day', 4)
                        ->get();
        $this->assertCount(10, $attendance);
    }

    public function test_エラー_退勤時間が指定されていない(): void
    {
        $content = <<<EOF
        2024,4
        日付,出勤時間,退勤時間
        1,13:00,
        2,13:00,18:00
        3,,
        4,13:00,18:00
        5,,
        EOF;

        $file = UploadedFile::fake()->createWithContent('importCsv.csv', $content);

        $this->actingAs($this->admin);

        $response = $this->from('/admin/csv')
                        ->post('/admin/csv', [
                            'user_id' => $this->user->id,
                            'csv_file' => $file,
                        ]);

        $response->assertSessionHas('error');
        $response->assertRedirect('/admin/csv');

        $this->get('/admin/csv')->assertSee("3行目：退勤時間が指定されていません。");

        $attendance = Attendance::where('user_id', $this->user->id)
                        ->whereYear('working_day', 2024)
                        ->whereMonth('working_day', 4)
                        ->get();
        $this->assertCount(10, $attendance);
    }

    public function test_エラー_退勤時間の形式が正しくない(): void
    {
        $content = <<<EOF
        2024,4
        日付,出勤時間,退勤時間
        1,13:00,18:00
        2,13:00,1800
        3,,
        4,13:00,18:00
        5,,
        EOF;

        $file = UploadedFile::fake()->createWithContent('importCsv.csv', $content);

        $this->actingAs($this->admin);

        $response = $this->from('/admin/csv')
                        ->post('/admin/csv', [
                            'user_id' => $this->user->id,
                            'csv_file' => $file,
                        ]);

        $response->assertSessionHas('error');
        $response->assertRedirect('/admin/csv');

        $this->get('/admin/csv')->assertSee("4行目：退勤時間の形式が正しくありません。");

        $attendance = Attendance::where('user_id', $this->user->id)
                        ->whereYear('working_day', 2024)
                        ->whereMonth('working_day', 4)
                        ->get();
        $this->assertCount(10, $attendance);
    }

    public function test_エラー_退勤時間が整数でない(): void
    {
        $content = <<<EOF
        2024,4
        日付,出勤時間,退勤時間
        1,13:00,18:00
        2,13:00,18:00
        3,,
        4,13:00,18:ii
        5,,
        EOF;

        $file = UploadedFile::fake()->createWithContent('importCsv.csv', $content);

        $this->actingAs($this->admin);

        $response = $this->from('/admin/csv')
                        ->post('/admin/csv', [
                            'user_id' => $this->user->id,
                            'csv_file' => $file,
                        ]);

        $response->assertSessionHas('error');
        $response->assertRedirect('/admin/csv');

        $this->get('/admin/csv')->assertSee("6行目：退勤時間が整数ではありません。");

        $attendance = Attendance::where('user_id', $this->user->id)
                        ->whereYear('working_day', 2024)
                        ->whereMonth('working_day', 4)
                        ->get();
        $this->assertCount(10, $attendance);
    }

    public function test_エラー_退勤時間の時が不正(): void
    {
        $content = <<<EOF
        2024,4
        日付,出勤時間,退勤時間
        1,13:00,23:00
        2,13:00,24:00
        3,,
        4,13:00,18:00
        5,,
        EOF;

        $file = UploadedFile::fake()->createWithContent('importCsv.csv', $content);

        $this->actingAs($this->admin);

        $response = $this->from('/admin/csv')
                        ->post('/admin/csv', [
                            'user_id' => $this->user->id,
                            'csv_file' => $file,
                        ]);

        $response->assertSessionHas('error');
        $response->assertRedirect('/admin/csv');

        $this->get('/admin/csv')->assertSee("4行目：退勤時間の時が不正な値です。");

        $attendance = Attendance::where('user_id', $this->user->id)
                        ->whereYear('working_day', 2024)
                        ->whereMonth('working_day', 4)
                        ->get();
        $this->assertCount(10, $attendance);
    }

    public function test_エラー_退勤時間の分が不正(): void
    {
        $content = <<<EOF
        2024,4
        日付,出勤時間,退勤時間
        1,13:00,18:59
        2,13:00,18:60
        3,,
        4,13:00,18:00
        5,,
        EOF;

        $file = UploadedFile::fake()->createWithContent('importCsv.csv', $content);

        $this->actingAs($this->admin);

        $response = $this->from('/admin/csv')
                        ->post('/admin/csv', [
                            'user_id' => $this->user->id,
                            'csv_file' => $file,
                        ]);

        $response->assertSessionHas('error');
        $response->assertRedirect('/admin/csv');

        $this->get('/admin/csv')->assertSee("4行目：退勤時間の分が不正な値です。");

        $attendance = Attendance::where('user_id', $this->user->id)
                        ->whereYear('working_day', 2024)
                        ->whereMonth('working_day', 4)
                        ->get();
        $this->assertCount(10, $attendance);
    }

    public function test_エラー_出勤時間が退勤時間よりも後(): void
    {
        $content = <<<EOF
        2024,4
        日付,出勤時間,退勤時間
        1,13:00,13:00
        2,13:00,12:59
        3,,
        4,13:00,18:00
        5,,
        EOF;

        $file = UploadedFile::fake()->createWithContent('importCsv.csv', $content);

        $this->actingAs($this->admin);

        $response = $this->from('/admin/csv')
                        ->post('/admin/csv', [
                            'user_id' => $this->user->id,
                            'csv_file' => $file,
                        ]);

        $response->assertSessionHas('error');
        $response->assertRedirect('/admin/csv');

        $this->get('/admin/csv')->assertSee("4行目：出勤時間が退勤時間よりも後になっています。");

        $attendance = Attendance::where('user_id', $this->user->id)
                        ->whereYear('working_day', 2024)
                        ->whereMonth('working_day', 4)
                        ->get();
        $this->assertCount(10, $attendance);
    }

    public function test_エラー_勤務日に重複がある(): void
    {
        $content = <<<EOF
        2024,4
        日付,出勤時間,退勤時間
        1,13:00,18:00
        1,13:00,18:00
        3,,
        4,13:00,18:00
        5,,
        EOF;

        $file = UploadedFile::fake()->createWithContent('importCsv.csv', $content);

        $this->actingAs($this->admin);

        $response = $this->from('/admin/csv')
                        ->post('/admin/csv', [
                            'user_id' => $this->user->id,
                            'csv_file' => $file,
                        ]);

        $response->assertSessionHas('error');
        $response->assertRedirect('/admin/csv');

        $this->get('/admin/csv')->assertSee('勤務日に重複があります。');

        $attendance = Attendance::where('user_id', $this->user->id)
                        ->whereYear('working_day', 2024)
                        ->whereMonth('working_day', 4)
                        ->get();
        $this->assertCount(10, $attendance);
    }
}
