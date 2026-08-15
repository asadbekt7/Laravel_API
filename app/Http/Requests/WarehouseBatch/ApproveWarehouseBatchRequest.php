<?php
// app/Http/Requests/WarehouseBatch/ApproveWarehouseBatchRequest.php

namespace App\Http\Requests\WarehouseBatch;

use Illuminate\Foundation\Http\FormRequest;

class ApproveWarehouseBatchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('act', $this->route('batch'));
    }

    public function rules(): array
    {
        return [
            'comment' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
