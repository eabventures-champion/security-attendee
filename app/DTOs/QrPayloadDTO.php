<?php
namespace App\DTOs;

readonly class QrPayloadDTO
{
    public function __construct(
        public string $event_uuid,
        public string $attendee_uuid,
        public string $secure_token,
        public string $expires_at,
        public ?string $signature = null
    ) {}

    public function toArray(): array
    {
        return [
            'event_uuid' => $this->event_uuid,
            'attendee_uuid' => $this->attendee_uuid,
            'secure_token' => $this->secure_token,
            'expires_at' => $this->expires_at,
            'signature' => $this->signature,
        ];
    }

    public function toEncrypted(): string
    {
        // Example implementation for encryption
        return base64_encode(json_encode($this->toArray()));
    }

    public static function fromDecrypted(array $data): self
    {
        return new self(
            event_uuid: $data['event_uuid'],
            attendee_uuid: $data['attendee_uuid'],
            secure_token: $data['secure_token'],
            expires_at: $data['expires_at'],
            signature: $data['signature'] ?? null
        );
    }
}