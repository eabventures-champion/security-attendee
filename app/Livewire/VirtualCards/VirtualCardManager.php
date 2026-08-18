<?php

namespace App\Livewire\VirtualCards;

use App\Models\VirtualIdCard;
use App\Models\Organization;
use App\Models\Event;
use App\Mail\MemberVirtualIdCardMail;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Livewire\Attributes\Title;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

#[Title('Virtual ID Cards')]
class VirtualCardManager extends Component
{
    use WithPagination, WithFileUploads;

    // Filters and search
    public string $search = '';
    public string $statusFilter = '';
    public string $institutionFilter = '';
    public int $perPage = 15;

    // Bulk selection
    public array $selectedMembers = [];
    public bool $selectAll = false;

    // Member Add / Edit Modal
    public bool $showMemberModal = false;
    public ?int $editingMemberId = null;
    public string $full_name = '';
    public string $email = '';
    public string $phone = '';
    public string $member_id_number = '';
    public string $institution = 'University of Ghana, School of Law';
    public string $admission_year = '';
    public string $completion_year = '';
    public $photo = null;
    public ?string $existing_photo_path = null;
    public array $member_custom_fields = [];
    public ?string $duplicateEmailWarning = null;
    public bool $isDuplicateEmail = false;

    // Excel / CSV Bulk Upload Modal
    public bool $showUploadModal = false;
    public $excel_file = null;
    public array $uploadPreview = [];
    public int $uploadedCount = 0;

    // Field Customizer & Card Branding Modal
    public bool $showFieldCustomizerModal = false;
    public array $customFieldDefs = [];
    public array $defaultFieldDefs = [];
    public string $newFieldLabel = '';
    public string $newFieldType = 'text';
    public string $newFieldOptions = ''; // Comma separated options for dropdowns
    public $institution_logo = null;
    public ?string $existing_institution_logo_path = null;

    // Institution Names / Faculties Dropdown Manager Modal
    public bool $showInstitutionsModal = false;
    public array $institutionList = [];
    public string $newInstitutionName = '';
    public string $bulkInstitutionsText = '';
    public $institutions_file = null;

    // Share Registration / Application Link Modal
    public bool $showShareLinkModal = false;

    // Interactive Card Preview Modal
    public bool $showCardPreviewModal = false;
    public ?VirtualIdCard $previewCard = null;
    public string $previewSide = 'front'; // 'front' or 'back'

    protected $paginationTheme = 'tailwind';

    public function getShareableApplicationUrl(): string
    {
        $org = $this->getOrganization();
        if (!$org) return url('/virtual-cards/apply/default');
        return route('virtual-cards.public.apply', ['org_slug' => $org->slug ?: $org->uuid]);
    }

    public function openShareLinkModal(): void
    {
        $this->showShareLinkModal = true;
    }

    public function closeShareLinkModal(): void
    {
        $this->showShareLinkModal = false;
    }

    public function mount()
    {
        $this->admission_year = (string) (date('Y') - 3);
        $this->completion_year = (string) date('Y');
        $this->loadFieldDefinitions();
        $this->loadInstitutions();
    }

    public static function getDefaultInstitutionsList(): array
    {
        return [
            'Accra Metropolitan University, School of Law',
            'Ashesi University, Faculty of Humanities and Social Sciences',
            'Central University, Faculty of Law',
            'Ghana Institute of Management and Public Administration (GIMPA), Faculty of Law',
            'Greenfield College, Faculty of Law',
            'KAAF University College, Faculty of Law',
            'Kings University College, Faculty of Law, Governance and International Relations',
            'Kwame Nkrumah University of Science and Technology (KNUST), Faculty of Law',
            'Lancaster University, Faculty of Law',
            'Mountcrest University, Faculty of Law',
            'Pentecost University, Faculty of Law',
            'Presbyterian University, Faculty of Law',
            'University of Business and Integrated Development Studies (UBIDS), Faculty of Law',
            'University of Cape Coast, Faculty of Law',
            'University for Development Studies (UDS), Faculty of Law',
            'University of Ghana, School of Law',
            'University of Professional Studies (UPSA), School of Law',
            'Wisconsin International University College, Ghana, Faculty of Law',
            'Zenith University College, Ghana, Faculty of Law',
        ];
    }

