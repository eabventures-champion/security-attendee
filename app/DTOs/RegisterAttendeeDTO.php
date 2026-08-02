<?php
namespace App\DTOs;

use Illuminate\Http\Request;

readonly class RegisterAttendeeDTO
{
    public function __construct(
        public string $event_id,
        public string $first_name,
        public string $last_name,
        public string $email,
        public ?string $phone,
        public ?string $company,
        public ?string $job_title,
        public array $custom_fields = []
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            event_id: $request->input('event_id'),
            first_name: $request->input('first_name'),
            last_name: $request->input('last_name'),
            email: $request->input('email'),
            phone: $request->input('phone'),
            company: $request->input('company'),
            job_title: $request->input('job_title'),
            custom_fields: $request->input('custom_fields', [])
        );
    }
}