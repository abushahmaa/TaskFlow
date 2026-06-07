<?php

namespace App\Http\Requests\WorkLog;

use Illuminate\Foundation\Http\FormRequest;

class StoreWorkLogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'description'  => ['required', 'string'],
            'hours_worked' => ['required', 'numeric', 'min:0.25', 'max:24'],
            'attachment'   => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,doc,docx,txt,zip', 'max:10240'],
        ];
    }
}
