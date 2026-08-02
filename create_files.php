<?php
$baseDir = __DIR__ . '/app';
@mkdir($baseDir.'/Enums', 0777, true);
@mkdir($baseDir.'/Traits', 0777, true);
@mkdir($baseDir.'/DTOs', 0777, true);
@mkdir($baseDir.'/Services', 0777, true);
@mkdir($baseDir.'/Http/Requests', 0777, true);
@mkdir($baseDir.'/Policies', 0777, true);
@mkdir($baseDir.'/Http/Middleware', 0777, true);

function write($path, $content) {
    global $baseDir;
    file_put_contents($baseDir . '/' . $path, '<?php' . PHP_EOL . $content);
}

write('Enums/EventStatus.php', <<<'EOT'
namespace App\Enums;

enum EventStatus: string
{
    case Draft = 'draft';
    case Published = 'published';
    case Cancelled = 'cancelled';
    case Completed = 'completed';
    case Archived = 'archived';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Published => 'Published',
            self::Cancelled => 'Cancelled',
            self::Completed => 'Completed',
            self::Archived => 'Archived',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Draft => 'gray',
            self::Published => 'emerald',
            self::Cancelled => 'rose',
            self::Completed => 'blue',
            self::Archived => 'amber',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Draft => 'pencil',
            self::Published => 'check-circle',
            self::Cancelled => 'x-circle',
            self::Completed => 'flag',
            self::Archived => 'archive',
        };
    }
}
EOT
);

write('Enums/VerificationStatus.php', <<<'EOT'
namespace App\Enums;

enum VerificationStatus: string
{
    case Pending = 'pending';
    case Verified = 'verified';
    case Rejected = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::Verified => 'Verified',
            self::Rejected => 'Rejected',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending => 'amber',
            self::Verified => 'emerald',
            self::Rejected => 'rose',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Pending => 'clock',
            self::Verified => 'check',
            self::Rejected => 'x',
        };
    }
}
EOT
);

write('Enums/AccessRole.php', <<<'EOT'
namespace App\Enums;

enum AccessRole: string
{
    case GeneralAdmission = 'general_admission';
    case Vip = 'vip';
    case Speaker = 'speaker';
    case Exhibitor = 'exhibitor';
    case Sponsor = 'sponsor';
    case Staff = 'staff';
    case Volunteer = 'volunteer';
    case Media = 'media';
    case Organizer = 'organizer';
    case Security = 'security';

    public function label(): string
    {
        return ucwords(str_replace('_', ' ', $this->value));
    }

    public function color(): string
    {
        return match ($this) {
            self::GeneralAdmission => 'gray',
            self::Vip => 'purple',
            self::Speaker => 'blue',
            self::Exhibitor => 'orange',
            self::Sponsor => 'yellow',
            self::Staff, self::Organizer => 'indigo',
            self::Volunteer => 'teal',
            self::Media => 'cyan',
            self::Security => 'red',
        };
    }

    public function badgeClass(): string
    {
        return 'bg-' . $this->color() . '-100 text-' . $this->color() . '-800';
    }
}
EOT
);

write('Enums/ScanResult.php', <<<'EOT'
namespace App\Enums;

enum ScanResult: string
{
    case Granted = 'granted';
    case DeniedWrongGate = 'denied_wrong_gate';
    case DeniedAlreadyCheckedIn = 'denied_already_checked_in';
    case DeniedQrExpired = 'denied_qr_expired';
    case DeniedNotVerified = 'denied_not_verified';
    case DeniedRevoked = 'denied_revoked';
    case DeniedUnauthorized = 'denied_unauthorized';
    case DeniedInvalid = 'denied_invalid';
    case DeniedDeviceUnauthorized = 'denied_device_unauthorized';

    public function label(): string
    {
        return match ($this) {
            self::Granted => 'Access Granted',
            self::DeniedWrongGate => 'Wrong Gate',
            self::DeniedAlreadyCheckedIn => 'Already Checked In',
            self::DeniedQrExpired => 'QR Code Expired',
            self::DeniedNotVerified => 'Not Verified',
            self::DeniedRevoked => 'Access Revoked',
            self::DeniedUnauthorized => 'Unauthorized',
            self::DeniedInvalid => 'Invalid QR Code',
            self::DeniedDeviceUnauthorized => 'Device Unauthorized',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Granted => 'emerald',
            default => 'rose',
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::Granted => 'check-circle',
            default => 'x-circle',
        };
    }

    public function isSuccess(): bool
    {
        return $this === self::Granted;
    }
}
EOT
);

