<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class CsvUploadRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_id' => ['required', 'integer'],
        ];
    }

    public function withValidator($validator)
    {
        $user_id = $this->input('user_id');

        $validator->after(function ($validator) use ($user_id) {
            $user = User::find($user_id);

            if (is_null($user)) {
                $validator->errors()->add('user_id', '存在しないユーザーです。');
            } elseif ($user->is_admin !== 0) {
                $validator->errors()->add('user_id', '一般ユーザーではありません。');
            }
        });
    }

    public function messages()
    {
        return [
            'user_id.required' => '名前は必ず指定してください。',
            'user_id.integer' => '不正な値が入力されました。'
        ];
    }
}
