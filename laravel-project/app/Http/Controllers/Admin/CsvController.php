<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CsvUploadRequest;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
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

        $errorMessage = null;

        try {
            if (!$request->hasFile('csv_file')) {
                throw new Exception('CSVファイルの取得に失敗しました。');
            }

            $file = $request->file('csv_file');
            $path = $file->getRealPath();
            $fp = fopen($path, 'r');

            $csvData = fgetcsv($fp);

            if (empty($csvData[0])) {
                throw new Exception('年が指定されていません。');
            }
            if (!is_numeric($csvData[0])) {
                throw new Exception('年が整数ではありません。');
            }
            if ($csvData[0] < 2017) {
                throw new Exception('設立年より前を指定しています。');
            }
            if ($csvData[0] > now()->year) {
                throw new Exception('年の指定が未来です。');
            }

            if (empty($csvData[1])) {
                throw new Exception('月が指定されていません。');
            }
            if (!is_numeric($csvData[1])) {
                throw new Exception('月が整数ではありません。');
            }
            if ($csvData[1] < 1 || $csvData[1] > 12) {
                throw new Exception('月数が不正です。');
            }
            if ($csvData[0] == now()->year && $csvData[1] >= now()->month) {
                throw new Exception('年月の指定が未来です。');
            }

            $year = $csvData[0];
            $month = $csvData[1];

            // ヘッダースキップ
            fgetcsv($fp);

            $i = 3;

            while (($csvData = fgetcsv($fp)) !== FALSE) {
                if (empty($csvData[0])) {
                    throw new Exception("{$i}行目：日が指定されていません。");
                }
                if (!is_numeric($csvData[0])) {
                    throw new Exception("{$i}行目：日が整数ではありません。");
                }
                if (!checkdate($month, $csvData[0], $year)) {
                    throw new Exception("{$i}行目：無効な日付です。");
                }

                $working_day = sprintf('%04d', $year) . '-' . sprintf('%02d', $month) . '-' . sprintf('%02d', $csvData[0]);

                // 休日
                if (empty($csvData[1]) && empty($csvData[2])) {
                    continue;
                }

                if (empty($csvData[1])) {
                    throw new Exception("{$i}行目：出勤時間が指定されていません。");
                }

                $start = explode(':', $csvData[1]);

                if ((empty($start[0]) && $start[0] !== '00') || (empty($start[1]) && $start[1] !== '00')) {
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

                $start_time = Carbon::create($year, $month, $csvData[0], $start[0], $start[1]);
                
                if (empty($csvData[2])) {
                    throw new Exception("{$i}行目：退勤時間が指定されていません。");
                }

                $finish = explode(':', $csvData[2]);

                if ((empty($finish[0]) && $finish[0] !== '00') || (empty($finish[1]) && $finish[1] !== '00')) {
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

            $formerCount = $attendance->count();
            $uniqueAttendance = $attendance->unique('working_day');
            $latterCount = $uniqueAttendance->count();
            if ($formerCount !== $latterCount) {
                throw new Exception('勤務日に重複があります。');
            }

        } catch (Exception $e) {
            $errorMessage = $e->getMessage();
        } finally {
            fclose($fp);
        }

        return redirect()->route('admin.csv.index')
                    ->with('error', $errorMessage);
    }
}
