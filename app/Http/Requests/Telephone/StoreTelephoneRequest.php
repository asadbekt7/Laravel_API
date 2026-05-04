<?php
// App/Http/Requests/Telephone/StoreTelephoneRequest.php
namespace App\Http\Requests\Telephone;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreTelephoneRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'        => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'model_id'    => ['required', 'integer', 'exists:models,id'],
            'unit_id'     => ['required', 'integer', 'exists:units,id'],
            'quantity'    => ['required', 'integer', 'min:1'],
            'description' => ['nullable', 'string'],
        ];
    }
}
