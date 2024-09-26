<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CsvUploadRequest;
use App\Models\User;
use Carbon\Carbon;
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

        if (!$request->hasFile('csv_file')) {
            throw new \Exception('CSVファイルの取得に失敗しました。');
        } else {
            $file = $request->file('csv_file');
            $path = $file->getRealPath();
            $fp = fopen($path, 'r');

            $csvData = fgetcsv($fp);

            if (empty($csvData[0])) {
                throw new \Exception('年が指定されていません。');
            }
            if (!is_numeric($csvData[0])) {
                throw new \Exception('年が整数ではありません。');
            }
            if ($csvData[0] < 2017) {
                throw new \Exception('設立年より前を指定しています。');
            }
            if ($csvData[0] > now()->year) {
                throw new \Exception('年の指定が未来です。');
            }

            if (empty($csvData[1])) {
                throw new \Exception('月が指定されていません。');
            }
            if (!is_numeric($csvData[1])) {
                throw new \Exception('月が整数ではありません。');
            }
            if ($csvData[1] < 1 || $csvData[1] > 12) {
                throw new \Exception('月数が不正です。');
            }
            if ($csvData[0] == now()->year && $csvData[1] >= now()->month) {
                throw new \Exception('年月の指定が未来です。');
            }

            $year = $csvData[0];
            $month = $csvData[1];

            // ヘッダースキップ
            fgetcsv($fp);

            while (($csvData = fgetcsv($fp)) !== FALSE) {
                if (empty($csvData[0])) {
                    throw new \Exception('日が指定されていません。');
                }
                if (!is_numeric($csvData[0])) {
                    throw new \Exception('日が整数ではありません。');
                }
                if (!checkdate($month, $csvData[0], $year)) {
                    throw new \Exception('無効な日付です。');
                }

                $working_day = sprintf('%04d', $year) . '-' . sprintf('%02d', $month) . '-' . sprintf('%02d', $csvData[0]);

                if (empty($csvData[1])) {
                    throw new \Exception('出勤時間が指定されていません。');
                }

                $start = explode(':', $csvData[1]);

                if ((empty($start[0]) && $start[0] !== '00') || (empty($start[1]) && $start[1] !== '00')) {
                    throw new \Exception('出勤時間の形式が正しくありません。');
                }
                if (!is_numeric($start[0]) || !is_numeric($start[1])) {
                    throw new \Exception('出勤時間が整数ではありません。');
                }
                if ($start[0] < 0 || $start[0] > 23) {
                    throw new \Exception('出勤の時が不正な値です。');
                }
                if ($start[1] < 0 || $start[1] > 59) {
                    throw new \Exception('出勤の分が不正な値です。');
                }

                $start_time = Carbon::create($year, $month, $csvData[0], $start[0], $start[1]);
                
                if (empty($csvData[2])) {
                    throw new \Exception('退勤時間が指定されていません。');
                }

                $finish = explode(':', $csvData[2]);

                if ((empty($finish[0]) && $finish[0] !== '00') || (empty($finish[1]) && $finish[1] !== '00')) {
                    throw new \Exception('退勤時間の形式が正しくありません。');
                }
                if (!is_numeric($finish[0]) || !is_numeric($finish[1])) {
                    throw new \Exception('退勤時間が整数ではありません。');
                }
                if ($finish[0] < 0 || $finish[0] > 23) {
                    throw new \Exception('退勤の時が不正な値です。');
                }
                if ($finish[1] < 0 || $finish[1] > 59) {
                    throw new \Exception('退勤の分が不正な値です。');
                }

                $finish_time = Carbon::create($year, $month, $csvData[0], $finish[0], $finish[1]);

                if ($start_time->gt($finish_time)) {
                    throw new \Exception('出勤時間が退勤時間よりも後になっています。');
                }

                $record = [$user_id, $working_day, $start_time, $finish_time];
            }

            fclose($fp);
        }

        return redirect()->route('admin.csv.index');
    }
}