    public function loadInstitutions(): void
    {
        $org = $this->getOrganization();
        $settings = $org && $org->settings ? (is_array($org->settings) ? $org->settings : json_decode($org->settings, true)) : [];
        $idCardConfig = $settings['id_card_config'] ?? [];

        if (!empty($idCardConfig['institutions']) && is_array($idCardConfig['institutions'])) {
            $this->institutionList = array_values(array_filter(array_map('trim', $idCardConfig['institutions'])));
        } else {
            $this->institutionList = self::getDefaultInstitutionsList();
        }

        if (empty($this->institution) && !empty($this->institutionList)) {
            $this->institution = in_array('University of Ghana, School of Law', $this->institutionList)
                ? 'University of Ghana, School of Law'
                : $this->institutionList[0];
        }
    }

    public function openInstitutionsModal(): void
    {
        $this->newInstitutionName = '';
        $this->bulkInstitutionsText = implode("\n", $this->institutionList);
        $this->institutions_file = null;
        $this->showInstitutionsModal = true;
    }

    public function closeInstitutionsModal(): void
    {
        $this->showInstitutionsModal = false;
        $this->institutions_file = null;
    }

    public function addInstitution(): void
    {
        $name = trim($this->newInstitutionName);
        if (empty($name)) {
            session()->flash('error', 'Please enter an institution/faculty name.');
            return;
        }

        if (in_array($name, $this->institutionList)) {
            session()->flash('error', "'{$name}' already exists in the list.");
            return;
        }

        $this->institutionList[] = $name;
        $this->newInstitutionName = '';
        $this->persistInstitutions();
        session()->flash('success', "Added '{$name}' to institutions dropdown list.");
    }

    public function removeInstitution(int $index): void
    {
        if (isset($this->institutionList[$index])) {
            $removed = $this->institutionList[$index];
            array_splice($this->institutionList, $index, 1);
            $this->persistInstitutions();
            session()->flash('success', "Removed '{$removed}' from institutions list.");
        }
    }

    public function saveBulkInstitutions(): void
    {
        $lines = preg_split('/[\r\n]+/', (string)$this->bulkInstitutionsText);
        $clean = [];
        foreach ($lines as $line) {
            $t = trim($line);
            // Clean leading numbers if any e.g. "1. "
            $t = preg_replace('/^\d+[\.\)]\s*/', '', $t);
            if (!empty($t) && !in_array($t, $clean)) {
                $clean[] = $t;
            }
        }

        if (empty($clean)) {
            session()->flash('error', 'Please provide at least one institution name.');
            return;
        }

        $this->institutionList = $clean;
        $this->persistInstitutions();
        session()->flash('success', "Updated institutions dropdown with " . count($this->institutionList) . " items.");
    }

    public function uploadInstitutionsFile(): void
    {
        $this->validate([
            'institutions_file' => 'required|file|mimes:txt,csv|max:5120',
        ]);

        try {
            $content = file_get_contents($this->institutions_file->getRealPath());
            $lines = preg_split('/[\r\n]+/', (string)$content);
            $importedCount = 0;

            foreach ($lines as $line) {
                $t = trim($line, " \t\n\r\0\x0B,\"'");
                $t = preg_replace('/^\d+[\.\)]\s*/', '', $t);
                if (!empty($t) && !in_array($t, $this->institutionList)) {
                    $this->institutionList[] = $t;
                    $importedCount++;
                }
            }

            $this->persistInstitutions();
            $this->institutions_file = null;
            session()->flash('success', "Successfully imported {$importedCount} new institution(s) from file!");
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to read institutions file: ' . $e->getMessage());
        }
    }

    public function resetDefaultInstitutions(): void
    {
        $this->institutionList = self::getDefaultInstitutionsList();
        $this->persistInstitutions();
        $this->bulkInstitutionsText = implode("\n", $this->institutionList);
        session()->flash('success', 'Reset institutions to default 19 standard Ghanaian law faculties.');
    }

    protected function persistInstitutions(): void
    {
        $org = $this->getOrganization();
        if ($org) {
            $settings = $org->settings ? (is_array($org->settings) ? $org->settings : json_decode($org->settings, true)) : [];
            $settings['id_card_config'] = $settings['id_card_config'] ?? [];
            $settings['id_card_config']['institutions'] = array_values($this->institutionList);
            $org->settings = $settings;
            $org->save();
        }
    }

