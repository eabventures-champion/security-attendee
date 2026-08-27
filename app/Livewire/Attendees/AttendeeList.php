<?php

namespace App\Livewire\Attendees;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Attendee;
use App\Models\Event;
use App\Models\QrCode;
use App\Enums\VerificationStatus;
use App\Enums\AccessRole;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Mail\AttendeePrivateInvitation;

#[Layout('layouts.app')]
#[Title('Attendees Management')]
class AttendeeList extends Component
{
    use WithPagination, WithFileUploads;

    public $eventUuid;
    public $search = '';
    public $statusFilter = '';
    public $roleFilter = '';
    public $categoryFilter = '';
    public array $expandedOrgs = [];
    public array $expandedEvents = [];
    public bool $groupedView = true;
    public int $perPage = 10;
    public array $selectedAttendees = [];
    public bool $selectAll = false;
    public bool $selectAllOnPage = false;

    // Email delivery report & progressive batch processing state
    public bool $showEmailReportModal = false;
    public array $emailDeliveryResults = [];
    public int $emailSuccessCount = 0;
    public int $emailFailedCount = 0;
    public int $approvedTotalCount = 0;
    public bool $isProcessingBatch = false;
    public array $pendingBatchUuids = [];
    public int $batchTotalCount = 0;
    public int $batchProcessedCount = 0;
    public ?string $currentBatchId = null;
    public int $batchChunkSize = 6;

    public function toggleExpandOrg(int $orgId): void
    {
        if (in_array($orgId, $this->expandedOrgs)) {
            $this->expandedOrgs = array_diff($this->expandedOrgs, [$orgId]);
        } else {
            $this->expandedOrgs[] = $orgId;
        }
    }

    public function toggleExpandEvent(int $eventId): void
    {
        if (in_array($eventId, $this->expandedEvents)) {
            $this->expandedEvents = array_diff($this->expandedEvents, [$eventId]);
        } else {
            $this->expandedEvents[] = $eventId;
        }
    }

    public bool $showMobileOrgModal = false;
    public ?int $mobileOrgId = null;
    public ?int $mobileSelectedEventId = null;

    public function openMobileOrgModal(int $orgId): void
    {
        $this->mobileOrgId = $orgId;
        $this->mobileSelectedEventId = null;
        $this->showMobileOrgModal = true;
    }

    public function closeMobileOrgModal(): void
    {
        $this->showMobileOrgModal = false;
        $this->mobileOrgId = null;
        $this->mobileSelectedEventId = null;
    }

    public function selectMobileEvent(?int $eventId): void
    {
        if ($this->mobileSelectedEventId === $eventId) {
            $this->mobileSelectedEventId = null;
        } else {
            $this->mobileSelectedEventId = $eventId;
        }
    }

    public bool $showMobileAttendeesModal = false;
    public ?int $mobileEventId = null;

    public function openMobileAttendeesModal(int $eventId): void
    {
        $this->mobileEventId = $eventId;
        $this->showMobileAttendeesModal = true;
    }

    public function closeMobileAttendeesModal(): void
    {
        $this->showMobileAttendeesModal = false;
        $this->mobileEventId = null;
    }

    public bool $showAddModal = false;
    public bool $showDetailsModal = false;
    public bool $showBulkInviteModal = false;
    public $selectedAttendee = null;

    // Form fields for adding attendee
    public $new_event_id = '';
    public $new_full_name = '';
    public $new_email = '';
    public $new_phone = '';
    public $new_access_role = 'general_admission';
    public $new_assigned_gate_id = null;
    public $new_verification_status = 'verified';
    public bool $auto_generate_qr = true;

    // Bulk invite fields
    public string $bulk_invite_type = 'form'; // 'form' (Option A) or 'direct' (Option B)
    public string $bulk_input_mode = 'emails_only'; // 'emails_only' (1), 'names_and_emails' (2), or 'excel_import' (3)
    public string $bulk_emails = '';
    public string $bulk_names_emails = '';
    public string $bulk_access_role = 'general_admission';
    public bool $bulk_auto_verify = true;
    public $bulk_excel_file = null;
    public string $bulk_uploaded_file_name = '';
    public int $bulk_imported_count = 0;
    public bool $bulk_resend_to_existing = false; // Default false: only send to non-duplicates
    public bool $bulk_show_duplicate_details = false;

    // Import CSV fields
    public bool $showImportCsvModal = false;
    public string $import_event_id = '';
    public $csv_file = null;
    public array $importResults = [];
    public array $importEventFields = [];

    // Secure Single-Use Link Generator fields
    public bool $showLinkGeneratorModal = false;
    public string $gen_event_id = ''; // Explicit event selector for secure link
    public string $gen_access_role = 'vvip';
    public string $gen_category = 'details'; // 'details', 'no_details'
    public string $gen_email = '';
    public int $gen_max_uses = 1;
    public string $generated_invite_url = '';
    public string $generated_whatsapp_url = '';
    public array $gen_standard_fields = [];
    public array $gen_custom_fields = [];

    public function updatedGenEventId($value)
    {
        if ($value) {
            $event = Event::find($value);
            if ($event) {
                $this->gen_category = $event->settings['default_entry_mode'] ?? 'details';
                $config = $event->form_fields_config;
                $this->gen_standard_fields = $config['standard_fields'];
                $this->gen_custom_fields = $config['custom_fields'];
            }
        }
    }

    public function openLinkGeneratorModal()
    {
        $this->showLinkGeneratorModal = true;
        $this->generated_invite_url = '';
        $this->generated_whatsapp_url = '';

        // Pre-select event from current filter, or default to first event
        if ($this->eventUuid) {
            $event = Event::where('uuid', $this->eventUuid)->first();
            $this->gen_event_id = $event ? (string) $event->id : '';
            if ($event) {
                $this->gen_category = $event->settings['default_entry_mode'] ?? 'details';
                $config = $event->form_fields_config;
                $this->gen_standard_fields = $config['standard_fields'];
                $this->gen_custom_fields = $config['custom_fields'];
            }
        } else {
            $firstEvent = Event::first();
            if ($firstEvent) {
                $this->gen_event_id = (string) $firstEvent->id;
                $this->gen_category = $firstEvent->settings['default_entry_mode'] ?? 'details';
                $config = $firstEvent->form_fields_config;
                $this->gen_standard_fields = $config['standard_fields'];
                $this->gen_custom_fields = $config['custom_fields'];
            } else {
                $this->gen_event_id = '';
                $this->gen_category = 'details';
                $defaultConfig = Event::defaultFormFieldsConfig();
                $this->gen_standard_fields = $defaultConfig['standard_fields'];
                $this->gen_custom_fields = [];
            }
        }
    }

    public function closeLinkGeneratorModal()
    {
        $this->showLinkGeneratorModal = false;
        $this->generated_invite_url = '';
        $this->generated_whatsapp_url = '';
        $this->gen_email = '';
        $this->gen_event_id = '';
    }

    public function setGenFieldStatus(string $fieldKey, string $status): void
    {
        $this->gen_standard_fields[$fieldKey] = $status;
        $this->persistGenFormFields();
    }

    public function addGenCustomField(): void
    {
        $this->gen_custom_fields[] = [
            'id' => 'field_' . \Illuminate\Support\Str::random(8),
            'label' => '',
            'type' => 'text',
            'required' => false,
            'options' => '',
        ];
        $this->persistGenFormFields();
    }

    public function removeGenCustomField(int $index): void
    {
        unset($this->gen_custom_fields[$index]);
        $this->gen_custom_fields = array_values($this->gen_custom_fields);
        $this->persistGenFormFields();
    }

    public function persistGenFormFields(): void
    {
        if (!empty($this->gen_event_id)) {
            $event = Event::find($this->gen_event_id);
            if ($event) {
                $currentSettings = is_array($event->settings) ? $event->settings : [];
                $currentSettings['form_fields'] = [
                    'standard_fields' => $this->gen_standard_fields,
                    'custom_fields' => array_values(array_filter($this->gen_custom_fields, fn($f) => !empty(trim($f['label'] ?? '')))),
                ];
                $event->settings = $currentSettings;
                $event->save();
            }
        }
    }

    public function generateSingleUseLink()
    {
        if (empty($this->gen_event_id)) {
            session()->flash('error', 'Please select an event to generate the link for.');
            return;
        }

        $this->persistGenFormFields();

        $event = Event::find($this->gen_event_id);

        if (!$event) {
            session()->flash('error', 'Event not found.');
            return;
        }

        $token = 'inv_' . \Illuminate\Support\Str::random(16);
        $isNoDetails = ($this->gen_category === 'no_details');

        \App\Models\EventInvitation::create([
            'uuid' => (string) \Illuminate\Support\Str::uuid(),
            'event_id' => $event->id,
            'email' => $this->gen_email ? trim($this->gen_email) : null,
            'token' => $token,
            'access_role' => $this->gen_access_role,
            'no_details' => $isNoDetails,
            'max_uses' => $this->gen_max_uses ?: 1,
            'use_count' => 0,
            'created_by' => auth()->id(),
        ]);

        $params = ['event_slug' => $event->slug, 'token' => $token];
        if ($isNoDetails) {
            $params['no_details'] = 1;
        }

        $this->generated_invite_url = route('events.public.invite', $params);

        $roleLabel = strtoupper(str_replace('_', ' ', $this->gen_access_role));
        $shareMsg = "🎉 You are invited to *" . $event->name . "*!\n\n🎟️ Access Role: " . $roleLabel . "\n\n🔗 Claim your digital entry pass here:\n" . $this->generated_invite_url;
        $this->generated_whatsapp_url = 'https://api.whatsapp.com/send?text=' . urlencode($shareMsg);

        session()->flash('message', 'Secure single-use invitation link generated!');
    }

