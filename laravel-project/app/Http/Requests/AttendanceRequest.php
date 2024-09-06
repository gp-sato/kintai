<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AttendanceRequest extends FormRequest
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
        $data = $this->all();

        if (mb_strlen($data['start_hour']) == 1) {
            $data['start_hour'] = '0' . $data['start_hour'];
        }
        if (mb_strlen($data['start_minute']) == 1) {
            $data['start_minute'] = '0' . $data['start_minute'];
        }
        if (mb_strlen($data['finish_hour']) == 1) {
            $data['finish_hour'] = '0' . $data['finish_hour'];
        }
        if (mb_strlen($data['finish_minute']) == 1) {
            $data['finish_minute'] = '0' . $data['finish_minute'];
        }

        $start_time = $data['start_hour'] . ':' . $data['start_minute'];
        $finish_time = $data['finish_hour'] . ':' . $data['finish_minute'];

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
            'finish_time' => ['required', 'date_format:H:i', 'after_or_equal:start_time'],
        ];
    }
}
