<?php

namespace App\Http\Requests;

use App\Enums\LinkAccessType;
use BenSampo\Enum\Rules\Enum;
use Illuminate\Foundation\Http\FormRequest;

class StoreMeetingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'accessType' => LinkAccessType::coerce($this->accessType) ?? LinkAccessType::coerce(LinkAccessType::PUBLIC),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'startDate' => 'nullable|date',
            'endDate' => 'nullable|date',
            'description' => 'nullable|string',
            'participants' => 'nullable|array',
            'accessType' => ['required', new Enum(LinkAccessType::class)],
        ];
    }
}