    public function mount($eventUuid = null)
    {
        $this->eventUuid = $eventUuid;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingEventUuid()
    {
        $this->resetPage();
    }

    public function updatingRoleFilter()
    {
        $this->resetPage();
    }

    public function openAddModal()
    {
        $this->resetAddForm();

        if ($this->eventUuid) {
            $event = Event::where('uuid', $this->eventUuid)->first();
            if ($event) {
                $this->new_event_id = $event->id;
            }
        } else {
            $firstEvent = Event::first();
            if ($firstEvent) {
                $this->new_event_id = $firstEvent->id;
            }
        }

        $this->showAddModal = true;
    }

    public function closeAddModal()
    {
        $this->showAddModal = false;
        $this->resetAddForm();
    }

    public function viewAttendeeDetails($idOrUuid)
    {
        $this->selectedAttendee = Attendee::with(['event', 'qrCode', 'assignedGate'])
            ->where('uuid', $idOrUuid)
            ->orWhere('id', $idOrUuid)
            ->first();

        if ($this->selectedAttendee) {
            $this->showDetailsModal = true;
        }
    }

    public function openDetailsModal($idOrUuid)
    {
        $this->viewAttendeeDetails($idOrUuid);
    }

    public function closeDetailsModal()
    {
        $this->showDetailsModal = false;
        $this->selectedAttendee = null;
    }

    public function openBulkInviteModal()
    {
        $this->bulk_invite_type = 'form';
        $this->bulk_input_mode = 'emails_only';
        $this->bulk_emails = '';
        $this->bulk_names_emails = '';
        $this->bulk_access_role = 'general_admission';
        $this->bulk_auto_verify = true;

        if ($this->eventUuid) {
            $event = Event::where('uuid', $this->eventUuid)->first();
            if ($event) {
                $this->new_event_id = $event->id;
            }
        } else {
            $firstEvent = Event::first();
            if ($firstEvent) {
                $this->new_event_id = $firstEvent->id;
            }
        }

        $this->showBulkInviteModal = true;
    }

    public function closeBulkInviteModal()
    {
        $this->showBulkInviteModal = false;
        $this->bulk_emails = '';
        $this->bulk_names_emails = '';
        $this->bulk_excel_file = null;
        $this->bulk_uploaded_file_name = '';
        $this->bulk_imported_count = 0;
    }

    /**
     * Livewire Lifecycle hook when an Excel/CSV file is uploaded in the Bulk Invite modal
     */
    public function updatedBulkExcelFile()
    {
        $this->validate([
            'bulk_excel_file' => 'nullable|file|mimes:csv,txt,xlsx,xls|max:10240',
        ]);

        if (!$this->bulk_excel_file) return;

        try {
            $path = $this->bulk_excel_file->getRealPath();
            $ext = strtolower($this->bulk_excel_file->getClientOriginalExtension());
            $origName = $this->bulk_excel_file->getClientOriginalName();

            $rows = $this->parseSpreadsheetRows($path, $ext);
            $extracted = $this->extractRecipientsFromRows($rows);

            if (empty($extracted)) {
                session()->flash('error', "No valid email addresses found in '{$origName}'. Please check the file formatting.");
                $this->bulk_excel_file = null;
                return;
            }

            $emailsList = [];
            $namesEmailsList = [];

            foreach ($extracted as $item) {
                $emailsList[] = $item['email'];
                if (!empty($item['name'])) {
                    $namesEmailsList[] = "{$item['name']}, {$item['email']}";
                } else {
                    $namesEmailsList[] = $item['email'];
                }
            }

            $this->bulk_emails = implode("\n", $emailsList);
            $this->bulk_names_emails = implode("\n", $namesEmailsList);
            $this->bulk_uploaded_file_name = $origName;
            $this->bulk_imported_count = count($extracted);

            session()->flash('success', "📁 Successfully imported {$this->bulk_imported_count} recipient(s) from '{$origName}'!");
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to parse Excel/CSV in Bulk Invite: ' . $e->getMessage());
            session()->flash('error', 'Failed to read file: ' . $e->getMessage());
        }
    }

    public function clearBulkUploadedFile(): void
    {
        $this->bulk_excel_file = null;
        $this->bulk_uploaded_file_name = '';
        $this->bulk_imported_count = 0;
        $this->bulk_emails = '';
        $this->bulk_names_emails = '';
    }

    /**
     * Read raw rows from CSV or XLSX file
     */
    protected function parseSpreadsheetRows(string $filePath, string $extension): array
    {
        $rows = [];
        $ext = strtolower($extension);

        if ($ext === 'csv' || $ext === 'txt') {
            $handle = fopen($filePath, 'r');
            if ($handle) {
                while (($data = fgetcsv($handle, 2000, ',')) !== false) {
                    if (count($data) === 1 && str_contains($data[0], ';')) {
                        $data = str_getcsv($data[0], ';');
                    } elseif (count($data) === 1 && str_contains($data[0], "\t")) {
                        $data = str_getcsv($data[0], "\t");
                    }
                    $rows[] = array_map('trim', $data);
                }
                fclose($handle);
            }
        } elseif ($ext === 'xlsx') {
            $zip = new \ZipArchive();
            if ($zip->open($filePath) === true) {
                $sharedStrings = [];
                if ($zip->locateName('xl/sharedStrings.xml') !== false) {
                    $xml = simplexml_load_string($zip->getFromName('xl/sharedStrings.xml'));
                    if ($xml) {
                        foreach ($xml->si as $val) {
                            $sharedStrings[] = (string)($val->t ?? ($val->r ? $val->r->t : ''));
                        }
                    }
                }

                $sheetContent = $zip->getFromName('xl/worksheets/sheet1.xml');
                if (!$sheetContent) {
                    for ($i = 0; $i < $zip->numFiles; $i++) {
                        $name = $zip->getNameIndex($i);
                        if (str_starts_with($name, 'xl/worksheets/sheet') && str_ends_with($name, '.xml')) {
                            $sheetContent = $zip->getFromIndex($i);
                            break;
                        }
                    }
                }

                if ($sheetContent) {
                    $sheetXml = simplexml_load_string($sheetContent);
                    if ($sheetXml && isset($sheetXml->sheetData->row)) {
                        foreach ($sheetXml->sheetData->row as $rowNode) {
                            $row = [];
                            foreach ($rowNode->c as $cell) {
                                $val = (string)$cell->v;
                                $type = (string)$cell['t'];
                                if ($type === 's' && isset($sharedStrings[(int)$val])) {
                                    $val = $sharedStrings[(int)$val];
                                } elseif ($type === 'inlineStr' && isset($cell->is->t)) {
                                    $val = (string)$cell->is->t;
                                }
                                $row[] = trim($val);
                            }
                            if (!empty(array_filter($row, fn($v) => $v !== ''))) {
                                $rows[] = $row;
                            }
                        }
                    }
                }
                $zip->close();
            }
        }

        return $rows;
    }

    /**
     * Automatically extract names and emails from raw spreadsheet rows
     */
    protected function extractRecipientsFromRows(array $rows): array
    {
        if (empty($rows)) return [];

        $emailCol = -1;
        $nameCol = -1;
        $startIndex = 0;

        // Check first row for header names
        $firstRow = $rows[0];
        foreach ($firstRow as $idx => $cell) {
            $clean = strtolower(trim((string)$cell));
            if (in_array($clean, ['email', 'email address', 'e-mail', 'mail', 'email_address', 'contact email'])) {
                $emailCol = $idx;
            } elseif (in_array($clean, ['name', 'full name', 'fullname', 'attendee name', 'attendee', 'first name', 'guest name', 'full_name'])) {
                $nameCol = $idx;
            }
        }

        if ($emailCol !== -1) {
            $startIndex = 1; // Header found, skip header
        } else {
            // Find which column has valid email addresses
            for ($c = 0; $c < count($firstRow); $c++) {
                if (isset($firstRow[$c]) && preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', trim($firstRow[$c]))) {
                    $emailCol = $c;
                    break;
                }
            }
            if ($emailCol !== -1 && $nameCol === -1) {
                $nameCol = ($emailCol === 0) ? (isset($firstRow[1]) ? 1 : -1) : 0;
            }
        }

        $extracted = [];
        for ($i = $startIndex; $i < count($rows); $i++) {
            $row = $rows[$i];
            if (empty($row)) continue;

            $email = '';
            $name = '';

            if ($emailCol !== -1 && isset($row[$emailCol])) {
                $email = trim((string)$row[$emailCol]);
            } else {
                foreach ($row as $cell) {
                    if (preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', trim((string)$cell))) {
                        $email = trim((string)$cell);
                        break;
                    }
                }
            }

            if ($nameCol !== -1 && isset($row[$nameCol])) {
                $name = trim((string)$row[$nameCol]);
            }

            if ($email && preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $email)) {
                $extracted[] = [
                    'name' => $name,
                    'email' => strtolower($email),
                ];
            }
        }

        return $extracted;
    }

