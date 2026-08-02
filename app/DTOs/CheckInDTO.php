<?php
namespace App\DTOs;

use Illuminate\Http\Request;

readonly class CheckInDTO
{
    public function __construct(
        public string $scanned_data,
        public string $gate_uuid,
        public int $scanned_by_id,
        public string $device_id,
        public ?string $ip_address
    ) {}
}