write('Enums/NotificationChannel.php', <<<'EOT'
namespace App\Enums;

enum NotificationChannel: string
{
    case Email = 'email';
    case Sms = 'sms';
    case WhatsApp = 'whatsapp';
    case InApp = 'in_app';
}
EOT
);

write('Enums/NotificationType.php', <<<'EOT'
namespace App\Enums;

enum NotificationType: string
{
    case RegistrationConfirmation = 'registration_confirmation';
    case VerificationRequest = 'verification_request';
    case VerificationSuccess = 'verification_success';
    case QrDelivery = 'qr_delivery';
    case EventReminder = 'event_reminder';
    case CheckinConfirmation = 'checkin_confirmation';
    case ThankYou = 'thank_you';
}
EOT
);

write('Enums/SubscriptionStatus.php', <<<'EOT'
namespace App\Enums;

enum SubscriptionStatus: string
{
    case Active = 'active';
    case Cancelled = 'cancelled';
    case Expired = 'expired';
    case Trial = 'trial';
}
EOT
);

write('Enums/AuditAction.php', <<<'EOT'
namespace App\Enums;

enum AuditAction: string
{
    case Registration = 'registration';
    case Verification = 'verification';
    case QrGeneration = 'qr_generation';
    case QrRevocation = 'qr_revocation';
    case CheckIn = 'check_in';
    case DuplicateScan = 'duplicate_scan';
    case ManualOverride = 'manual_override';
    case Login = 'login';
    case Logout = 'logout';
    case PermissionChange = 'permission_change';
    case EventCreated = 'event_created';
    case EventUpdated = 'event_updated';
    case EventPublished = 'event_published';
    case EventCancelled = 'event_cancelled';
    case GateCreated = 'gate_created';
    case SettingsUpdated = 'settings_updated';
}
EOT
);

write('Enums/WaitingListStatus.php', <<<'EOT'
namespace App\Enums;

enum WaitingListStatus: string
{
    case Waiting = 'waiting';
    case Notified = 'notified';
    case Registered = 'registered';
    case Expired = 'expired';
}
EOT
);