    /**
     * Parse input text into normalized recipient records with name & email
     */
    public function parseBulkRecipients(): array
    {
        $recipients = [];
        $input = ($this->bulk_input_mode === 'names_and_emails') ? $this->bulk_names_emails : $this->bulk_emails;

        if (empty(trim((string)$input))) {
            return [];
        }

        if ($this->bulk_input_mode === 'emails_only') {
            // Comma, semicolon, whitespace, or newline separated emails
            $rawItems = array_filter(array_map('trim', preg_split('/[\r\n,;]+/', (string)$input)));
            foreach ($rawItems as $item) {
                if (preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $item)) {
                    $namePart = explode('@', $item)[0];
                    $fullName = ucwords(str_replace(['.', '_', '-'], ' ', $namePart));
                    $recipients[] = [
                        'email' => strtolower($item),
                        'name' => $fullName,
                        'has_custom_name' => false,
                    ];
                }
            }
        } else {
            // Mode: names_and_emails
            // Split by lines
            $lines = array_filter(array_map('trim', preg_split('/[\r\n]+/', (string)$input)));
            foreach ($lines as $line) {
                $name = '';
                $email = '';

                // Pattern 1: Angled brackets "John Doe <john@example.com>" or "John Doe [john@example.com]"
                if (preg_match('/^(.*?)[<\[(]([a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,})[>\])]$/', $line, $matches)) {
                    $name = trim($matches[1], " \t\n\r\0\x0B,\"-'");
                    $email = strtolower(trim($matches[2]));
                }
                // Pattern 2: Delimited by comma, tab, colon, pipe, or hyphen: "John Doe, john@example.com" or "John Doe\tjohn@example.com"
                elseif (preg_match('/^(.*?)(?:,|\t|:|\||-)\s*([a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,})$/', $line, $matches)) {
                    $name = trim($matches[1], " \t\n\r\0\x0B,\"-'");
                    $email = strtolower(trim($matches[2]));
                }
                // Pattern 3: Email anywhere in line: "john@example.com John Doe"
                elseif (preg_match('/([a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,})/', $line, $matches)) {
                    $email = strtolower(trim($matches[1]));
                    $remaining = trim(str_replace($matches[1], '', $line), " \t\n\r\0\x0B,\"-'<>:;");
                    $name = !empty($remaining) ? $remaining : '';
                }

                if ($email && preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $email)) {
                    $hasCustomName = !empty($name);
                    if (empty($name)) {
                        $namePart = explode('@', $email)[0];
                        $name = ucwords(str_replace(['.', '_', '-'], ' ', $namePart));
                    }
                    $recipients[] = [
                        'email' => $email,
                        'name' => $name,
                        'has_custom_name' => $hasCustomName,
                    ];
                }
            }
        }

        // Deduplicate by email
        $uniqueRecipients = [];
        $seenEmails = [];
        foreach ($recipients as $rec) {
            if (!in_array($rec['email'], $seenEmails)) {
                $seenEmails[] = $rec['email'];
                $uniqueRecipients[] = $rec;
            }
        }

        return $uniqueRecipients;
    }

    /**
     * Analyze parsed recipients against the database for the target event
     */
    public function getBulkRecipientsAnalysis(): array
    {
        $recipients = $this->parseBulkRecipients();
        if (empty($recipients) || empty($this->new_event_id)) {
            return [
                'total' => count($recipients),
                'new' => count($recipients),
                'existing' => 0,
                'existing_emails' => [],
                'new_recipients' => $recipients,
                'existing_recipients' => [],
            ];
        }

        $emails = array_map('strtolower', array_column($recipients, 'email'));
        $existingAttendees = Attendee::where('event_id', $this->new_event_id)
            ->whereIn('email', $emails)
            ->get(['id', 'full_name', 'email', 'access_role', 'verification_status', 'created_at'])
            ->keyBy(fn($item) => strtolower(trim((string)$item->email)));

        $newRecipients = [];
        $existingRecipients = [];

        foreach ($recipients as $r) {
            $emailKey = strtolower($r['email']);
            if (isset($existingAttendees[$emailKey])) {
                $dbAttendee = $existingAttendees[$emailKey];
                $existingRecipients[] = array_merge($r, [
                    'db_id' => $dbAttendee->id,
                    'db_name' => $dbAttendee->full_name,
                    'db_role' => is_object($dbAttendee->access_role) ? $dbAttendee->access_role->label() : ucwords(str_replace('_', ' ', (string)$dbAttendee->access_role)),
                    'db_status' => is_object($dbAttendee->verification_status) ? $dbAttendee->verification_status->label() : ucwords((string)$dbAttendee->verification_status),
                    'db_registered_at' => $dbAttendee->created_at ? $dbAttendee->created_at->format('M d, Y') : 'Prior',
                ]);
            } else {
                $newRecipients[] = $r;
            }
        }

        return [
            'total' => count($recipients),
            'new' => count($newRecipients),
            'existing' => count($existingRecipients),
            'existing_emails' => array_keys($existingAttendees->toArray()),
            'new_recipients' => $newRecipients,
            'existing_recipients' => $existingRecipients,
        ];
    }

    public function toggleDuplicateDetails(): void
    {
        $this->bulk_show_duplicate_details = !$this->bulk_show_duplicate_details;
    }

    public function sendBulkInvitations()
    {
        $event = Event::find($this->new_event_id);
        if (!$event) {
            session()->flash('error', 'Please select a valid event.');
            return;
        }

        $analysis = $this->getBulkRecipientsAnalysis();
        $recipients = $this->parseBulkRecipients();

        if (empty($recipients)) {
            // If both inputs are completely blank, resend invitations to all existing attendees of this event
            $rawInput = ($this->bulk_input_mode === 'names_and_emails') ? $this->bulk_names_emails : $this->bulk_emails;
            if (empty(trim((string)$rawInput))) {
                $attendees = Attendee::where('event_id', $event->id)->get();
                $sentCount = 0;
                foreach ($attendees as $attendee) {
                    // Ensure unique QR pass code exists
                    if (!$attendee->qrCode) {
                        QrCode::create([
                            'uuid' => (string) Str::uuid(),
                            'attendee_id' => $attendee->id,
                            'event_id' => $event->id,
                            'secure_token' => Str::random(32),
                            'issued_at' => now(),
                            'is_revoked' => false,
                        ]);
                        $attendee->load('qrCode');
                    }

                    try {
                        Mail::to($attendee->email)->send(new AttendeePrivateInvitation($attendee, $this->bulk_invite_type));
                        $this->logEmailNotification($attendee, 'delivered');
                        $sentCount++;
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::error("Failed to send invitation to {$attendee->email}: " . $e->getMessage());
                        $this->logEmailNotification($attendee, 'failed', $e->getMessage());
                    }
                }
                session()->flash('success', "Bulk invitations dispatched to {$sentCount} existing attendees with unique security passes.");
                $this->closeBulkInviteModal();
                return;
            } else {
                session()->flash('error', 'No valid email addresses found in the input. Please check the formatting.');
                return;
            }
        }

        // By default, only send to non-duplicates (the new recipients), unless resend to existing is checked
        $processRecipients = $this->bulk_resend_to_existing ? $recipients : $analysis['new_recipients'];

        if (empty($processRecipients)) {
            if ($analysis['existing'] > 0 && !$this->bulk_resend_to_existing) {
                session()->flash('warning', "⚠️ All {$analysis['existing']} recipient(s) are already registered in the database for this event. No new invitations were sent. (To re-send passes to these existing attendees, check the 'Also re-send passes to existing attendees' box).");
            } else {
                session()->flash('error', 'No valid recipients available to invite.');
            }
            $this->closeBulkInviteModal();
            return;
        }

        // Bulk invite recipients
        $newCreatedCount = 0;
        $reInvitedCount = 0;
        $sentCount = 0;

        foreach ($processRecipients as $item) {
            $email = $item['email'];
            $name = $item['name'];
            $hasCustomName = $item['has_custom_name'];

            // Check if attendee exists for this event
            $attendee = Attendee::where('event_id', $event->id)->where('email', $email)->first();

            $isVerified = ($this->bulk_auto_verify || $this->bulk_invite_type === 'direct');

            if (!$attendee) {
                $attendee = Attendee::create([
                    'uuid' => (string) Str::uuid(),
                    'event_id' => $event->id,
                    'organization_id' => $event->organization_id,
                    'full_name' => $name,
                    'email' => $email,
                    'access_role' => $this->bulk_access_role,
                    'verification_status' => $isVerified ? VerificationStatus::Verified : VerificationStatus::Pending,
                    'verified_at' => $isVerified ? now() : null,
                    'consent' => true,
                ]);
                $newCreatedCount++;
            } else {
                // If existing attendee, update name if custom name provided, and update role
                $updateData = [
                    'access_role' => $this->bulk_access_role,
                ];
                if ($hasCustomName) {
                    $updateData['full_name'] = $name;
                }
                if ($isVerified && $attendee->verification_status !== VerificationStatus::Verified) {
                    $updateData['verification_status'] = VerificationStatus::Verified;
                    $updateData['verified_at'] = now();
                }
                $attendee->update($updateData);
                $reInvitedCount++;
            }

            // Generate unique QR Code pass if not existing
            if (!$attendee->qrCode) {
                $token = Str::random(32);
                QrCode::create([
                    'uuid' => (string) Str::uuid(),
                    'attendee_id' => $attendee->id,
                    'event_id' => $event->id,
                    'secure_token' => $token,
                    'encrypted_payload' => base64_encode(json_encode(['token' => $token, 'attendee_uuid' => $attendee->uuid])),
                    'digital_signature' => hash_hmac('sha256', $token, config('app.key')),
                    'issued_at' => now(),
                    'expires_at' => $event->ends_at ? $event->ends_at->addDays(1) : now()->addYear(),
                    'is_revoked' => false,
                ]);
                $attendee->load('qrCode');
            }

            try {
                Mail::to($attendee->email)->send(new AttendeePrivateInvitation($attendee, $this->bulk_invite_type));
                $this->logEmailNotification($attendee, 'delivered');
                $sentCount++;
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Failed to send bulk invitation to {$email}: " . $e->getMessage());
                $this->logEmailNotification($attendee, 'failed', $e->getMessage());
            }
        }

        $flowLabel = ($this->bulk_invite_type === 'direct') ? 'Option B (1-Click Instant Pass)' : 'Option A (Form RSVP)';
        
        if ($newCreatedCount > 0 && $reInvitedCount > 0) {
            session()->flash('success', "🎉 Bulk Invitations Dispatched via {$flowLabel}: {$newCreatedCount} new recipient(s) invited & {$reInvitedCount} existing attendee pass(es) re-sent.");
        } elseif ($newCreatedCount > 0) {
            session()->flash('success', "🎉 Bulk Invitations Dispatched via {$flowLabel}: {$newCreatedCount} new attendee(s) invited.");
        } else {
            session()->flash('success', "🎉 Bulk Invitations Dispatched via {$flowLabel}: {$reInvitedCount} existing attendee pass(es) re-sent & updated.");
        }

        $this->closeBulkInviteModal();
    }

    public function resetAddForm()
    {
        $this->new_full_name = '';
        $this->new_email = '';
        $this->new_phone = '';
        $this->new_access_role = 'general_admission';
        $this->new_verification_status = 'verified';
        $this->auto_generate_qr = true;
        $this->resetErrorBag();
    }

    public function rules()
    {
        return [
            'new_event_id' => 'required|exists:events,id',
            'new_full_name' => 'required|string|min:2|max:255',
            'new_email' => [
                'required',
                'email',
                'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
                'max:255',
                Rule::unique('attendees', 'email')->where(fn ($query) => $query->where('event_id', $this->new_event_id))
            ],
            'new_phone' => [
                'nullable',
                'string',
                'regex:/^[0-9]{10}$/',
                Rule::unique('attendees', 'phone')->where(fn ($query) => $query->where('event_id', $this->new_event_id)->whereNotNull('phone')->where('phone', '!=', ''))
            ],
            'new_access_role' => 'required|string',
            'new_verification_status' => 'required|string',
        ];
    }

    public function messages()
    {
        return [
            'new_event_id.required' => 'Please select an event.',
            'new_full_name.required' => 'Full name is required.',
            'new_email.required' => 'Email address is required.',
            'new_email.email' => 'Please enter a valid email address.',
            'new_email.regex' => 'Please enter a valid email address with a domain extension (e.g. .com, .org).',
            'new_email.unique' => 'This email address is already registered for this event.',
            'new_phone.regex' => 'Phone number must be exactly 10 digits (e.g. 0246345698).',
            'new_phone.unique' => 'This phone number is already registered for this event.',
        ];
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function saveAttendee()
    {
        $this->validate();

        $attendee = Attendee::create([
            'uuid' => (string) \Illuminate\Support\Str::uuid(),
            'event_id' => $this->new_event_id,
            'full_name' => $this->new_full_name,
            'email' => $this->new_email,
            'phone' => $this->new_phone ?: null,
            'access_role' => $this->new_access_role,
            'assigned_gate_id' => $this->new_assigned_gate_id ?: null,
            'verification_status' => $this->new_verification_status,
            'verified_at' => $this->new_verification_status === 'verified' ? now() : null,
            'organization_id' => auth()->user()->organization_id ?? session('current_organization_id'),
            'consent' => true,
        ]);

        if ($this->new_access_role === 'security' || (is_object($this->new_access_role) && $this->new_access_role === AccessRole::Security)) {
            $this->ensureSecurityUserAccount($attendee);
        }

        // Auto-generate QR code if verified or explicitly requested
        if ($this->new_verification_status === 'verified' || $this->auto_generate_qr) {
            QrCode::create([
                'uuid' => (string) \Illuminate\Support\Str::uuid(),
                'attendee_id' => $attendee->id,
                'event_id' => $attendee->event_id,
                'secure_token' => \Illuminate\Support\Str::random(32),
                'issued_at' => now(),
                'is_revoked' => false,
            ]);
        }

        // Send In-App Admin Notification
        try {
            \App\Services\AdminNotificationService::send(
                $attendee->organization_id,
                'New Manual Registration',
                "{$attendee->full_name} was registered manually for '{$attendee->event->name}'.",
                'registration',
                route('attendees.index', $attendee->event->uuid)
            );
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send admin notification: ' . $e->getMessage());
        }

        session()->flash('success', "Attendee '{$attendee->full_name}' manually registered successfully.");
        $this->closeAddModal();
    }

    protected function logEmailNotification(Attendee $attendee, string $status, ?string $errorMessage = null, ?string $batchId = null): void
    {
        try {
            \App\Models\NotificationLog::create([
                'uuid' => (string) Str::uuid(),
                'attendee_id' => $attendee->id,
                'event_id' => $attendee->event_id,
                'user_id' => auth()->id(),
                'channel' => \App\Enums\NotificationChannel::Email,
                'type' => \App\Enums\NotificationType::QrDelivery,
                'status' => $status,
                'sent_at' => in_array($status, ['delivered', 'sent']) ? now() : null,
                'error_message' => $errorMessage,
                'metadata' => [
                    'recipient_email' => $attendee->email,
                    'batch_id' => $batchId,
                ],
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to log email notification: ' . $e->getMessage());
        }
    }

    public function verifyAttendee($uuid)
    {
        $attendee = Attendee::with(['event', 'qrCode'])->where('uuid', $uuid)->first();
        if ($attendee) {
            $attendee->verification_status = VerificationStatus::Verified;
            $attendee->verified_at = now();
            $attendee->save();

            // Create QR Code if missing
            if (!$attendee->qrCode) {
                $token = \Illuminate\Support\Str::random(32);
                QrCode::create([
                    'uuid' => (string) \Illuminate\Support\Str::uuid(),
                    'attendee_id' => $attendee->id,
                    'event_id' => $attendee->event_id,
                    'secure_token' => $token,
                    'encrypted_payload' => base64_encode(json_encode(['token' => $token, 'attendee_uuid' => $attendee->uuid])),
                    'digital_signature' => hash_hmac('sha256', $token, config('app.key')),
                    'issued_at' => now(),
                    'expires_at' => ($attendee->event && $attendee->event->ends_at) ? $attendee->event->ends_at->addDays(1) : now()->addYear(),
                    'is_revoked' => false,
                ]);
                $attendee->load('qrCode');
            }

            // Send Confirmation Email with QR Pass upon Org Admin Approval
            try {
                Mail::to($attendee->email)->send(new \App\Mail\EventRegistrationConfirmation($attendee));
                $this->logEmailNotification($attendee, 'delivered');
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Failed to send approval confirmation email: ' . $e->getMessage());
                $this->logEmailNotification($attendee, 'failed', $e->getMessage());
            }

            session()->flash('success', "Attendee '{$attendee->full_name}' approved & verified! Official QR pass dispatched via email.");
        }
    }

    public function bulkApproveAttendees()
    {
        if (empty($this->selectedAttendees)) return;

        $attendees = Attendee::with(['event', 'qrCode'])->whereIn('uuid', $this->selectedAttendees)->get();
        $this->processApproveAndEmail($attendees);
    }

    /**
     * Approve ALL attendees matching the current filters (across all pages)
     */
    public function approveAllFilteredAttendees()
    {
        $attendees = $this->getFilteredAttendeesQuery()->with(['event', 'qrCode'])->get();

        if ($attendees->isEmpty()) {
            session()->flash('warning', 'No attendees found matching the current filters.');
            return;
        }

        $this->processApproveAndEmail($attendees);
    }

    /**
     * Core method: approve attendees, generate QR codes, and start progressive email dispatch
     */
    protected function processApproveAndEmail($attendees)
    {
        // 1. Fast Database Update: Mark all attendees verified & generate any missing QR codes
        foreach ($attendees as $attendee) {
            if ($attendee->verification_status !== VerificationStatus::Verified) {
                $attendee->verification_status = VerificationStatus::Verified;
                $attendee->verified_at = now();
                $attendee->save();
            }

            if (!$attendee->qrCode) {
                $token = \Illuminate\Support\Str::random(32);
                QrCode::create([
                    'uuid' => (string) \Illuminate\Support\Str::uuid(),
                    'attendee_id' => $attendee->id,
                    'event_id' => $attendee->event_id,
                    'secure_token' => $token,
                    'encrypted_payload' => base64_encode(json_encode(['token' => $token, 'attendee_uuid' => $attendee->uuid])),
                    'digital_signature' => hash_hmac('sha256', $token, config('app.key')),
                    'issued_at' => now(),
                    'expires_at' => ($attendee->event && $attendee->event->ends_at) ? $attendee->event->ends_at->addDays(1) : now()->addYear(),
                    'is_revoked' => false,
                ]);
            }
        }

        // 2. Setup Progressive Dispatch Batch
        $allUuids = $attendees->pluck('uuid')->toArray();
        $this->pendingBatchUuids = $allUuids;
        $this->batchTotalCount = count($allUuids);
        $this->batchProcessedCount = 0;
        $this->approvedTotalCount = $this->batchTotalCount;
        $this->emailSuccessCount = 0;
        $this->emailFailedCount = 0;
        $this->emailDeliveryResults = [];
        $this->currentBatchId = (string) Str::uuid();
        $this->isProcessingBatch = true;
        $this->showEmailReportModal = true;

        // Reset selection state
        $this->selectedAttendees = [];
        $this->selectAllOnPage = false;
        $this->selectAll = false;

        // Note: Livewire will render the modal with wire:poll.300ms="processNextEmailChunk"
        // and immediately begin processing chunk by chunk without blocking the initial HTTP request.
    }

    /**
     * Process next chunk of emails progressively (prevents 504 Gateway Timeout and handles 451 rate limits)
     */
    public function processNextEmailChunk(): void
    {
        if (!$this->isProcessingBatch || empty($this->pendingBatchUuids)) {
            $this->isProcessingBatch = false;
            return;
        }

        $chunkUuids = array_splice($this->pendingBatchUuids, 0, $this->batchChunkSize);
        $attendees = Attendee::with(['event', 'qrCode'])->whereIn('uuid', $chunkUuids)->get();

        foreach ($attendees as $index => $attendee) {
            // Adaptive pause between sends (300ms) to respect SMTP rate limits
            if ($index > 0) {
                usleep(300000);
            }

            $sent = false;
            $lastError = null;

            for ($attempt = 1; $attempt <= 2; $attempt++) {
                try {
                    Mail::to($attendee->email)->send(new \App\Mail\EventRegistrationConfirmation($attendee));
                    $this->logEmailNotification($attendee, 'delivered', null, $this->currentBatchId);
                    $this->emailDeliveryResults[] = [
                        'uuid' => $attendee->uuid,
                        'name' => $attendee->full_name,
                        'email' => $attendee->email,
                        'status' => 'success',
                        'error' => null,
                    ];
                    $this->emailSuccessCount++;
                    $sent = true;
                    break;
                } catch (\Exception $e) {
                    $lastError = $e->getMessage();
                    // If rate limited by SMTP (e.g. Mailtrap 451 4.7.1 Ratelimit / 550 Too many emails), pause 2s and retry once
                    if ($attempt === 1 && (str_contains($lastError, 'Ratelimit') || str_contains($lastError, '451') || str_contains($lastError, '550') || str_contains($lastError, 'Too many emails'))) {
                        sleep(2);
                        continue;
                    }
                }
            }

            if (!$sent) {
                \Illuminate\Support\Facades\Log::error("Failed to send bulk approval email to {$attendee->email}: " . $lastError);
                $this->logEmailNotification($attendee, 'failed', $lastError, $this->currentBatchId);
                $this->emailDeliveryResults[] = [
                    'uuid' => $attendee->uuid,
                    'name' => $attendee->full_name,
                    'email' => $attendee->email,
                    'status' => 'failed',
                    'error' => $lastError,
                ];
                $this->emailFailedCount++;
            }

            $this->batchProcessedCount++;
        }

        if (empty($this->pendingBatchUuids)) {
            $this->isProcessingBatch = false;
            session()->flash('success', "🎉 Bulk pass issuance complete for {$this->approvedTotalCount} attendee(s): {$this->emailSuccessCount} sent, {$this->emailFailedCount} failed.");
        }
    }

    /**
     * Close the email delivery report modal
     */
    public function closeEmailReportModal()
    {
        $this->showEmailReportModal = false;
        $this->isProcessingBatch = false;
        $this->pendingBatchUuids = [];
        $this->emailDeliveryResults = [];
        $this->emailSuccessCount = 0;
        $this->emailFailedCount = 0;
        $this->approvedTotalCount = 0;
        $this->batchTotalCount = 0;
        $this->batchProcessedCount = 0;
    }

    /**
     * Retry sending emails to attendees that previously failed
     */
    public function retryFailedEmails()
    {
        $failedUuids = collect($this->emailDeliveryResults)
            ->where('status', 'failed')
            ->pluck('uuid')
            ->toArray();

        if (empty($failedUuids)) {
            session()->flash('info', 'No failed emails to retry.');
            return;
        }

        // Keep successful results, clear failed to re-accumulate
        $this->emailDeliveryResults = collect($this->emailDeliveryResults)
            ->where('status', 'success')
            ->values()
            ->toArray();

        $this->pendingBatchUuids = $failedUuids;
        $this->batchTotalCount = count($failedUuids);
        $this->batchProcessedCount = 0;
        $this->emailFailedCount = 0;
        $this->isProcessingBatch = true;
        $this->currentBatchId = (string) Str::uuid();
    }

    /**
     * Reset Option 1: Reset Delivery Logs Only (Leaves attendee QR codes and status intact)
     */
    public function clearDeliveryLogsOnly(): void
    {
        $attendeeIds = $this->getFilteredAttendeesQuery()->pluck('id')->toArray();
        if (!empty($attendeeIds)) {
            \App\Models\NotificationLog::whereIn('attendee_id', $attendeeIds)->delete();
        }

        if ($this->eventUuid) {
            $event = Event::where('uuid', $this->eventUuid)->first();
            if ($event) {
                \App\Models\NotificationLog::where('event_id', $event->id)->delete();
            }
        }

        $this->emailDeliveryResults = [];
        $this->emailSuccessCount = 0;
        $this->emailFailedCount = 0;
        $this->resetPage();
        session()->flash('success', '🧹 Email & QR delivery logs have been cleared. Attendee QR passes and statuses remain intact.');
    }

    /**
     * Reset Option 2: Full Reset: Clear Delivery Logs AND Reset Attendee Verification & QR Codes for fresh testing
     */
    public function fullResetLogsAndAttendeeStatus(): void
    {
        $attendeeQuery = $this->getFilteredAttendeesQuery();
        $attendeeIds = (clone $attendeeQuery)->pluck('id')->toArray();

        if (!empty($attendeeIds)) {
            // 1. Delete notification logs for these attendees
            \App\Models\NotificationLog::whereIn('attendee_id', $attendeeIds)->delete();

            // 2. Delete QR codes for these attendees
            QrCode::whereIn('attendee_id', $attendeeIds)->delete();

            // 3. Reset attendee verification statuses to Pending
            Attendee::whereIn('id', $attendeeIds)->update([
                'verification_status' => VerificationStatus::Pending,
                'verified_at' => null,
            ]);
        }

        if ($this->eventUuid) {
            $event = Event::where('uuid', $this->eventUuid)->first();
            if ($event) {
                \App\Models\NotificationLog::where('event_id', $event->id)->delete();
                QrCode::where('event_id', $event->id)->delete();
                Attendee::where('event_id', $event->id)->update([
                    'verification_status' => VerificationStatus::Pending,
                    'verified_at' => null,
                ]);
            }
        }

        $this->emailDeliveryResults = [];
        $this->emailSuccessCount = 0;
        $this->emailFailedCount = 0;
        $this->selectedAttendees = [];
        $this->selectAll = false;
        $this->selectAllOnPage = false;
        $this->resetPage();
        session()->flash('success', '🔄 Full Reset Complete: All delivery logs cleared, QR passes cleared, and attendees reset to Pending for fresh testing.');
    }

    public function resendPassEmail($uuid = null)
    {
        $targetUuid = $uuid ?: ($this->selectedAttendee->uuid ?? null);
        if (!$targetUuid) return;

        $attendee = Attendee::with(['event', 'qrCode'])->where('uuid', $targetUuid)->first();
        if (!$attendee) return;

        // Ensure QR code exists
        if (!$attendee->qrCode) {
            $token = \Illuminate\Support\Str::random(32);
            QrCode::create([
                'uuid' => (string) \Illuminate\Support\Str::uuid(),
                'attendee_id' => $attendee->id,
                'event_id' => $attendee->event_id,
                'secure_token' => $token,
                'encrypted_payload' => base64_encode(json_encode(['token' => $token, 'attendee_uuid' => $attendee->uuid])),
                'digital_signature' => hash_hmac('sha256', $token, config('app.key')),
                'issued_at' => now(),
                'expires_at' => $attendee->event->ends_at ? $attendee->event->ends_at->addDays(1) : now()->addYear(),
                'is_revoked' => false,
            ]);
            $attendee->load('qrCode');
        }

        try {
            Mail::to($attendee->email)->send(new \App\Mail\EventRegistrationConfirmation($attendee));
            $this->logEmailNotification($attendee, 'delivered');
            session()->flash('message', "Pass email re-sent successfully to {$attendee->email}!");
            if ($this->selectedAttendee && $this->selectedAttendee->uuid === $targetUuid) {
                $this->selectedAttendee = $attendee;
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Failed to resend pass email to {$attendee->email}: " . $e->getMessage());
            $this->logEmailNotification($attendee, 'failed', $e->getMessage());
            session()->flash('error', "Could not send email: " . $e->getMessage());
        }
    }

    public function deleteAttendee($uuid)
    {
        $attendee = Attendee::where('uuid', $uuid)->first();
        if ($attendee) {
            DB::transaction(function () use ($attendee) {
                \App\Models\CheckIn::where('attendee_id', $attendee->id)->delete();
                \App\Models\QrCode::where('attendee_id', $attendee->id)->delete();
                \App\Models\NotificationLog::where('attendee_id', $attendee->id)->delete();
                $attendee->forceDelete();
            });
            session()->flash('success', 'Attendee permanently deleted from the database.');
        }
    }

    public function toggleAttendeeRole($uuid, $newRole)
    {
        $attendee = Attendee::where('uuid', $uuid)->first();
        if ($attendee) {
            $attendee->access_role = $newRole;
            $attendee->save();
            session()->flash('success', "Role updated to '" . AccessRole::from($newRole)->label() . "' for {$attendee->full_name}.");
        }
    }

    public function sendWhatsAppPass($uuid = null)
    {
        $targetUuid = $uuid ?: ($this->selectedAttendee->uuid ?? null);
        if (!$targetUuid) return;

        $attendee = Attendee::with(['event', 'qrCode', 'notificationLogs'])->where('uuid', $targetUuid)->first();
        if (!$attendee) return;

        // Ensure QR code exists
        if (!$attendee->qrCode) {
            $token = \Illuminate\Support\Str::random(32);
            QrCode::create([
                'uuid' => (string) \Illuminate\Support\Str::uuid(),
                'attendee_id' => $attendee->id,
                'event_id' => $attendee->event_id,
                'secure_token' => $token,
                'encrypted_payload' => base64_encode(json_encode(['token' => $token, 'attendee_uuid' => $attendee->uuid])),
                'digital_signature' => hash_hmac('sha256', $token, config('app.key')),
                'issued_at' => now(),
                'expires_at' => ($attendee->event && $attendee->event->ends_at) ? $attendee->event->ends_at->addDays(1) : now()->addYear(),
                'is_revoked' => false,
            ]);
            $attendee->load('qrCode');
        }

        $result = \App\Services\WhatsAppDispatchService::dispatchQrPass($attendee);

        if ($this->selectedAttendee && $this->selectedAttendee->uuid === $targetUuid) {
            $this->selectedAttendee = $attendee->fresh(['event', 'qrCode', 'notificationLogs']);
        }

        if ($result['success']) {
            session()->flash('success', "📱 WhatsApp QR Pass message dispatched for {$attendee->full_name}!");
            $this->js("window.open('{$result['url']}', '_blank')");
        } else {
            session()->flash('error', "⚠️ WhatsApp Dispatch Warning: {$result['message']}");
        }
    }

    public function markWhatsAppFailed(string $uuid, string $reason = 'Number not on WhatsApp'): void
    {
        $attendee = Attendee::where('uuid', $uuid)->first();
        if (!$attendee) return;

        \App\Models\NotificationLog::create([
            'uuid' => (string) \Illuminate\Support\Str::uuid(),
            'attendee_id' => $attendee->id,
            'event_id' => $attendee->event_id,
            'user_id' => auth()->id(),
            'channel' => \App\Enums\NotificationChannel::WhatsApp->value,
            'type' => \App\Enums\NotificationType::QrDelivery->value,
            'status' => 'failed',
            'error_message' => $reason ?: 'Number not registered on WhatsApp',
            'metadata' => [
                'recipient_phone' => $attendee->phone,
                'manual_override' => true,
            ],
        ]);

        if ($this->selectedAttendee && $this->selectedAttendee->uuid === $uuid) {
            $this->selectedAttendee = $attendee->fresh(['event', 'qrCode', 'notificationLogs']);
        }

        session()->flash('warning', "Status updated to 'WhatsApp Failed' for {$attendee->full_name}.");
    }

    public function markWhatsAppSent(string $uuid): void
    {
        $attendee = Attendee::where('uuid', $uuid)->first();
        if (!$attendee) return;

        \App\Models\NotificationLog::create([
            'uuid' => (string) \Illuminate\Support\Str::uuid(),
            'attendee_id' => $attendee->id,
            'event_id' => $attendee->event_id,
            'user_id' => auth()->id(),
            'channel' => \App\Enums\NotificationChannel::WhatsApp->value,
            'type' => \App\Enums\NotificationType::QrDelivery->value,
            'status' => 'delivered',
            'sent_at' => now(),
            'error_message' => null,
            'metadata' => [
                'recipient_phone' => $attendee->phone,
                'manual_override' => true,
            ],
        ]);

        if ($this->selectedAttendee && $this->selectedAttendee->uuid === $uuid) {
            $this->selectedAttendee = $attendee->fresh(['event', 'qrCode', 'notificationLogs']);
        }

        session()->flash('success', "Status updated to 'WhatsApp Sent' for {$attendee->full_name}.");
    }

    public function getFilteredAttendeesQuery()
    {
        $query = Attendee::whereHas('event')
            ->with(['event', 'qrCode', 'assignedGate', 'latestCheckIn.gate', 'latestCheckIn.scanner', 'notificationLogs']);

        $isSuperAdmin = auth()->user()->hasRole('super_admin') || auth()->user()->email === 'superadmin@attendflow.com';
        if (!$isSuperAdmin && auth()->user()->organization_id) {
            $query->whereHas('event', fn($q) => $q->where('organization_id', auth()->user()->organization_id));
        }

        if ($this->eventUuid) {
            $event = Event::where('uuid', $this->eventUuid)->first();
            if ($event) {
                $query->where('event_id', $event->id);
            }
        }

        if ($this->search) {
            $query->where(function($q) {
                $q->where('full_name', 'like', '%'.$this->search.'%')
                  ->orWhere('email', 'like', '%'.$this->search.'%');
            });
        }

        if ($this->statusFilter !== '') {
            $query->where('verification_status', $this->statusFilter);
        }

        if ($this->roleFilter) {
            $query->where('access_role', $this->roleFilter);
        }

        if ($this->categoryFilter === 'no_details') {
            $query->where(function($q) {
                $q->where('email', 'like', '%@attendflow.pass')
                  ->orWhere('phone', 'like', '000%')
                  ->orWhere('full_name', 'like', '%Guest Pass%');
            });
        } elseif ($this->categoryFilter === 'details') {
            $query->where('email', 'not like', '%@attendflow.pass')
                  ->where('phone', 'not like', '000%')
                  ->where('full_name', 'not like', '%Guest Pass%');
        }

        return $query;
    }

    public function updatedSelectAllOnPage($value)
    {
        if ($value) {
            $pageUuids = $this->getFilteredAttendeesQuery()
                ->latest()
                ->paginate($this->perPage, ['*'], 'page', $this->getPage())
                ->pluck('uuid')
                ->map(fn($uuid) => (string) $uuid)
                ->toArray();

            $this->selectedAttendees = array_values(array_unique(array_merge($this->selectedAttendees, $pageUuids)));
        } else {
            $pageUuids = $this->getFilteredAttendeesQuery()
                ->latest()
                ->paginate($this->perPage, ['*'], 'page', $this->getPage())
                ->pluck('uuid')
                ->map(fn($uuid) => (string) $uuid)
                ->toArray();

            $this->selectedAttendees = array_values(array_diff($this->selectedAttendees, $pageUuids));
        }
        $this->selectAll = $value;
    }

    public function selectAllFilteredAttendees(): void
    {
        $this->selectedAttendees = $this->getFilteredAttendeesQuery()
            ->pluck('uuid')
            ->map(fn($uuid) => (string) $uuid)
            ->toArray();
        $this->selectAll = true;
        $this->selectAllOnPage = true;
    }

    public function bulkDeleteAttendees(): void
    {
        if (empty($this->selectedAttendees)) return;

        $attendees = Attendee::whereIn('uuid', $this->selectedAttendees)->get();
        $count = $attendees->count();
        $attendeeIds = $attendees->pluck('id')->toArray();

        if (!empty($attendeeIds)) {
            DB::transaction(function () use ($attendeeIds) {
                \App\Models\CheckIn::whereIn('attendee_id', $attendeeIds)->delete();
                \App\Models\QrCode::whereIn('attendee_id', $attendeeIds)->delete();
                \App\Models\NotificationLog::whereIn('attendee_id', $attendeeIds)->delete();
                Attendee::whereIn('id', $attendeeIds)->forceDelete();
            });
        }

        $this->selectedAttendees = [];
        $this->selectAllOnPage = false;
        $this->selectAll = false;
        session()->flash('success', "{$count} attendee(s) permanently deleted from the database.");
    }

    public function deleteAllFilteredAttendees(): void
    {
        $attendees = $this->getFilteredAttendeesQuery()->get();
        $count = $attendees->count();
        if ($count === 0) return;

        $attendeeIds = $attendees->pluck('id')->toArray();

        if (!empty($attendeeIds)) {
            DB::transaction(function () use ($attendeeIds) {
                \App\Models\CheckIn::whereIn('attendee_id', $attendeeIds)->delete();
                \App\Models\QrCode::whereIn('attendee_id', $attendeeIds)->delete();
                \App\Models\NotificationLog::whereIn('attendee_id', $attendeeIds)->delete();
                Attendee::whereIn('id', $attendeeIds)->forceDelete();
            });
        }

        $this->selectedAttendees = [];
        $this->selectAllOnPage = false;
        $this->selectAll = false;
        session()->flash('success', "All {$count} attendee(s) in the table have been permanently deleted from the database.");
    }

    public function bulkChangeRole($newRole)
    {
        if (empty($this->selectedAttendees)) return;

        $count = Attendee::whereIn('uuid', $this->selectedAttendees)->update(['access_role' => $newRole]);

        $this->selectedAttendees = [];
        $this->selectAllOnPage = false;
        $this->selectAll = false;
        session()->flash('success', "{$count} attendee(s) updated to '" . AccessRole::from($newRole)->label() . "'.");
    }

    public function revokeQr($uuid)
    {
        $attendee = Attendee::where('uuid', $uuid)->first();
        if ($attendee) {
            QrCode::where('attendee_id', $attendee->id)->update(['is_revoked' => true]);
            session()->flash('success', 'QR code revoked.');
        }
    }

    public function resendVerification($uuid)
    {
        session()->flash('success', 'Verification email resent.');
    }

    public function export()
    {
        if (!empty($this->selectedAttendees)) {
            $attendees = Attendee::with(['event', 'latestCheckIn', 'qrCode'])
                ->whereIn('uuid', $this->selectedAttendees)
                ->latest()
                ->get();
        } else {
            $attendees = $this->getFilteredAttendeesQuery()
                ->with(['event', 'latestCheckIn', 'qrCode'])
                ->latest()
                ->get();
        }

        if ($attendees->isEmpty()) {
            session()->flash('warning', 'No attendees found to export with the current filters.');
            return null;
        }

        // Collect custom extra fields from events / metadata
        $customFieldHeaders = [];
        foreach ($attendees as $att) {
            if ($att->event && !empty($att->event->form_fields_config['custom_fields'])) {
                foreach ($att->event->form_fields_config['custom_fields'] as $cf) {
                    $cId = $cf['id'] ?? ($cf['label'] ?? '');
                    $cLabel = $cf['label'] ?? $cId;
                    if ($cId && !isset($customFieldHeaders[$cId])) {
                        $customFieldHeaders[$cId] = $cLabel;
                    }
                }
            }
            if (is_array($att->metadata)) {
                foreach ($att->metadata as $mKey => $mVal) {
                    if (!isset($customFieldHeaders[$mKey])) {
                        $customFieldHeaders[$mKey] = Str::headline($mKey);
                    }
                }
            }
        }

        $eventName = 'attendees';
        if ($this->eventUuid) {
            $selectedEvent = Event::where('uuid', $this->eventUuid)->first();
            if ($selectedEvent) {
                $eventName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $selectedEvent->name);
            }
        }

        $fileName = "{$eventName}_export_" . date('Y-m-d_His') . '.csv';

        return response()->streamDownload(function () use ($attendees, $customFieldHeaders) {
            $handle = fopen('php://output', 'w');

            // UTF-8 BOM for Microsoft Excel compatibility
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            $baseHeaders = [
                'Full Name',
                'Email Address',
                'Phone Number',
                'Event Name',
                'Access Role',
                'Verification Status',
                'Check-In Status',
                'Checked-In At',
                'Company / Organization',
                'Job Title',
                'Country',
                'Gender',
                'Emergency Contact Name',
                'Emergency Contact Phone',
                'Dietary Preferences',
                'Accessibility Needs',
                'Registration Reason',
                'Registration Date',
            ];

            $allHeaders = array_merge($baseHeaders, array_values($customFieldHeaders));
            fputcsv($handle, $allHeaders);

            foreach ($attendees as $attendee) {
                $roleLabel = is_object($attendee->access_role) ? $attendee->access_role->label() : ($attendee->access_role ?? 'General Admission');
                $statusLabel = is_object($attendee->verification_status) ? $attendee->verification_status->value : ($attendee->verification_status ?? 'verified');
                $isCheckedIn = $attendee->latestCheckIn && (is_object($attendee->latestCheckIn->scan_result) ? $attendee->latestCheckIn->scan_result->value === 'granted' : $attendee->latestCheckIn->scan_result === 'granted');
                $checkInTime = ($isCheckedIn && $attendee->latestCheckIn->scanned_at) ? $attendee->latestCheckIn->scanned_at->format('Y-m-d H:i:s') : 'N/A';

                $row = [
                    $attendee->full_name,
                    $attendee->email,
                    $attendee->phone ?? '',
                    $attendee->event->name ?? 'N/A',
                    $roleLabel,
                    ucfirst((string) $statusLabel),
                    $isCheckedIn ? 'Checked In' : 'Not Checked In',
                    $checkInTime,
                    $attendee->company ?? '',
                    $attendee->job_title ?? '',
                    $attendee->country ?? '',
                    $attendee->gender ?? '',
                    $attendee->emergency_contact_name ?? '',
                    $attendee->emergency_contact_phone ?? '',
                    $attendee->dietary_preferences ?? '',
                    $attendee->accessibility_needs ?? '',
                    $attendee->registration_reason ?? '',
                    $attendee->created_at ? $attendee->created_at->format('Y-m-d H:i:s') : '',
                ];

                // Append any custom extra fields
                foreach (array_keys($customFieldHeaders) as $cKey) {
                    $val = '';
                    if (is_array($attendee->metadata)) {
                        $val = $attendee->metadata[$cKey] ?? '';
                        if (is_array($val)) {
                            $val = implode(', ', $val);
                        }
                    }
                    $row[] = (string) $val;
                }

                fputcsv($handle, $row);
            }

            fclose($handle);
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ]);
    }

    // ─── CSV Import Methods ─────────────────────────────────────

    private const HEADER_FIELD_MAP = [
        'Full Name' => 'full_name',
        'Email Address' => 'email',
        'Phone Number' => 'phone',
        'Company / Organization' => 'company',
        'Job Title' => 'job_title',
        'Country' => 'country',
        'Gender' => 'gender',
        'Emergency Contact Name' => 'emergency_contact_name',
        'Emergency Contact Phone' => 'emergency_contact_phone',
        'Dietary Preferences' => 'dietary_preferences',
        'Accessibility Needs' => 'accessibility_needs',
        'Registration Reason' => 'registration_reason',
        'Role' => 'access_role',
        'Verification Status' => 'verification_status',
    ];

    private const FIELD_LABEL_MAP = [
        'full_name' => 'Full Name',
        'email' => 'Email Address',
        'phone' => 'Phone Number',
        'company' => 'Company / Organization',
        'job_title' => 'Job Title',
        'country' => 'Country',
        'gender' => 'Gender',
        'emergency_contact_name' => 'Emergency Contact Name',
        'emergency_contact_phone' => 'Emergency Contact Phone',
        'dietary_preferences' => 'Dietary Preferences',
        'accessibility_needs' => 'Accessibility Needs',
        'registration_reason' => 'Registration Reason',
    ];

    public function openImportCsvModal(): void
    {
        $this->csv_file = null;
        $this->importResults = [];
        $this->importEventFields = [];

        if ($this->eventUuid) {
            $event = Event::where('uuid', $this->eventUuid)->first();
            $this->import_event_id = $event ? (string) $event->id : '';
        } else {
            $firstEvent = Event::first();
            $this->import_event_id = $firstEvent ? (string) $firstEvent->id : '';
        }

        $this->loadImportEventFields();
        $this->showImportCsvModal = true;
    }

    public function closeImportCsvModal(): void
    {
        $this->showImportCsvModal = false;
        $this->csv_file = null;
        $this->importResults = [];
        $this->import_event_id = '';
        $this->importEventFields = [];
    }

    public function updatedImportEventId($value): void
    {
        $this->importResults = [];
        $this->csv_file = null;
        $this->loadImportEventFields();
    }

    private function loadImportEventFields(): void
    {
        if (empty($this->import_event_id)) {
            $this->importEventFields = [];
            return;
        }

        $event = Event::find($this->import_event_id);
        if (!$event) {
            $this->importEventFields = [];
            return;
        }

        $config = $event->form_fields_config;
        $fields = [];

        foreach ($config['standard_fields'] as $key => $status) {
            if ($status !== 'disabled') {
                $fields[] = [
                    'key' => $key,
                    'label' => self::FIELD_LABEL_MAP[$key] ?? ucwords(str_replace('_', ' ', $key)),
                    'status' => $status,
                    'type' => 'standard',
                ];
            }
        }

        foreach ($config['custom_fields'] as $customField) {
            if (!empty(trim($customField['label'] ?? ''))) {
                $fields[] = [
                    'key' => $customField['id'] ?? $customField['label'],
                    'label' => $customField['label'],
                    'status' => ($customField['required'] ?? false) ? 'required' : 'optional',
                    'type' => 'custom',
                ];
            }
        }

        $this->importEventFields = $fields;
    }

    public function downloadCsvTemplate()
    {
        if (empty($this->import_event_id)) {
            session()->flash('error', 'Please select an event first.');
            return;
        }

        $event = Event::find($this->import_event_id);
        if (!$event) {
            session()->flash('error', 'Event not found.');
            return;
        }

        $config = $event->form_fields_config;
        $headers = [];
        $exampleRow = [];

        // Add non-disabled standard fields
        foreach ($config['standard_fields'] as $key => $status) {
            if ($status !== 'disabled') {
                $label = self::FIELD_LABEL_MAP[$key] ?? ucwords(str_replace('_', ' ', $key));
                $headers[] = $label;
                $exampleRow[] = $this->getExampleValue($key, $status);
            }
        }

        // Add custom fields
        foreach ($config['custom_fields'] as $customField) {
            if (!empty(trim($customField['label'] ?? ''))) {
                $headers[] = $customField['label'];
                $exampleRow[] = '';
            }
        }

        // Always include Role and Verification Status
        if (!in_array('Role', $headers)) {
            $headers[] = 'Role';
            $exampleRow[] = 'general_admission';
        }
        if (!in_array('Verification Status', $headers)) {
            $headers[] = 'Verification Status';
            $exampleRow[] = 'verified';
        }

        $safeEventName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $event->name);
        $fileName = "import_template_{$safeEventName}_" . date('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($headers, $exampleRow) {
            $handle = fopen('php://output', 'w');
            // Add BOM for Excel compatibility
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($handle, $headers);
            fputcsv($handle, $exampleRow);
            fclose($handle);
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ]);
    }

    /**
     * Normalizes phone number from CSV import (restoring leading 0 stripped by Excel)
     * into a standard 10-digit Ghana format (0XXXXXXXXX) or valid international digits.
     */
    private function normalizePhoneNumber(?string $phone): ?string
    {
        if (empty($phone)) {
            return null;
        }

        // Strip all non-digits
        $digits = preg_replace('/[^0-9]/', '', (string)$phone);

        if (empty($digits)) {
            return null;
        }

        // Case 1: Excel stripped leading 0 for 9-digit local numbers (e.g. 547977840, 243036092)
        if (strlen($digits) === 9) {
            return '0' . $digits;
        }

        // Case 2: International Ghana number starting with 233 (e.g. 233547977840 -> 0547977840)
        if (strlen($digits) === 12 && str_starts_with($digits, '233')) {
            return '0' . substr($digits, 3);
        }

        // Case 3: Already 10-digit standard local number (e.g. 0547977840)
        if (strlen($digits) === 10) {
            return $digits;
        }

        return $digits;
    }

    private function getExampleValue(string $fieldKey, string $status): string
    {
        $suffix = $status === 'required' ? ' (required)' : ' (optional)';
        return match ($fieldKey) {
            'full_name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '0241234567',
            'company' => 'Acme Inc',
            'job_title' => 'Manager',
            'country' => 'Ghana',
            'gender' => 'Male',
            'emergency_contact_name' => 'Jane Doe',
            'emergency_contact_phone' => '0209876543',
            'dietary_preferences' => 'None',
            'accessibility_needs' => 'None',
            'registration_reason' => 'Networking',
            default => '',
        };
    }

    public function importCsv(): void
    {
        if (empty($this->import_event_id)) {
            session()->flash('error', 'Please select an event.');
            return;
        }

        if (!$this->csv_file) {
            session()->flash('error', 'Please upload a CSV file.');
            return;
        }

        $event = Event::find($this->import_event_id);
        if (!$event) {
            session()->flash('error', 'Event not found.');
            return;
        }

        $config = $event->form_fields_config;
        $requiredFields = [];
        foreach ($config['standard_fields'] as $key => $status) {
            if ($status === 'required') {
                $requiredFields[] = $key;
            }
        }

        // Parse CSV using fgetcsv to properly handle quoted multi-line cells and exact row numbering
        $path = $this->csv_file->getRealPath();
        $handle = fopen($path, 'r');
        if (!$handle) {
            session()->flash('error', 'Unable to open CSV file.');
            return;
        }

        // Read header row
        $rawHeader = fgetcsv($handle);
        if (!$rawHeader || empty(array_filter($rawHeader, fn($h) => trim((string)$h) !== ''))) {
            fclose($handle);
            session()->flash('error', 'CSV file must contain a valid header row.');
            return;
        }

        // Remove UTF-8 BOM from the first header column if present
        $rawHeader[0] = preg_replace('/^\x{FEFF}/u', '', (string)$rawHeader[0]);
        $csvHeaders = array_map('trim', $rawHeader);

        // Build column index map: CSV column index => db field or custom field key
        $columnMap = [];
        $customFieldLabels = [];
        foreach ($config['custom_fields'] as $cf) {
            if (!empty(trim($cf['label'] ?? ''))) {
                $customFieldLabels[strtolower(trim($cf['label']))] = $cf['id'] ?? $cf['label'];
            }
        }

        foreach ($csvHeaders as $index => $header) {
            $normalizedHeader = trim($header);
            // Check standard field map
            if (isset(self::HEADER_FIELD_MAP[$normalizedHeader])) {
                $columnMap[$index] = ['type' => 'standard', 'field' => self::HEADER_FIELD_MAP[$normalizedHeader]];
            }
            // Check custom fields (case-insensitive)
            elseif (isset($customFieldLabels[strtolower($normalizedHeader)])) {
                $columnMap[$index] = ['type' => 'custom', 'field' => $customFieldLabels[strtolower($normalizedHeader)], 'label' => $normalizedHeader];
            }
        }

        $imported = 0;
        $skipped = 0;
        $skipReasons = [];
        $errors = [];
        $organizationId = $event->organization_id;
        $validRoles = array_column(AccessRole::cases(), 'value');
        $validStatuses = array_column(VerificationStatus::cases(), 'value');

        // Track seen emails and phones within the CSV to prevent intra-file duplicates
        $seenEmails = [];
        $seenPhones = [];

        $rowNumber = 1; // Header is row 1
        $totalRowsProcessed = 0;

        while (($values = fgetcsv($handle)) !== false) {
            $rowNumber++;

            // Skip entirely blank rows (e.g. trailing empty rows in spreadsheet exports)
            if (empty(array_filter($values, fn($v) => trim((string)$v) !== ''))) {
                continue;
            }

            $totalRowsProcessed++;

            $rowData = [];
            $customData = [];

            foreach ($columnMap as $colIndex => $mapping) {
                $value = isset($values[$colIndex]) ? trim((string)$values[$colIndex]) : '';
                if ($mapping['type'] === 'standard') {
                    $rowData[$mapping['field']] = $value;
                } else {
                    $customData[$mapping['field']] = $value;
                }
            }

            // Validate required fields
            $missingFields = [];
            foreach ($requiredFields as $reqField) {
                if (empty($rowData[$reqField] ?? '')) {
                    $label = self::FIELD_LABEL_MAP[$reqField] ?? $reqField;
                    $missingFields[] = $label;
                }
            }

            if (!empty($missingFields)) {
                $errors[] = "Row {$rowNumber}: Missing required fields — " . implode(', ', $missingFields);
                continue;
            }

            // Must have email at minimum for deduplication
            if (empty($rowData['email'] ?? '')) {
                $errors[] = "Row {$rowNumber}: Email address is required.";
                continue;
            }

            // Validate email format
            if (!filter_var($rowData['email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Row {$rowNumber}: Invalid email address '{$rowData['email']}'.";
                continue;
            }

            // Normalize phone numbers (handles Excel stripping leading 0 e.g. 547977840 -> 0547977840)
            if (isset($rowData['phone'])) {
                $rowData['phone'] = $this->normalizePhoneNumber($rowData['phone']);
            }
            if (isset($rowData['emergency_contact_phone'])) {
                $rowData['emergency_contact_phone'] = $this->normalizePhoneNumber($rowData['emergency_contact_phone']);
            }

            $email = strtolower(trim($rowData['email']));
            $phone = !empty($rowData['phone'] ?? '') ? trim($rowData['phone']) : null;

            // Check for duplicate email within this CSV file
            if (isset($seenEmails[$email])) {
                $skipped++;
                $skipReasons[] = "Row {$rowNumber}: Duplicate email '{$rowData['email']}' (same as row {$seenEmails[$email]})";
                continue;
            }

            // Check for duplicate phone within this CSV file
            if ($phone && isset($seenPhones[$phone])) {
                $skipped++;
                $skipReasons[] = "Row {$rowNumber}: Duplicate phone '{$phone}' (same as row {$seenPhones[$phone]})";
                continue;
            }

            // Check for duplicate email in database
            $emailExists = Attendee::where('event_id', $event->id)
                ->where('email', $rowData['email'])
                ->exists();

            if ($emailExists) {
                $skipped++;
                $skipReasons[] = "Row {$rowNumber}: Email '{$rowData['email']}' is already registered for this event";
                continue;
            }

            // Check for duplicate phone in database (checks 0547977840, 547977840, and 233547977840)
            if ($phone) {
                $phoneExists = Attendee::where('event_id', $event->id)
                    ->where(function($q) use ($phone) {
                        $q->where('phone', $phone);
                        if (str_starts_with($phone, '0')) {
                            $q->orWhere('phone', substr($phone, 1))
                              ->orWhere('phone', '233' . substr($phone, 1));
                        }
                    })
                    ->exists();

                if ($phoneExists) {
                    $skipped++;
                    $skipReasons[] = "Row {$rowNumber}: Phone '{$phone}' is already registered for this event";
                    continue;
                }
            }

            // Track this row's email and phone as seen
            $seenEmails[$email] = $rowNumber;
            if ($phone) {
                $seenPhones[$phone] = $rowNumber;
            }

            // Determine role and verification status
            $role = $rowData['access_role'] ?? 'general_admission';
            if (!in_array($role, $validRoles)) {
                $role = 'general_admission';
            }
            unset($rowData['access_role']);

            $verificationStatus = $rowData['verification_status'] ?? 'verified';
            if (!in_array($verificationStatus, $validStatuses)) {
                $verificationStatus = 'verified';
            }
            unset($rowData['verification_status']);

            // Build metadata with custom fields
            $metadata = !empty($customData) ? $customData : null;

            try {
                $attendee = Attendee::create(array_merge($rowData, [
                    'uuid' => (string) Str::uuid(),
                    'event_id' => $event->id,
                    'organization_id' => $organizationId,
                    'access_role' => $role,
                    'verification_status' => $verificationStatus,
                    'verified_at' => $verificationStatus === 'verified' ? now() : null,
                    'consent' => true,
                    'metadata' => $metadata,
                ]));

                // Auto-generate QR code for verified attendees
                if ($verificationStatus === 'verified') {
                    QrCode::create([
                        'uuid' => (string) Str::uuid(),
                        'attendee_id' => $attendee->id,
                        'event_id' => $event->id,
                        'secure_token' => Str::random(32),
                        'issued_at' => now(),
                        'is_revoked' => false,
                    ]);
                }

                $imported++;
            } catch (\Exception $e) {
                $errors[] = "Row {$rowNumber}: " . $e->getMessage();
            }
        }

        fclose($handle);

        $this->importResults = [
            'imported' => $imported,
            'skipped' => $skipped,
            'skip_reasons' => $skipReasons,
            'errors' => $errors,
            'total_rows' => $totalRowsProcessed,
        ];

        $this->csv_file = null;

        if ($imported > 0) {
            session()->flash('success', "{$imported} attendee(s) imported successfully" . ($skipped > 0 ? ", {$skipped} duplicate(s) skipped" : '') . '.');
        } elseif ($skipped > 0) {
            session()->flash('warning', "No new attendees imported. {$skipped} duplicate(s) skipped.");
        }
    }

    public function assignGateToAttendee($uuid, $gateId)
    {
        $attendee = Attendee::where('uuid', $uuid)->first();
        if ($attendee) {
            $gate = $gateId ? \App\Models\Gate::find($gateId) : null;
            $attendee->assigned_gate_id = $gate ? $gate->id : null;
            
            $this->ensureSecurityUserAccount($attendee);
            $attendee->save();
            
            $gateNotice = $gate ? "assigned to '{$gate->name}'" : "unassigned from gate";
            session()->flash('success', "Security personnel '{$attendee->full_name}' {$gateNotice}. Login credentials ready for {$attendee->email}.");
        }
    }

    protected function ensureSecurityUserAccount(Attendee $attendee)
    {
        $user = \App\Models\User::where('email', $attendee->email)->first();

        if (!$user) {
            $user = \App\Models\User::create([
                'uuid' => (string) \Illuminate\Support\Str::uuid(),
                'name' => $attendee->full_name,
                'email' => $attendee->email,
                'password' => \Illuminate\Support\Facades\Hash::make('Security@123'),
                'phone' => $attendee->phone,
                'organization_id' => $attendee->organization_id,
                'is_active' => true,
            ]);
        }

        if (method_exists($user, 'assignRole')) {
            try {
                $user->assignRole('gate_staff');
            } catch (\Exception $e) {
                // Role might not exist in spatie roles
            }
        }

        $attendee->user_id = $user->id;
    }

    public function render()
    {
        $countQuery = $this->getFilteredAttendeesQuery();

        $totalCount = (clone $countQuery)->count();
        $verifiedCount = (clone $countQuery)->where('verification_status', VerificationStatus::Verified)->count();
        $pendingCount = (clone $countQuery)->where('verification_status', VerificationStatus::Pending)->count();
        $rejectedCount = (clone $countQuery)->where('verification_status', VerificationStatus::Rejected)->count();

        $attendees = $this->getFilteredAttendeesQuery()->latest()->paginate($this->perPage);

        // Keep selectAllOnPage checkbox in sync with current page items
        $currentPageUuids = $attendees->pluck('uuid')->map(fn($u) => (string) $u)->toArray();
        if (count($currentPageUuids) > 0 && count(array_diff($currentPageUuids, $this->selectedAttendees)) === 0) {
            $this->selectAllOnPage = true;
        } else {
            $this->selectAllOnPage = false;
        }
        $events = Event::select('id', 'uuid', 'name', 'settings')->get();

        $availableGates = \App\Models\Gate::all();
        if ($this->eventUuid) {
            $currEvt = Event::where('uuid', $this->eventUuid)->first();
            if ($currEvt) {
                $availableGates = \App\Models\Gate::where('event_id', $currEvt->id)->get();
            }
        }

        $isSuperAdmin = auth()->user()->hasRole('super_admin') || auth()->user()->email === 'superadmin@attendflow.com';

        $organizationsTree = collect();
        if ($isSuperAdmin) {
            $organizationsTree = \App\Models\Organization::with(['events' => function($q) {
                $q->withCount('attendees');
            }])->get();
        } else {
            $currentOrgId = auth()->user()->organization_id;
            if ($currentOrgId) {
                $organizationsTree = \App\Models\Organization::where('id', $currentOrgId)->with(['events' => function($q) {
                    $q->withCount('attendees');
                }])->get();
            }
        }

        $mobileOrg = null;
        if ($this->showMobileOrgModal && $this->mobileOrgId) {
            $mobileOrg = \App\Models\Organization::with(['users', 'events' => function($q) {
                $q->withCount('attendees');
            }])->find($this->mobileOrgId);
        }

        $mobileEvent = null;
        $mobileAttendees = collect();
        if ($this->showMobileAttendeesModal && $this->mobileEventId) {
            $mobileEvent = Event::with('organization')->find($this->mobileEventId);
            $mobileAttendees = Attendee::where('event_id', $this->mobileEventId)
                ->with(['qrCode', 'assignedGate', 'latestCheckIn.gate', 'latestCheckIn.scanner'])
                ->latest()
                ->get();
        }

        return view('livewire.attendees.attendee-list', [
            'mobileOrg' => $mobileOrg,
            'mobileEvent' => $mobileEvent,
            'mobileAttendees' => $mobileAttendees,
            'showMobileAttendeesModal' => $this->showMobileAttendeesModal,
            'attendees' => $attendees,
            'events' => $events,
            'availableGates' => $availableGates,
            'totalCount' => $totalCount,
            'verifiedCount' => $verifiedCount,
            'pendingCount' => $pendingCount,
            'rejectedCount' => $rejectedCount,
            'isSuperAdmin' => $isSuperAdmin,
            'organizationsTree' => $organizationsTree,
            'eventUuid' => $this->eventUuid,
            'search' => $this->search,
            'statusFilter' => $this->statusFilter,
            'roleFilter' => $this->roleFilter,
            'expandedOrgs' => $this->expandedOrgs,
            'expandedEvents' => $this->expandedEvents,
            'groupedView' => $this->groupedView,
            'perPage' => $this->perPage,
            'selectedAttendees' => $this->selectedAttendees ?? [],
            'selectAll' => $this->selectAll,
            'showAddModal' => $this->showAddModal,
            'showDetailsModal' => $this->showDetailsModal,
            'showBulkInviteModal' => $this->showBulkInviteModal,
            'showImportCsvModal' => $this->showImportCsvModal,
            'importResults' => $this->importResults,
            'importEventFields' => $this->importEventFields,
            'showLinkGeneratorModal' => $this->showLinkGeneratorModal,
            'gen_access_role' => $this->gen_access_role,
            'gen_email' => $this->gen_email,
            'gen_max_uses' => $this->gen_max_uses,
            'generated_invite_url' => $this->generated_invite_url,
            'selectedAttendee' => $this->selectedAttendee,
            'gen_standard_fields' => $this->gen_standard_fields,
            'gen_custom_fields' => $this->gen_custom_fields,
            'showEmailReportModal' => $this->showEmailReportModal,
            'emailDeliveryResults' => $this->emailDeliveryResults,
            'emailSuccessCount' => $this->emailSuccessCount,
            'emailFailedCount' => $this->emailFailedCount,
            'approvedTotalCount' => $this->approvedTotalCount,
            'isProcessingBatch' => $this->isProcessingBatch,
            'batchTotalCount' => $this->batchTotalCount,
            'batchProcessedCount' => $this->batchProcessedCount,
        ]);
    }

    public function updatedSelectAll($value): void
    {
        $this->updatedSelectAllOnPage($value);
    }
}