    /**
     * Load or initialize default and custom field configurations & institution logo
     */
    public function loadFieldDefinitions(): void
    {
        $org = $this->getOrganization();
        $settings = $org && $org->settings ? (is_array($org->settings) ? $org->settings : json_decode($org->settings, true)) : [];
        $idCardConfig = $settings['id_card_config'] ?? null;

        $this->existing_institution_logo_path = $idCardConfig['institution_logo_path'] ?? ($org->logo_path ?? null);

        if (!$idCardConfig) {
            $this->defaultFieldDefs = [
                ['key' => 'logo', 'label' => 'Institution Logo', 'enabled' => true, 'is_default' => true],
                ['key' => 'photo', 'label' => 'Profile Photo', 'enabled' => true, 'is_default' => true],
                ['key' => 'full_name', 'label' => 'Full Name', 'enabled' => true, 'is_default' => true],
                ['key' => 'member_id_number', 'label' => 'Member ID Number', 'enabled' => true, 'is_default' => true],
                ['key' => 'institution', 'label' => 'Institution/Faculty of Law', 'enabled' => true, 'is_default' => true],
                ['key' => 'admission_year', 'label' => 'Year of Admission', 'enabled' => true, 'is_default' => true],
                ['key' => 'completion_year', 'label' => 'Year of Completion', 'enabled' => true, 'is_default' => true],
            ];
            $this->customFieldDefs = [];
        } else {
            $this->defaultFieldDefs = $idCardConfig['default_fields'] ?? [];
            $this->customFieldDefs = $idCardConfig['custom_fields'] ?? [];
        }
    }

    /**
     * Save Field Definitions and Institution Logo
     */
    public function saveFieldDefinitions(): void
    {
        $org = $this->getOrganization();
        if ($org) {
            $logoPath = $this->existing_institution_logo_path;
            if ($this->institution_logo) {
                $this->validate([
                    'institution_logo' => 'image|max:5120',
                ]);
                $logoPath = $this->institution_logo->store('virtual_cards/logos', 'public');
                $this->existing_institution_logo_path = $logoPath;
            }

            $settings = $org->settings ? (is_array($org->settings) ? $org->settings : json_decode($org->settings, true)) : [];
            $settings['id_card_config'] = [
                'institution_logo_path' => $logoPath,
                'default_fields' => $this->defaultFieldDefs,
                'custom_fields' => $this->customFieldDefs,
            ];
            $org->settings = $settings;
            $org->save();
        }

        $this->institution_logo = null;
        $this->showFieldCustomizerModal = false;
        session()->flash('success', '🪪 ID Card fields configuration and Institution Logo saved successfully.');
    }

    public function removeInstitutionLogo(): void
    {
        $org = $this->getOrganization();
        if ($org) {
            if ($this->existing_institution_logo_path && !str_starts_with($this->existing_institution_logo_path, 'http')) {
                Storage::disk('public')->delete($this->existing_institution_logo_path);
            }
            $this->existing_institution_logo_path = null;
            $this->institution_logo = null;

            $settings = $org->settings ? (is_array($org->settings) ? $org->settings : json_decode($org->settings, true)) : [];
            if (isset($settings['id_card_config'])) {
                $settings['id_card_config']['institution_logo_path'] = null;
                $org->settings = $settings;
                $org->save();
            }
        }
        session()->flash('success', 'Institution Logo removed.');
    }

    public function addCustomField(): void
    {
        $this->validate([
            'newFieldLabel' => 'required|string|max:100',
        ]);

        $key = Str::slug($this->newFieldLabel, '_');
        $options = [];

        if ($this->newFieldType === 'yes_no') {
            $options = ['Yes', 'No'];
        } elseif (in_array($this->newFieldType, ['dropdown', 'select'])) {
            $options = !empty($this->newFieldOptions) 
                ? array_values(array_filter(array_map('trim', explode(',', $this->newFieldOptions))))
                : ['Yes', 'No'];
        }

        $this->customFieldDefs[] = [
            'key' => $key,
            'label' => $this->newFieldLabel,
            'type' => in_array($this->newFieldType, ['yes_no', 'dropdown', 'select']) ? 'select' : $this->newFieldType,
            'options' => $options,
            'enabled' => true,
            'is_default' => false,
        ];

        $this->newFieldLabel = '';
        $this->newFieldType = 'text';
        $this->newFieldOptions = '';
    }

