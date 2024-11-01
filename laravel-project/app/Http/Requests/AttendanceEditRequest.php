<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AttendanceEditRequest extends FormRequest
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
        $start_time = sprintf('%02d', $this->start_hour).':'.sprintf('%02d', $this->start_minute);
        if (! is_null($this->finish_hour) && ! is_null($this->finish_minute)) {
            $finish_time = sprintf('%02d', $this->finish_hour).':'.sprintf('%02d', $this->finish_minute);
        } else {
            $finish_time = null;
        }

        $this->merge(['start_time' => $start_time, 'finish_time' => $finish_time]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'start_time' => ['required', 'date_format:H:i'],
            'finish_time' => ['nullable', 'date_format:H:i', 'after_or_equal:start_time'],
        ];
    }

    public function messages()
    {
        return [
            'finish_time.after_or_equal' => '退勤時間は出勤時間より後にしてください。',
        ];
    }
}
