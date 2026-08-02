<?php
namespace App\Services;

use App\Enums\AuditAction;

class AuditService
{
    public function log(AuditAction $action, string $description, $subject = null, array $oldValues = [], array $newValues = []): void
    {
        // Implementation
    }

    public function getLogsForEvent($event)
    {
        // Implementation
    }

    public function getLogsForOrganization($organization)
    {
        // Implementation
    }
}