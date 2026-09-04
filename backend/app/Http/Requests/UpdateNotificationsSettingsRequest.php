<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\NotificationType;
use Illuminate\Validation\Rule;

class UpdateNotificationsSettingsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'preferences'                          => ['required', 'array'],
            'preferences.*.notification_type'      => ['required', 'string', Rule::enum(NotificationType::class)],
            'preferences.*.mail'                   => ['required', 'boolean'],
        ];
    }
}
