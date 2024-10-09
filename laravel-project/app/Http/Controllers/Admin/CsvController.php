<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CsvDownloadRequest;
use App\Http\Requests\CsvUploadRequest;
use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class CsvController extends Controller
{
    public function index()
    {
        if (Gate::denies('admin.authority')) {
            abort(403);
        }

        $users = User::where('is_admin', 0)->get();

        return view('admin.csv.index', compact(['users']));
    }

    public function upload(CsvUploadRequest $request)
    {
        if (Gate::denies('admin.authority')) {
            abort(403);
        }

        $user_id = $request->input('user_id');

        $attendance = collect();
        $year = null;
        $month = null;

        try {
            if (!$request->hasFile('csv_file')) {
                throw new Exception('CSVファイルの取得に失敗しました。');
            }

            $file = $request->file('csv_file');
            $path = $file->getRealPath();
            $fp = fopen($path, 'r');

            $yearMonth = fgetcsv($fp);

            $year = $yearMonth[0];
            $month = $yearMonth[1];

            $this->validateYearMonth($year, $month);

            // ヘッダースキップ
            fgetcsv($fp);

            // 勤怠情報の開始行
            $i = 3;

            while (($csvData = fgetcsv($fp)) !== FALSE) {

                $this->validateDay($csvData, $i, $year, $month);

                $working_day = sprintf('%04d', $year) . '-' . sprintf('%02d', $month) . '-' . sprintf('%02d', $csvData[0]);

                // 休日
                if (empty($csvData[1]) && empty($csvData[2])) {
                    continue;
                }

                $start = $this->validateStart($csvData, $i);

                $start_time = Carbon::create($year, $month, $csvData[0], $start[0], $start[1]);
                
                $finish = $this->validateFinish($csvData, $i);

                $finish_time = Carbon::create($year, $month, $csvData[0], $finish[0], $finish[1]);

                if ($start_time->gt($finish_time)) {
                    throw new Exception("{$i}行目：出勤時間が退勤時間よりも後になっています。");
                }

                $record = [
                    'user_id' => $user_id,
                    'working_day' => $working_day,
                    'start_time' => $start_time,
                    'finish_time' => $finish_time,
                ];

                $attendance->push($record);

                $i++;
            }

            $this->validateDuplication($attendance);

        } catch (Exception $e) {
            $errorMessage = $e->getMessage();
            return redirect()->route('admin.csv.index')
                    ->with('error', $errorMessage);
        } finally {
            fclose($fp);
        }
        
        $deleteAttendance = $this->getAttendanceForDelete($user_id, $year, $month);

        try {
            $importResult = DB::transaction(function () use ($deleteAttendance, $attendance) {
                foreach ($deleteAttendance as $record) {
                    Attendance::find($record->id)->delete();
                }

                foreach ($attendance as $record) {
                    $day = new Attendance();
                    $day->user_id = $record['user_id'];
                    $day->working_day = $record['working_day'];
                    $day->start_time = $record['start_time'];
                    $day->finish_time = $record['finish_time'];
                    $day->save();
                }

                return true;
            });
        } catch (\Throwable $e) {
            $importResult = false;
        }

        if ($importResult) {
            $resultMessage = 'インポートに成功しました。';
        } else {
            $resultMessage = 'インポートに失敗しました。';
        }

        return redirect()->route('admin.csv.index')
                    ->with('importResult', $resultMessage);
    }

    private function validateYearMonth($year, $month)
    {
        if (empty($year)) {
            throw new Exception('年が指定されていません。');
        }
        if (!is_numeric($year)) {
            throw new Exception('年が整数ではありません。');
        }
        if ($year < 2017) {
            throw new Exception('設立年より前を指定しています。');
        }
        if ($year > now()->year) {
            throw new Exception('年の指定が未来です。');
        }

        if (empty($month)) {
            throw new Exception('月が指定されていません。');
        }
        if (!is_numeric($month)) {
            throw new Exception('月が整数ではありません。');
        }
        if ($month < 1 || $month > 12) {
            throw new Exception('月数が不正です。');
        }
        if ($year == now()->year && $month >= now()->month) {
            throw new Exception('年月の指定が未来です。');
        }
    }

    private function validateDay($csvData, $i, $year, $month)
    {
        if (empty($csvData[0])) {
            throw new Exception("{$i}行目：日が指定されていません。");
        }
        if (!is_numeric($csvData[0])) {
            throw new Exception("{$i}行目：日が整数ではありません。");
        }
        if (!checkdate($month, $csvData[0], $year)) {
            throw new Exception("{$i}行目：無効な日付です。");
        }
    }

    private function validateStart($csvData, $i)
    {
        if (empty($csvData[1])) {
            throw new Exception("{$i}行目：出勤時間が指定されていません。");
        }

        $start = explode(':', $csvData[1]);

        if (empty($start[0]) || empty($start[1])) {
            throw new Exception("{$i}行目：出勤時間の形式が正しくありません。");
        }
        if (!is_numeric($start[0]) || !is_numeric($start[1])) {
            throw new Exception("{$i}行目：出勤時間が整数ではありません。");
        }
        if ($start[0] < 0 || $start[0] > 23) {
            throw new Exception("{$i}行目：出勤時間の時が不正な値です。");
        }
        if ($start[1] < 0 || $start[1] > 59) {
            throw new Exception("{$i}行目：出勤時間の分が不正な値です。");
        }

        return $start;
    }

    private function validateFinish($csvData, $i)
    {
        if (empty($csvData[2])) {
            throw new Exception("{$i}行目：退勤時間が指定されていません。");
        }

        $finish = explode(':', $csvData[2]);

        if (empty($finish[0]) || empty($finish[1])) {
            throw new Exception("{$i}行目：退勤時間の形式が正しくありません。");
        }
        if (!is_numeric($finish[0]) || !is_numeric($finish[1])) {
            throw new Exception("{$i}行目：退勤時間が整数ではありません。");
        }
        if ($finish[0] < 0 || $finish[0] > 23) {
            throw new Exception("{$i}行目：退勤時間の時が不正な値です。");
        }
        if ($finish[1] < 0 || $finish[1] > 59) {
            throw new Exception("{$i}行目：退勤時間の分が不正な値です。");
        }

        return $finish;
    }

    private function validateDuplication($attendance)
    {
        $uniqueAttendanceCount = $attendance->unique('working_day')->count();
        if ($attendance->count() !== $uniqueAttendanceCount) {
            throw new Exception('勤務日に重複があります。');
        }
    }

    private function getAttendanceForDelete($user_id, $year, $month)
    {
        $deleteStartDay = Carbon::create($year, $month)->startOfMonth()->toDateString();
        $deleteEndDay = Carbon::create($year, $month)->endOfMonth()->toDateString();

        $deleteAttendance = Attendance::where('user_id', $user_id)
                                ->where('working_day', '>=', $deleteStartDay)
                                ->where('working_day', '<=', $deleteEndDay)
                                ->get();

        return $deleteAttendance;
    }

    public function download(CsvDownloadRequest $request)
    {
        if (Gate::denies('admin.authority')) {
            abort(403);
        }

        $user_id = $request->query('download_user_id');
        $year = $request->query('year');
        $month = $request->query('month');

        $attendance = Attendance::where('user_id', $user_id)
                                ->whereYear('working_day', $year)
                                ->whereMonth('working_day', $month)
                                ->orderBy('working_day', 'ASC')
                                ->get();

        $records = [];

        foreach ($attendance as $day) {
            $date = Carbon::create($day->working_day)->format('j');
            $stringStartTime = $day->round_start_time->format('H:i');
            $stringFinishTime = $day->round_finish_time->format('H:i');
            $workingTime = sprintf('%02d', $day->working_time / 60) . ':' . sprintf('%02d', $day->working_time % 60);
            array_push($records, [$date, $stringStartTime, $stringFinishTime, $workingTime]);
        }

        $totalWorkingTime = $attendance->sum(function ($day) {
            return $day->working_time ?? 0;
        });
        $stringTotalWorkingTime = sprintf('%02d', $totalWorkingTime / 60) . ':' . sprintf('%02d', $totalWorkingTime % 60);

        $headRecords = [];

        $user = User::find($user_id);
        array_push($headRecords, [$year, $month, $user->name, $stringTotalWorkingTime]);

        $csvHeader = ['日付', '出勤時間', '退勤時間', '勤務時間'];
        array_push($headRecords, $csvHeader);

        $csvData = array_merge($headRecords, $records);

        $stream = fopen('php://temp', 'r+b');
        foreach ($csvData as $record) {
            fputcsv($stream, $record);
        }
        rewind($stream);
        $csv = str_replace(PHP_EOL, "\r\n", stream_get_contents($stream));
        $csv = mb_convert_encoding($csv, 'SJIS-win', 'UTF-8');
        $filename = $year . "年" . $month . "月分職員勤務実績記録票（" . $user->name . "）.csv";
        $headers = array(
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=' . $filename,
        );
        return response($csv, 200, $headers);
    }
}
