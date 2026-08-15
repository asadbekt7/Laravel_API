<?php
// app/Http/Requests/WarehouseBatch/RejectWarehouseBatchRequest.php

namespace App\Http\Requests\WarehouseBatch;

use Illuminate\Foundation\Http\FormRequest;

class RejectWarehouseBatchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('act', $this->route('batch'));
    }

    public function rules(): array
    {
        return [
            'comment' => ['required', 'string', 'max:1000'],
        ];
    }
}
