<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class CsvDownloadRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function prepareForValidation()
    {
        $yearMonth = sprintf('%04d', $this->year) . '-' . sprintf('%02d', $this->month);

        $this->merge(['yearMonth' => $yearMonth]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'download_user_id' => ['required', 'integer'],
            'year' => ['required', 'integer', 'gte:2017'],
            'month' => ['required', 'integer'],
            'yearMonth' => ['required', 'date_format:Y-m'],
        ];
    }

    public function withValidator($validator)
    {
        $user_id = $this->query('download_user_id');

        $validator->after(function ($validator) use ($user_id) {
            $user = User::find($user_id);

            if (!is_null($user_id) && is_null($user)) {
                $validator->errors()->add('download_user_id', '存在しないユーザーです。');
            }
            if (!is_null($user) && $user->is_admin !== 0) {
                $validator->errors()->add('download_user_id', '一般ユーザーではありません。');
            }
        });
    }

    public function attributes()
    {
        return [
            'year' => '年',
            'month' => '月',
            'yearMonth' => '年月',
        ];
    }

    public function messages()
    {
        return [
            'download_user_id.required' => '名前は必ず指定してください。',
            'download_user_id.integer' => '不正な値が入力されました。'
        ];
    }
}
