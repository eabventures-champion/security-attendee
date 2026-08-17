<?php

namespace App\Livewire\VirtualCards;

use App\Models\Organization;
use App\Models\VirtualIdCard;
use App\Mail\MemberVirtualIdCardMail;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

#[Layout('layouts.guest')]
#[Title('Apply for Virtual Membership ID')]
class PublicMemberCardApplication extends Component
{
    use WithFileUploads;

    public string $org_slug = '';
    public ?Organization $organization = null;

    // Form inputs
    public string $full_name = '';
    public string $email = '';
    public string $phone = '';
    public string $institution = '';
    public string $law_faculty = '';
    public string $admission_year = '';
    public string $completion_year = '';
    public $photo = null;
    public array $custom_field_values = [];

    // Configurations
    public array $defaultFieldDefs = [];
    public array $customFieldDefs = [];
    public array $institutionList = [];
    public ?string $institution_logo_url = null;

    // State
    public bool $submitted = false;
    public ?VirtualIdCard $generatedCard = null;

    public function mount($org_slug = null, $orgSlug = null)
    {
        $slug = $org_slug ?: $orgSlug;
        $this->org_slug = (string)$slug;

        $this->organization = Organization::where('slug', $slug)
            ->orWhere('uuid', $slug)
            ->orWhere('id', $slug)
            ->first();

        if (!$this->organization) {
            $this->organization = Organization::firstOrFail();
        }

        $settings = $this->organization->settings ? (is_array($this->organization->settings) ? $this->organization->settings : json_decode($this->organization->settings, true)) : [];
        $idCardConfig = $settings['id_card_config'] ?? null;

        if (!$idCardConfig) {
            $this->defaultFieldDefs = [
                ['key' => 'photo', 'label' => 'Profile Photo', 'enabled' => true],
                ['key' => 'full_name', 'label' => 'Full Name', 'enabled' => true],
                ['key' => 'institution', 'label' => 'Institution/Faculty of Law', 'enabled' => true],
                ['key' => 'admission_year', 'label' => 'Year of Admission', 'enabled' => true],
                ['key' => 'completion_year', 'label' => 'Year of Completion', 'enabled' => true],
            ];
            $this->customFieldDefs = [];
        } else {
            $this->defaultFieldDefs = $idCardConfig['default_fields'] ?? [];
            $this->customFieldDefs = $idCardConfig['custom_fields'] ?? [];
            if (!empty($idCardConfig['institution_logo_path'])) {
                $path = $idCardConfig['institution_logo_path'];
                $this->institution_logo_url = str_starts_with($path, 'http') ? $path : asset('storage/' . $path);
            }
        }

        if (!empty($idCardConfig['institutions']) && is_array($idCardConfig['institutions'])) {
            $this->institutionList = array_values(array_filter(array_map('trim', $idCardConfig['institutions'])));
        } else {
            $this->institutionList = [
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

        if (empty($this->institution_logo_url) && !empty($this->organization->logo_path)) {
            $path = $this->organization->logo_path;
            $this->institution_logo_url = str_starts_with($path, 'http') ? $path : asset('storage/' . $path);
        }

        $this->institution = in_array('University of Ghana, School of Law', $this->institutionList)
            ? 'University of Ghana, School of Law'
            : ($this->institutionList[0] ?? '');
        $this->law_faculty = '';
        $this->admission_year = (string)(date('Y') - 3);
        $this->completion_year = (string)date('Y');
    }

    public function submitApplication()
    {
        $rules = [
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
            'institution' => 'nullable|string|max:255',
            'admission_year' => 'nullable|string|max:20',
            'completion_year' => 'nullable|string|max:20',
            'photo' => 'nullable|image|max:5120',
        ];

        $this->validate($rules);

        // Check if member already has a card with this email in this organization
        $existing = VirtualIdCard::where('organization_id', $this->organization->id)
            ->where('email', strtolower(trim($this->email)))
            ->first();

        $photoPath = null;
        if ($this->photo) {
            $photoPath = $this->photo->store('virtual_cards/photos', 'public');
        } elseif ($existing) {
            $photoPath = $existing->photo_path;
        }

        $memberId = $existing ? $existing->member_id_number : ('FALAS-' . date('Y') . '-' . str_pad(rand(100, 99999), 5, '0', STR_PAD_LEFT));

        $data = [
            'organization_id' => $this->organization->id,
            'full_name' => $this->full_name,
            'email' => strtolower(trim($this->email)),
            'phone' => $this->phone ? trim($this->phone) : null,
            'member_id_number' => $memberId,
            'institution' => $this->institution ?: 'University of Ghana, School of Law',
            'law_faculty' => null,
            'admission_year' => $this->admission_year ?: (string)(date('Y') - 3),
            'completion_year' => $this->completion_year ?: (string)date('Y'),
            'photo_path' => $photoPath,
            'custom_fields' => $this->custom_field_values,
            'status' => 'active',
        ];

        if ($existing) {
            $existing->update($data);
            $this->generatedCard = $existing;
        } else {
            $this->generatedCard = VirtualIdCard::create($data);
        }

        // Dispatch Confirmation Email with Virtual ID
        try {
            Mail::to($this->generatedCard->email)->queue(new MemberVirtualIdCardMail($this->generatedCard));
        } catch (\Exception $e) {
            try {
                Mail::to($this->generatedCard->email)->send(new MemberVirtualIdCardMail($this->generatedCard));
            } catch (\Exception $ex) {
                Log::error('Public card application email failed: ' . $ex->getMessage());
            }
        }

        $this->submitted = true;
        session()->flash('success', '🎉 Your Virtual ID Card has been generated successfully!');
    }

    public function render()
    {
        return view('livewire.virtual-cards.public-member-card-application');
    }
}