write('Traits/BelongsToOrganization.php', <<<'EOT'
namespace App\Traits;

use App\Models\Organization;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToOrganization
{
    public static function bootBelongsToOrganization(): void
    {
        static::addGlobalScope('organization', function (Builder $builder) {
            if (auth()->check() && session()->has('current_organization_id')) {
                $builder->where($builder->getModel()->getTable() . '.organization_id', session('current_organization_id'));
            }
        });

        static::creating(function (Model $model) {
            if (empty($model->organization_id) && auth()->check() && session()->has('current_organization_id')) {
                $model->organization_id = session('current_organization_id');
            }
        });
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function scopeForOrganization(Builder $query, int $organizationId): Builder
    {
        return $query->withoutGlobalScope('organization')->where('organization_id', $organizationId);
    }
}
EOT
);

write('Traits/HasUuid.php', <<<'EOT'
namespace App\Traits;

use Illuminate\Support\Str;

trait HasUuid
{
    public static function bootHasUuid(): void
    {
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    public function initializeHasUuid(): void
    {
        $this->fillable = array_diff($this->fillable, ['uuid']);
        $this->guarded[] = 'uuid';
    }

    public static function findByUuid(string $uuid): ?self
    {
        return static::where('uuid', $uuid)->first();
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }
}
EOT
);

write('DTOs/CreateEventDTO.php', <<<'EOT'
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
EOT
);

write('DTOs/RegisterAttendeeDTO.php', <<<'EOT'
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
EOT
);

write('DTOs/CheckInDTO.php', <<<'EOT'
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
EOT
);

write('DTOs/QrPayloadDTO.php', <<<'EOT'
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
EOT
);

write('Services/OrganizationService.php', <<<'EOT'
namespace App\Services;

class OrganizationService
{
    public function create(array $data): void
    {
        // Implementation
    }

    public function update(array $data): void
    {
        // Implementation
    }

    public function getCurrentOrganization()
    {
        // Implementation
    }

    public function checkSubscriptionLimit(): bool
    {
        // Implementation
        return true;
    }
}
EOT
);

write('Services/EventService.php', <<<'EOT'
namespace App\Services;

use App\DTOs\CreateEventDTO;

class EventService
{
    public function create(CreateEventDTO $dto)
    {
        // Implementation
    }

    public function update($event, array $data)
    {
        // Implementation
    }

    public function duplicate($event)
    {
        // Implementation
    }

    public function publish($event)
    {
        // Implementation
    }

    public function archive($event)
    {
        // Implementation
    }

    public function cancel($event, string $reason)
    {
        // Implementation
    }

    public function getUpcoming()
    {
        // Implementation
    }

    public function getPast()
    {
        // Implementation
    }

    public function getStats($event)
    {
        // Implementation
    }
}
EOT
);

write('Services/RegistrationService.php', <<<'EOT'
namespace App\Services;

use App\DTOs\RegisterAttendeeDTO;

class RegistrationService
{
    public function register(RegisterAttendeeDTO $dto)
    {
        // Implementation
    }

    public function checkDuplicate(string $email, ?string $phone, $eventId): bool
    {
        // Implementation
        return false;
    }

    public function assignTicketCategory($attendee, $ticketCategory)
    {
        // Implementation
    }

    public function getRegistrationStats($event)
    {
        // Implementation
    }
}
EOT
);

write('Services/VerificationService.php', <<<'EOT'
namespace App\Services;

class VerificationService
{
    public function sendVerification($attendee)
    {
        // Implementation
    }

    public function verifyByToken(string $token)
    {
        // Implementation
    }

    public function verifyByOtp($attendee, string $otp)
    {
        // Implementation
    }

    public function generateOtp($attendee)
    {
        // Implementation
    }

    public function resendVerification($attendee)
    {
        // Implementation
    }

    public function rejectVerification($attendee, string $reason)
    {
        // Implementation
    }

    public function getVerificationStats($event)
    {
        // Implementation
    }
}
EOT
);

write('Services/QrCodeService.php', <<<'EOT'
namespace App\Services;

use App\DTOs\QrPayloadDTO;

class QrCodeService
{
    public function generate($attendee)
    {
        // Implementation
    }

    public function encryptPayload(QrPayloadDTO $dto): string
    {
        return $dto->toEncrypted();
    }

    public function generateSignature(string $data): string
    {
        return hash_hmac('sha256', $data, config('app.key'));
    }

    public function validateSignature($payload, string $signature): bool
    {
        // Implementation
        return true;
    }

    public function decryptPayload(string $encrypted)
    {
        // Implementation
    }

    public function revokeQrCode($qrCode, string $reason)
    {
        // Implementation
    }

    public function reissueQrCode($attendee)
    {
        // Implementation
    }

    public function generateQrImage($qrCode)
    {
        // Implementation
    }

    public function isExpired($qrCode): bool
    {
        return false;
    }
}
EOT
);

write('Services/CheckInService.php', <<<'EOT'
namespace App\Services;

use App\DTOs\CheckInDTO;
use App\Enums\ScanResult;

class CheckInService
{
    public function processCheckIn(CheckInDTO $dto): ScanResult
    {
        // Implementation
        return ScanResult::Granted;
    }

    public function validateQrCode(string $scannedData)
    {
        // Implementation
    }

    public function checkGateAuthorization($attendee, $gate): bool
    {
        return true;
    }

    public function checkDuplicateScan($attendee, $event): bool
    {
        return false;
    }

    public function manualCheckIn($attendee, $gate, $user)
    {
        // Implementation
    }

    public function getCheckInStats($event)
    {
        // Implementation
    }

    public function getGateActivity($gate)
    {
        // Implementation
    }
}
EOT
);

write('Services/AuditService.php', <<<'EOT'
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
EOT
);

write('Services/ReportService.php', <<<'EOT'
namespace App\Services;

class ReportService
{
    public function attendanceReport($event, array $filters = [])
    {
        // Implementation
    }

    public function registrationReport($event, array $filters = [])
    {
        // Implementation
    }

    public function verificationReport($event, array $filters = [])
    {
        // Implementation
    }

    public function gateReport($event, $gate)
    {
        // Implementation
    }

    public function duplicateScanReport($event)
    {
        // Implementation
    }
}
EOT
);

write('Services/NotificationService.php', <<<'EOT'
namespace App\Services;

use App\Enums\NotificationType;
use App\Enums\NotificationChannel;
use Illuminate\Support\Collection;

class NotificationService
{
    public function send($attendee, NotificationType $type, NotificationChannel $channel): void
    {
        // Implementation
    }

    public function sendBulk(Collection $attendees, NotificationType $type): void
    {
        // Implementation
    }

    public function logNotification($attendee, NotificationType $type, NotificationChannel $channel, string $status): void
    {
        // Implementation
    }
}
EOT
);

write('Http/Requests/StoreEventRequest.php', <<<'EOT'
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'venue_name' => ['nullable', 'string', 'max:255'],
            'venue_address' => ['nullable', 'string', 'max:255'],
            'venue_city' => ['nullable', 'string', 'max:255'],
            'venue_country' => ['nullable', 'string', 'max:255'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['required', 'date', 'after_or_equal:starts_at'],
            'registration_deadline' => ['nullable', 'date', 'before_or_equal:starts_at'],
            'capacity' => ['nullable', 'integer', 'min:1'],
            'is_multi_day' => ['boolean'],
            'is_free' => ['boolean'],
            'settings' => ['nullable', 'array'],
        ];
    }
}
EOT
);

