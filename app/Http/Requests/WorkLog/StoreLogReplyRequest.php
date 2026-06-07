<?php

namespace App\Http\Requests\WorkLog;

use Illuminate\Foundation\Http\FormRequest;

class StoreLogReplyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'message' => ['required', 'string', 'max:2000'],
        ];
    }
}
