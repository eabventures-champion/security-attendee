<?php
namespace App\DTOs;

use Illuminate\Http\Request;

readonly class CreateEventDTO
{
    public function __construct(
        public string $name,
        public ?string $description,
        public ?string $venue_name,
        public ?string $venue_address,
        public ?string $venue_city,
        public ?string $venue_country,
        public string $starts_at,
        public string $ends_at,
        public ?string $registration_deadline,
        public ?int $capacity,
        public bool $is_multi_day,
        public bool $is_free,
        public array $settings = []
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            name: $request->input('name'),
            description: $request->input('description'),
            venue_name: $request->input('venue_name'),
            venue_address: $request->input('venue_address'),
            venue_city: $request->input('venue_city'),
            venue_country: $request->input('venue_country'),
            starts_at: $request->input('starts_at'),
            ends_at: $request->input('ends_at'),
            registration_deadline: $request->input('registration_deadline'),
            capacity: $request->input('capacity'),
            is_multi_day: $request->boolean('is_multi_day'),
            is_free: $request->boolean('is_free'),
            settings: $request->input('settings', [])
        );
    }
}