write('Http/Requests/UpdateEventRequest.php', <<<'EOT'
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'venue_name' => ['nullable', 'string', 'max:255'],
            'venue_address' => ['nullable', 'string', 'max:255'],
            'venue_city' => ['nullable', 'string', 'max:255'],
            'venue_country' => ['nullable', 'string', 'max:255'],
            'starts_at' => ['sometimes', 'required', 'date'],
            'ends_at' => ['sometimes', 'required', 'date', 'after_or_equal:starts_at'],
            'registration_deadline' => ['nullable', 'date', 'before_or_equal:starts_at'],
            'capacity' => ['nullable', 'integer', 'min:1'],
            'is_multi_day' => ['boolean'],
            'is_free' => ['boolean'],
            'settings' => ['nullable', 'array'],
        ];
    }
}
EOT
);

write('Http/Requests/RegisterAttendeeRequest.php', <<<'EOT'
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterAttendeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'event_id' => ['required', 'exists:events,id'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'company' => ['nullable', 'string', 'max:255'],
            'job_title' => ['nullable', 'string', 'max:255'],
            'consent' => ['accepted'],
        ];
    }
}
EOT
);

write('Http/Requests/StoreGateRequest.php', <<<'EOT'
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'event_id' => ['required', 'exists:events,id'],
        ];
    }
}
EOT
);

write('Http/Requests/ProcessCheckInRequest.php', <<<'EOT'
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
EOT
);

