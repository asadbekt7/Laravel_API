<?php
// App/Http/Requests/Monitor/UpdateMonitorRequest.php
namespace App\Http\Requests\Monitor;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateMonitorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'        => ['sometimes', 'string', 'max:255'],
            'category_id' => ['sometimes', 'integer', 'exists:categories,id'],
            'quantity'    => ['sometimes', 'integer', 'min:1'],
            'description' => ['nullable', 'string'],
            'room_name'   => ['nullable', 'string'],
            'building'    => ['nullable', 'string'],
            'room_number' => ['nullable', 'string'],
        ];
    }
}
