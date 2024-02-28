<?php

namespace App\Http\Requests;

use App\Enums\ActivityAction;
use App\Enums\LinkAccessType;
use BenSampo\Enum\Rules\Enum;
use Illuminate\Foundation\Http\FormRequest;

class StoreActivityLogRequest extends FormRequest
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
        if ($this->has('action')) {
            $this->merge([
                'action' => ActivityAction::coerce($this->action),
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'action' => ['required', new Enum(ActivityAction::class)],
            'user_id' => ['required', 'exists:users,id'],
            'meeting_id' => ['required', 'exists:meetings,id'],
        ];
    }
}