write('Policies/OrganizationPolicy.php', <<<'EOT'
namespace App\Policies;

use App\Models\User;
use App\Models\Organization;

class OrganizationPolicy
{
    public function viewAny(User $user): bool { return true; }
    public function view(User $user, Organization $organization): bool { return true; }
    public function create(User $user): bool { return true; }
    public function update(User $user, Organization $organization): bool { return true; }
    public function delete(User $user, Organization $organization): bool { return true; }
    public function manageMembers(User $user, Organization $organization): bool { return true; }
    public function manageSettings(User $user, Organization $organization): bool { return true; }
}
EOT
);

write('Policies/EventPolicy.php', <<<'EOT'
namespace App\Policies;

use App\Models\User;
use App\Models\Event;

class EventPolicy
{
    public function viewAny(User $user): bool { return true; }
    public function view(User $user, Event $event): bool { return true; }
    public function create(User $user): bool { return true; }
    public function update(User $user, Event $event): bool { return true; }
    public function delete(User $user, Event $event): bool { return true; }
    public function publish(User $user, Event $event): bool { return true; }
    public function archive(User $user, Event $event): bool { return true; }
    public function cancel(User $user, Event $event): bool { return true; }
    public function duplicate(User $user, Event $event): bool { return true; }
    public function manageAttendees(User $user, Event $event): bool { return true; }
    public function manageGates(User $user, Event $event): bool { return true; }
    public function viewReports(User $user, Event $event): bool { return true; }
}
EOT
);

write('Policies/AttendeePolicy.php', <<<'EOT'
namespace App\Policies;

use App\Models\User;
use App\Models\Attendee;

class AttendeePolicy
{
    public function viewAny(User $user): bool { return true; }
    public function view(User $user, Attendee $attendee): bool { return true; }
    public function create(User $user): bool { return true; }
    public function update(User $user, Attendee $attendee): bool { return true; }
    public function delete(User $user, Attendee $attendee): bool { return true; }
    public function verify(User $user, Attendee $attendee): bool { return true; }
    public function checkIn(User $user, Attendee $attendee): bool { return true; }
    public function downloadQr(User $user, Attendee $attendee): bool { return true; }
}
EOT
);

write('Policies/GatePolicy.php', <<<'EOT'
namespace App\Policies;

use App\Models\User;
use App\Models\Gate;

class GatePolicy
{
    public function viewAny(User $user): bool { return true; }
    public function view(User $user, Gate $gate): bool { return true; }
    public function create(User $user): bool { return true; }
    public function update(User $user, Gate $gate): bool { return true; }
    public function delete(User $user, Gate $gate): bool { return true; }
    public function assignRoles(User $user, Gate $gate): bool { return true; }
}
EOT
);

write('Http/Middleware/EnsureOrganizationContext.php', <<<'EOT'
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOrganizationContext
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $user = auth()->user();
            $organizationId = session('current_organization_id');

            if (!$organizationId) {
                // Determine a default organization if none in session
                // $organizationId = ...
            }

            if (!$organizationId && !$user->is_super_admin) {
                abort(403, 'No active organization context found.');
            }

            if ($organizationId) {
                app()->singleton('current_organization_id', fn() => $organizationId);
            }
        }

        return $next($request);
    }
}
EOT
);

write('Http/Middleware/CheckSubscriptionLimit.php', <<<'EOT'
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\OrganizationService;

class CheckSubscriptionLimit
{
    public function __construct(private readonly OrganizationService $organizationService)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        if (!$this->organizationService->checkSubscriptionLimit()) {
            abort(403, 'Subscription limits exceeded.');
        }

        return $next($request);
    }
}
EOT
);

write('Http/Middleware/TrackLastLogin.php', <<<'EOT'
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackLastLogin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $user = auth()->user();
            if (!$user->last_login_at || $user->last_login_at->diffInMinutes(now()) > 5) {
                $user->update(['last_login_at' => now()]);
            }
        }

        return $next($request);
    }
}
EOT
);

echo "Files created successfully\n";
