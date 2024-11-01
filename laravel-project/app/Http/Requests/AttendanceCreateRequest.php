<?php

namespace App\Http\Requests;

use App\Models\Attendance;
use Illuminate\Foundation\Http\FormRequest;

class AttendanceCreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $working_day = sprintf('%04d', $this->labor_year).'-'.sprintf('%02d', $this->labor_month).'-'.sprintf('%02d', $this->labor_day);
        $start_time = sprintf('%02d', $this->start_hour).':'.sprintf('%02d', $this->start_minute);
        if (! is_null($this->finish_hour) && ! is_null($this->finish_minute)) {
            $finish_time = sprintf('%02d', $this->finish_hour).':'.sprintf('%02d', $this->finish_minute);
        } else {
            $finish_time = null;
        }

        $this->merge(['working_day' => $working_day, 'start_time' => $start_time, 'finish_time' => $finish_time]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'labor_year' => ['required', 'integer', 'gte:2017'],
            'working_day' => ['required', 'date_format:Y-m-d', 'before:tomorrow'],
            'start_time' => ['required', 'date_format:H:i'],
            'finish_time' => ['nullable', 'date_format:H:i', 'after_or_equal:start_time'],
        ];
    }

    public function withValidator($validator)
    {
        $user = $this->route('user');
        $workingDay = $this->input('working_day');

        $validator->after(function ($validator) use ($user, $workingDay) {
            $attendance = Attendance::where('user_id', $user->id)
                ->where('working_day', $workingDay)
                ->first();

            if (! is_null($attendance)) {
                $validator->errors()->add('working_day', '既に勤怠が存在します。');
            }
        });
    }

    public function messages()
    {
        return [
            'working_day.before' => '勤務日には、今日までの日付を指定してください。',
            'finish_time.after_or_equal' => '退勤時間は出勤時間より後にしてください。',
        ];
    }
}