    public function removeCustomField(int $index): void
    {
        unset($this->customFieldDefs[$index]);
        $this->customFieldDefs = array_values($this->customFieldDefs);
    }

    public function removeDefaultField(int $index): void
    {
        unset($this->defaultFieldDefs[$index]);
        $this->defaultFieldDefs = array_values($this->defaultFieldDefs);
    }

    protected function getOrganization(): ?Organization
    {
        $user = auth()->user();
        if (!$user) return null;
        return $user->organization ?? Organization::first();
    }

    public function openAddModal(): void
    {
        $this->resetMemberForm();
        $this->member_id_number = 'FALAS-' . date('Y') . '-' . str_pad(rand(100, 99999), 5, '0', STR_PAD_LEFT);
        $this->showMemberModal = true;
    }

    public function openEditModal(int $id): void
    {
        $card = VirtualIdCard::findOrFail($id);
        $this->editingMemberId = $card->id;
        $this->full_name = $card->full_name;
        $this->email = $card->email ?? '';
        $this->phone = $card->phone ?? '';
        $this->member_id_number = $card->member_id_number;
        $this->institution = $card->institution ?? '';
        $this->law_faculty = $card->law_faculty ?? '';
        $this->admission_year = $card->admission_year ?? '';
        $this->completion_year = $card->completion_year ?? '';
        $this->existing_photo_path = $card->photo_path;
        $this->member_custom_fields = $card->custom_fields ?? [];
        $this->photo = null;
        $this->showMemberModal = true;
    }

    public function closeMemberModal(): void
    {
        $this->showMemberModal = false;
        $this->resetMemberForm();
    }

    public function updatedEmail($value): void
    {
        $this->checkDuplicateEmail($value);
    }

    public function checkDuplicateEmail(?string $email): void
    {
        $email = trim((string)$email);
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->duplicateEmailWarning = null;
            $this->isDuplicateEmail = false;
            return;
        }

        $org = $this->getOrganization();
        if (!$org) {
            $this->duplicateEmailWarning = null;
            $this->isDuplicateEmail = false;
            return;
        }

        $query = VirtualIdCard::where('organization_id', $org->id)
            ->where('email', strtolower($email));

        if ($this->editingMemberId) {
            $query->where('id', '!=', $this->editingMemberId);
        }

        $existingCard = $query->first();

