<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProcessCheckInRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'scanned_data' => ['required', 'string'],
            'gate_uuid' => ['required', 'string', 'exists:gates,uuid'],
            'device_id' => ['required', 'string'],
        ];
    }
}