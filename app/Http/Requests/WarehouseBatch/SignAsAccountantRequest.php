<?php
// app/Http/Requests/WarehouseBatch/SignAsAccountantRequest.php

namespace App\Http\Requests\WarehouseBatch;

use Illuminate\Foundation\Http\FormRequest;

class SignAsAccountantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('act', $this->route('batch'));
    }

    public function rules(): array
    {
        return [
            'entries' => ['required', 'array', 'min:1'],
            'entries.*.id' => ['required', 'integer', 'exists:bugalteriya,id'],
            'entries.*.debit' => ['required', 'string', 'max:50'],
            'entries.*.kredit' => ['required', 'string', 'max:50'],
            'entries.*.talab_qilingan' => ['required', 'integer', 'min:0'],
        ];
    }
}