        if ($existingCard) {
            $this->isDuplicateEmail = true;
            $this->duplicateEmailWarning = "⚠️ Member card already registered with this email: {$existingCard->full_name} ({$existingCard->member_id_number})";
        } else {
            $this->isDuplicateEmail = false;
            $this->duplicateEmailWarning = null;
        }
    }

    public function resetMemberForm(): void
    {
        $this->editingMemberId = null;
        $this->full_name = '';
        $this->email = '';
        $this->phone = '';
        $this->member_id_number = '';
        $this->institution = 'University of Ghana, School of Law';
        $this->law_faculty = '';
        $this->admission_year = (string) (date('Y') - 3);
        $this->completion_year = (string) date('Y');
        $this->photo = null;
        $this->existing_photo_path = null;
        $this->member_custom_fields = [];
        $this->duplicateEmailWarning = null;
        $this->isDuplicateEmail = false;
        $this->resetErrorBag();
    }

    public function saveMember(): void
    {
        $this->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'member_id_number' => 'required|string|max:100',
            'institution' => 'nullable|string|max:255',
            'admission_year' => 'nullable|string|max:20',
            'completion_year' => 'nullable|string|max:20',
            'photo' => 'nullable|image|max:5120',
        ]);

        $org = $this->getOrganization();
        if (!$org) {
            session()->flash('error', 'Organization context required.');
            return;
        }

        if ($this->email) {
            $dup = VirtualIdCard::where('organization_id', $org->id)
                ->where('email', strtolower(trim($this->email)))
                ->when($this->editingMemberId, fn($q) => $q->where('id', '!=', $this->editingMemberId))
                ->first();

            if ($dup) {
                $this->isDuplicateEmail = true;
                $this->duplicateEmailWarning = "⚠️ A virtual ID card is already registered with this email: {$dup->full_name} ({$dup->member_id_number})";
                $this->addError('email', "A virtual ID card is already registered with this email ({$dup->full_name} - {$dup->member_id_number}).");
                return;
            }
        }

        $photoPath = $this->existing_photo_path;
        if ($this->photo) {
            $photoPath = $this->photo->store('virtual_cards/photos', 'public');
        }

        $data = [
            'organization_id' => $org->id,
            'full_name' => $this->full_name,
            'email' => $this->email ? strtolower(trim($this->email)) : null,
            'phone' => $this->phone ? trim($this->phone) : null,
            'member_id_number' => $this->member_id_number,
            'institution' => $this->institution ?: 'University of Ghana, School of Law',
            'law_faculty' => null,
            'admission_year' => $this->admission_year,
            'completion_year' => $this->completion_year,
            'photo_path' => $photoPath,
            'custom_fields' => $this->member_custom_fields,
            'status' => 'active',
        ];

        if ($this->editingMemberId) {
            $card = VirtualIdCard::findOrFail($this->editingMemberId);
            $card->update($data);
            session()->flash('success', "Member '{$card->full_name}' virtual ID card updated successfully.");
        } else {
            $card = VirtualIdCard::create($data);
            session()->flash('success', "Virtual ID Card generated for '{$card->full_name}' ({$card->member_id_number})!");
        }

        $this->closeMemberModal();
    }

    public function deleteMember(int $id): void
    {
        $card = VirtualIdCard::find($id);
        if ($card) {
            $name = $card->full_name;
            if ($card->photo_path && !str_starts_with($card->photo_path, 'http')) {
                Storage::disk('public')->delete($card->photo_path);
            }
            $card->delete();
            session()->flash('success', "Member '{$name}' deleted successfully.");
        }
    }

    public function bulkDelete(): void
    {
        if (empty($this->selectedMembers)) return;

        VirtualIdCard::whereIn('id', $this->selectedMembers)->delete();
        $count = count($this->selectedMembers);
        $this->selectedMembers = [];
        $this->selectAll = false;
        session()->flash('success', "Permanently deleted {$count} selected member record(s).");
    }

    public function openCardPreview(int $id): void
    {
        $this->previewCard = VirtualIdCard::findOrFail($id);
        $this->previewSide = 'front';
        $this->showCardPreviewModal = true;
    }

    public function closeCardPreview(): void
    {
        $this->showCardPreviewModal = false;
        $this->previewCard = null;
    }

    public function sendCardEmail(int $id): void
    {
        $card = VirtualIdCard::findOrFail($id);
        if (empty($card->email)) {
            session()->flash('error', "Cannot send email: Member '{$card->full_name}' has no email address.");
            return;
        }

        try {
            Mail::to($card->email)->queue(new MemberVirtualIdCardMail($card));
            session()->flash('success', "✉️ Virtual ID Card dispatched to {$card->email}!");
        } catch (\Exception $e) {
            try {
                Mail::to($card->email)->send(new MemberVirtualIdCardMail($card));
                session()->flash('success', "✉️ Virtual ID Card dispatched to {$card->email}!");
            } catch (\Exception $ex) {
                Log::error('Failed to send Virtual ID Card email: ' . $ex->getMessage());
                session()->flash('error', 'Failed to dispatch email: ' . $ex->getMessage());
            }
        }
    }

    public function sendCardWhatsApp(int $id): void
    {
        $card = VirtualIdCard::findOrFail($id);
        if (empty($card->phone)) {
            session()->flash('error', "Cannot send WhatsApp: Member '{$card->full_name}' has no phone number.");
            return;
        }

        $cleanPhone = preg_replace('/[^0-9]/', '', (string)$card->phone);
        if (strlen($cleanPhone) === 9) {
            $cleanPhone = '233' . $cleanPhone;
        } elseif (!empty($cleanPhone) && str_starts_with($cleanPhone, '0')) {
            $cleanPhone = '233' . substr($cleanPhone, 1);
        }

        $orgName = $card->organization ? $card->organization->name : config('app.name');
        $cardUrl = route('virtual-cards.public.view', ['uuid' => $card->uuid]);
        $qrImageUrl = $card->qr_code_url;

        $message = "Hello {$card->full_name},\n\nHere is your official *Virtual Membership ID Card* for *{$orgName}*:\n\n🪪 *Member ID:* {$card->member_id_number}\n🏛️ *Institution / Faculty of Law:* {$card->institution}\n\n📷 *View & Download Your ID Card:* \n{$cardUrl}\n\nPlease present or save this digital credential on your device.";

        $waUrl = "https://api.whatsapp.com/send?phone={$cleanPhone}&text=" . rawurlencode($message);
        $this->js("window.open('{$waUrl}', '_blank')");
        session()->flash('success', "📱 Opening WhatsApp to deliver Virtual ID Card to {$card->full_name}!");
    }

    public function updatedSelectAll($value): void
    {
        if ($value) {
            $this->selectedMembers = $this->queryMembers()->pluck('id')->map(fn($id) => (string)$id)->toArray();
        } else {
            $this->selectedMembers = [];
        }
    }

    public function openUploadModal(): void
    {
        $this->excel_file = null;
        $this->uploadPreview = [];
        $this->uploadedCount = 0;
        $this->showUploadModal = true;
    }

    public function closeUploadModal(): void
    {
        $this->showUploadModal = false;
        $this->excel_file = null;
        $this->uploadPreview = [];
    }

    public function updatedExcelFile(): void
    {
        $this->validate([
            'excel_file' => 'required|file|mimes:csv,txt,xlsx,xls|max:10240',
        ]);

        try {
            $path = $this->excel_file->getRealPath();
            $ext = strtolower($this->excel_file->getClientOriginalExtension());
            $rows = $this->parseSpreadsheetRows($path, $ext);
            $this->uploadPreview = $this->extractMembersFromRows($rows);
            $this->uploadedCount = count($this->uploadPreview);

            if ($this->uploadedCount === 0) {
                session()->flash('error', 'No valid member rows found in the uploaded file.');
            }
        } catch (\Exception $e) {
            Log::error('Failed to parse Virtual Card upload file: ' . $e->getMessage());
            session()->flash('error', 'Failed to read spreadsheet: ' . $e->getMessage());
        }
    }

    public function importUploadedMembers(): void
    {
        if (empty($this->uploadPreview)) {
            session()->flash('error', 'No member records to import.');
            return;
        }

        $org = $this->getOrganization();
        if (!$org) {
            session()->flash('error', 'Organization context required.');
            return;
        }

        $imported = 0;
        $updated = 0;

        foreach ($this->uploadPreview as $row) {
            $existing = null;
            if (!empty($row['email'])) {
                $existing = VirtualIdCard::where('organization_id', $org->id)->where('email', $row['email'])->first();
            }

            if ($existing) {
                $existing->update([
                    'full_name' => $row['full_name'] ?: $existing->full_name,
                    'phone' => $row['phone'] ?: $existing->phone,
                    'institution' => $row['institution'] ?: $existing->institution,
                    'admission_year' => $row['admission_year'] ?: $existing->admission_year,
                    'completion_year' => $row['completion_year'] ?: $existing->completion_year,
                    'custom_fields' => !empty($row['custom_fields']) ? $row['custom_fields'] : $existing->custom_fields,
                ]);
                $updated++;
            } else {
                // Uniformly auto-generate system ID number
                $idNumber = 'FALAS-' . date('Y') . '-' . str_pad(rand(100, 99999), 5, '0', STR_PAD_LEFT);

                VirtualIdCard::create([
                    'organization_id' => $org->id,
                    'full_name' => $row['full_name'],
                    'email' => $row['email'] ?: null,
                    'phone' => $row['phone'] ?: null,
                    'member_id_number' => $idNumber,
                    'institution' => $row['institution'] ?: 'University of Ghana, School of Law',
                    'law_faculty' => null,
                    'admission_year' => $row['admission_year'] ?: (string)(date('Y') - 3),
                    'completion_year' => $row['completion_year'] ?: (string)date('Y'),
                    'custom_fields' => $row['custom_fields'] ?? [],
                    'status' => 'active',
                ]);
                $imported++;
            }
        }

        session()->flash('success', "📁 Successfully processed spreadsheet: {$imported} new member(s) created with uniform auto-generated ID numbers & {$updated} existing record(s) updated.");
        $this->closeUploadModal();
    }

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

    public function getExpectedImportFields(): array
    {
        $fields = [
            ['label' => 'Full Name', 'key' => 'full_name', 'required' => true, 'type' => 'text'],
            ['label' => 'Email Address', 'key' => 'email', 'required' => false, 'type' => 'email'],
            ['label' => 'Phone / WhatsApp', 'key' => 'phone', 'required' => false, 'type' => 'phone'],
            ['label' => 'Institution / Faculty of Law', 'key' => 'institution', 'required' => false, 'type' => 'text'],
            ['label' => 'Admission Year', 'key' => 'admission_year', 'required' => false, 'type' => 'number'],
            ['label' => 'Completion Year', 'key' => 'completion_year', 'required' => false, 'type' => 'number'],
        ];

        foreach ($this->customFieldDefs as $cf) {
            $fields[] = [
                'label' => $cf['label'],
                'key' => $cf['key'],
                'required' => false,
                'type' => $cf['type'] ?? 'custom',
                'options' => $cf['options'] ?? [],
            ];
        }

        return $fields;
    }

    public function downloadSampleCsv()
    {
        $fields = $this->getExpectedImportFields();
        $headers = array_column($fields, 'label');
        
        $sampleRow = [
            'Kwame Mensah',
            'kwame.mensah@example.com',
            '0244123456',
            'University of Ghana, School of Law',
            (string)(date('Y') - 3),
            (string)date('Y'),
        ];

        // Append sample values for custom fields
        foreach ($this->customFieldDefs as $cf) {
            if (!empty($cf['options'])) {
                $sampleRow[] = $cf['options'][0] ?? 'Yes';
            } elseif (($cf['type'] ?? '') === 'number') {
                $sampleRow[] = '100';
            } elseif (($cf['type'] ?? '') === 'date') {
                $sampleRow[] = date('Y-m-d');
            } else {
                $sampleRow[] = 'Sample ' . $cf['label'];
            }
        }

        $filename = 'virtual_id_card_members_template.csv';

        return response()->streamDownload(function () use ($headers, $sampleRow) {
            $handle = fopen('php://output', 'w');
            // Write BOM for UTF-8 Excel compatibility
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($handle, $headers);
            fputcsv($handle, $sampleRow);
            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    protected function extractMembersFromRows(array $rows): array
    {
        if (empty($rows)) return [];

        $colMap = [
            'name' => -1,
            'email' => -1,
            'phone' => -1,
            'institution' => -1,
            'admission_year' => -1,
            'completion_year' => -1,
        ];

        $customColMap = []; // key => colIndex
        foreach ($this->customFieldDefs as $cf) {
            $customColMap[$cf['key']] = -1;
        }

        $firstRow = $rows[0];
        foreach ($firstRow as $idx => $cell) {
            $clean = strtolower(trim((string)$cell));
            $cleanSlug = Str::slug($clean, '_');

            if (in_array($clean, ['name', 'full name', 'fullname', 'member name', 'student name'])) $colMap['name'] = $idx;
            elseif (in_array($clean, ['email', 'email address', 'e-mail', 'mail'])) $colMap['email'] = $idx;
            elseif (in_array($clean, ['phone', 'phone number', 'contact', 'telephone', 'mobile', 'whatsapp'])) $colMap['phone'] = $idx;
            elseif (in_array($clean, ['institution', 'university', 'college', 'school', 'faculty', 'law faculty', 'department', 'institution/faculty of law', 'institution / faculty of law', 'institution/law faculty', 'institution / law faculty'])) $colMap['institution'] = $idx;
            elseif (in_array($clean, ['admission', 'admission year', 'year of admission', 'entry year'])) $colMap['admission_year'] = $idx;
            elseif (in_array($clean, ['completion', 'completion year', 'year of completion', 'graduation year'])) $colMap['completion_year'] = $idx;

            // Match Dynamic Custom Field Columns
            foreach ($this->customFieldDefs as $cf) {
                $cfLabelClean = strtolower(trim($cf['label']));
                $cfKeyClean = strtolower(trim($cf['key']));
                if ($clean === $cfLabelClean || $cleanSlug === $cfKeyClean || $clean === $cfKeyClean) {
                    $customColMap[$cf['key']] = $idx;
                }
            }
        }

        $startIndex = ($colMap['name'] !== -1 || $colMap['email'] !== -1) ? 1 : 0;
        $extracted = [];

        for ($i = $startIndex; $i < count($rows); $i++) {
            $r = $rows[$i];
            if (empty($r)) continue;

            $name = $colMap['name'] !== -1 && isset($r[$colMap['name']]) ? trim((string)$r[$colMap['name']]) : '';
            $email = $colMap['email'] !== -1 && isset($r[$colMap['email']]) ? strtolower(trim((string)$r[$colMap['email']])) : '';
            $phone = $colMap['phone'] !== -1 && isset($r[$colMap['phone']]) ? trim((string)$r[$colMap['phone']]) : '';
            $inst = $colMap['institution'] !== -1 && isset($r[$colMap['institution']]) ? trim((string)$r[$colMap['institution']]) : '';
            $adm = $colMap['admission_year'] !== -1 && isset($r[$colMap['admission_year']]) ? trim((string)$r[$colMap['admission_year']]) : '';
            $comp = $colMap['completion_year'] !== -1 && isset($r[$colMap['completion_year']]) ? trim((string)$r[$colMap['completion_year']]) : '';

            // Extract values for dynamic custom fields
            $customValues = [];
            foreach ($this->customFieldDefs as $cf) {
                $colIdx = $customColMap[$cf['key']] ?? -1;
                if ($colIdx !== -1 && isset($r[$colIdx])) {
                    $customValues[$cf['key']] = trim((string)$r[$colIdx]);
                }
            }

            // Fallback column indexing if headers were missing
            if (empty($name) && isset($r[0])) $name = trim((string)$r[0]);
            if (empty($email) && isset($r[1]) && str_contains($r[1], '@')) $email = strtolower(trim((string)$r[1]));

            if (!empty($name) || !empty($email)) {
                $extracted[] = [
                    'full_name' => $name ?: 'Member ' . ($i),
                    'email' => $email,
                    'phone' => $phone,
                    'institution' => $inst ?: 'University of Ghana, School of Law',
                    'admission_year' => $adm ?: (string)(date('Y') - 3),
                    'completion_year' => $comp ?: (string)date('Y'),
                    'custom_fields' => $customValues,
                ];
            }
        }

        return $extracted;
    }

    protected function queryMembers()
    {
        $user = auth()->user();
        $isSuperAdmin = $user ? $user->isSuperAdmin() : false;
        $query = VirtualIdCard::query();

        if (!$isSuperAdmin && $user && $user->organization_id) {
            $query->where('organization_id', $user->organization_id);
        }

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('full_name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%')
                  ->orWhere('member_id_number', 'like', '%' . $this->search . '%')
                  ->orWhere('institution', 'like', '%' . $this->search . '%')
                  ->orWhere('law_faculty', 'like', '%' . $this->search . '%');
            });
        }

        if (!empty($this->statusFilter)) {
            $query->where('status', $this->statusFilter);
        }

        if (!empty($this->institutionFilter)) {
            $query->where('institution', $this->institutionFilter);
        }

        return $query->latest();
    }

    public function render()
    {
        $user = auth()->user();
        $isSuperAdmin = $user ? $user->isSuperAdmin() : false;
        $members = $this->queryMembers()->paginate($this->perPage);

        $baseCountQuery = VirtualIdCard::query();
        if (!$isSuperAdmin && $user && $user->organization_id) {
            $baseCountQuery->where('organization_id', $user->organization_id);
        }

        $totalCount = (clone $baseCountQuery)->count();
        $activeCount = (clone $baseCountQuery)->where('status', 'active')->count();

        $institutions = (clone $baseCountQuery)->whereNotNull('institution')
            ->where('institution', '!=', '')
            ->distinct()
            ->pluck('institution');

        return view('livewire.virtual-cards.virtual-card-manager', [
            'members' => $members,
            'totalCount' => $totalCount,
            'activeCount' => $activeCount,
            'institutions' => $institutions,
        ]);
    }
}
