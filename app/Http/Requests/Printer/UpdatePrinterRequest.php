<?php
// App/Http/Requests/Printer/UpdatePrinterRequest.php
namespace App\Http\Requests\Printer;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePrinterRequest extends FormRequest
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
