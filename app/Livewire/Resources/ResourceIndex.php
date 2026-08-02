<?php

namespace App\Livewire\Resources;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\SystemResource;
use App\Models\SystemFeedback;
use Illuminate\Support\Str;

#[Layout('layouts.app')]
#[Title('Resource Center')]
class ResourceIndex extends Component
{
    public string $activeTab = 'updates'; // 'updates', 'feedbacks'

    // Resource filtering & state
    public string $searchResource = '';
    public string $selectedCategory = 'all';

    // Feedback filtering & state
    public string $searchFeedback = '';
    public string $selectedType = 'all';
    public string $selectedStatus = 'all';

    // Feedback submission modal / form
    public bool $showFeedbackModal = false;
    public string $feedbackType = 'feedback';
    public string $feedbackSubject = '';
    public string $feedbackMessage = '';

    // Super admin resource creation/edit modal
    public bool $showResourceModal = false;
    public ?int $editingResourceId = null;
    public string $resourceTitle = '';
    public string $resourceContent = '';
    public string $resourceCategory = 'update';
    public string $resourcePriority = 'normal';
    public bool $resourceIsPublished = true;
    public bool $resourcePinned = false;

    // Super admin response modal
    public bool $showResponseModal = false;
    public ?int $respondingFeedbackId = null;
    public string $adminResponseStatus = 'under_review';
    public string $adminResponseText = '';

    protected function rules(): array
    {
        return [
            'feedbackType' => 'required|in:feedback,suggestion,request,complaint',
            'feedbackSubject' => 'required|string|max:255',
            'feedbackMessage' => 'required|string|min:10',
        ];
    }

    public function mount(): void
    {
        $this->ensureDefaultWorkspaceGuideExists();
    }

    public function ensureDefaultWorkspaceGuideExists(): void
    {
        if (SystemResource::count() === 0) {
            SystemResource::create([
                'uuid' => (string) Str::uuid(),
                'title' => 'AttendFlow Workspace & Quick Navigation Guide',
                'category' => 'guide',
                'priority' => 'high',
                'is_published' => true,
                'pinned' => true,
                'content' => "### Welcome to AttendFlow Workspace Guide 🎉\n\nFollow this step-by-step guide to run seamless events, register attendees, manage entry gates, and track access:\n\n**1. Create & Manage Events**\nSet up upcoming events, configure venue locations, capacities, dates, and customize invitation forms.\n\n**2. Register & Verify Attendees**\nAdd attendees, assign roles (VVIP, VIP, Speaker, Guest), issue QR passes, and verify registrations.\n\n**3. Setup Entry Gates**\nConfigure venue entry checkpoints, map gates to specific events, and assign security officers.\n\n**4. Launch QR Scanner**\nOpen live QR scanner dashboard on mobile or desktop to scan and grant access to attendees instantly.\n\n**5. Invite Team Members & Assign Privileges**\nInvite team members (Event Managers, Gate Security, Check-in Staff) under your organization, assign RBAC roles, and manage their gate assignments.\n\n---\n*Need help, customized features, or found an issue? Use the **Feedback & Complaints** tab to submit requests directly to the system administrators.*",
                'created_by' => auth()->id(),
            ]);
        }
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    // --- Org Admin Feedback Handlers ---
    public function openFeedbackModal(): void
    {
        $this->reset(['feedbackSubject', 'feedbackMessage']);
        $this->feedbackType = 'feedback';
        $this->showFeedbackModal = true;
    }

    public function closeFeedbackModal(): void
    {
        $this->showFeedbackModal = false;
        $this->resetValidation();
    }

    public function submitFeedback(): void
    {
        $this->validate([
            'feedbackType' => 'required|in:feedback,suggestion,request,complaint',
            'feedbackSubject' => 'required|string|max:255',
            'feedbackMessage' => 'required|string|min:10',
        ]);

        $user = auth()->user();

        SystemFeedback::create([
            'uuid' => (string) Str::uuid(),
            'organization_id' => $user->organization_id,
            'user_id' => $user->id,
            'type' => $this->feedbackType,
            'subject' => $this->feedbackSubject,
            'message' => $this->feedbackMessage,
            'status' => 'pending',
        ]);

        $orgName = $user->organization->name ?? 'Organization';
        $typeTitles = [
            'feedback' => '💬 Workspace Feedback Received',
            'suggestion' => '💡 New Feature Suggestion Received',
            'request' => '📩 Support / Feature Request Received',
            'complaint' => '⚠️ Workspace Complaint Submitted',
        ];

        $title = $typeTitles[$this->feedbackType] ?? '📩 New Workspace Message';

        \App\Services\AdminNotificationService::sendSuperAdmin(
            $title,
            "{$orgName} (Admin: {$user->name}) submitted: \"{$this->feedbackSubject}\"",
            $this->feedbackType === 'complaint' ? 'error' : 'info',
            route('resources.index')
        );

        session()->flash('resource_success', 'Your message has been submitted successfully to the Super Admin!');
        $this->closeFeedbackModal();
        $this->activeTab = 'feedbacks';
    }

    // --- Super Admin Resource Management Handlers ---
    public function openResourceModal(?int $id = null): void
    {
        if (!auth()->user()->isSuperAdmin()) {
            return;
        }

        $this->resetValidation();

        if ($id) {
            $resource = SystemResource::findOrFail($id);
            $this->editingResourceId = $resource->id;
            $this->resourceTitle = $resource->title;
            $this->resourceContent = $resource->content;
            $this->resourceCategory = $resource->category;
            $this->resourcePriority = $resource->priority;
            $this->resourceIsPublished = $resource->is_published;
            $this->resourcePinned = $resource->pinned;
        } else {
            $this->editingResourceId = null;
            $this->resourceTitle = '';
            $this->resourceContent = '';
            $this->resourceCategory = 'update';
            $this->resourcePriority = 'normal';
            $this->resourceIsPublished = true;
            $this->resourcePinned = false;
        }

        $this->showResourceModal = true;
    }

    public function closeResourceModal(): void
    {
        $this->showResourceModal = false;
        $this->editingResourceId = null;
    }

    public function saveResource(): void
    {
        if (!auth()->user()->isSuperAdmin()) {
            return;
        }

        $this->validate([
            'resourceTitle' => 'required|string|max:255',
            'resourceContent' => 'required|string|min:10',
            'resourceCategory' => 'required|in:guide,update,announcement,feature,maintenance',
            'resourcePriority' => 'required|in:normal,important,high',
        ]);

        if ($this->editingResourceId) {
            $resource = SystemResource::findOrFail($this->editingResourceId);
            $resource->update([
                'title' => $this->resourceTitle,
                'content' => $this->resourceContent,
                'category' => $this->resourceCategory,
                'priority' => $this->resourcePriority,
                'is_published' => $this->resourceIsPublished,
                'pinned' => $this->resourcePinned,
            ]);
            session()->flash('resource_success', 'Resource update saved successfully!');
        } else {
            SystemResource::create([
                'uuid' => (string) Str::uuid(),
                'title' => $this->resourceTitle,
                'content' => $this->resourceContent,
                'category' => $this->resourceCategory,
                'priority' => $this->resourcePriority,
                'is_published' => $this->resourceIsPublished,
                'pinned' => $this->resourcePinned,
                'created_by' => auth()->id(),
            ]);
            session()->flash('resource_success', 'New resource update published successfully!');
        }

        $this->closeResourceModal();
    }

    public function deleteResource(int $id): void
    {
        if (!auth()->user()->isSuperAdmin()) {
            return;
        }

        SystemResource::destroy($id);
        session()->flash('resource_success', 'Resource update deleted successfully!');
    }

    public function toggleResourcePublish(int $id): void
    {
        if (!auth()->user()->isSuperAdmin()) {
            return;
        }

        $resource = SystemResource::findOrFail($id);
        $resource->update(['is_published' => !$resource->is_published]);
    }

    public function toggleResourcePin(int $id): void
    {
        if (!auth()->user()->isSuperAdmin()) {
            return;
        }

        $resource = SystemResource::findOrFail($id);
        $resource->update(['pinned' => !$resource->pinned]);
    }

    // --- Super Admin Feedback Response Handlers ---
    public function openResponseModal(int $feedbackId): void
    {
        if (!auth()->user()->isSuperAdmin()) {
            return;
        }

        $feedback = SystemFeedback::findOrFail($feedbackId);
        $this->respondingFeedbackId = $feedback->id;
        $this->adminResponseStatus = $feedback->status === 'pending' ? 'under_review' : $feedback->status;
        $this->adminResponseText = $feedback->admin_response ?? '';
        $this->showResponseModal = true;
    }

    public function closeResponseModal(): void
    {
        $this->showResponseModal = false;
        $this->respondingFeedbackId = null;
    }

    public function submitAdminResponse(): void
    {
        if (!auth()->user()->isSuperAdmin()) {
            return;
        }

        $this->validate([
            'adminResponseStatus' => 'required|in:pending,under_review,resolved,dismissed',
            'adminResponseText' => 'nullable|string',
        ]);

        $feedback = SystemFeedback::findOrFail($this->respondingFeedbackId);
        $feedback->update([
            'status' => $this->adminResponseStatus,
            'admin_response' => $this->adminResponseText,
            'responded_at' => now(),
        ]);

        session()->flash('resource_success', 'Feedback status & response updated successfully!');
        $this->closeResponseModal();
    }

    public function render()
    {
        $user = auth()->user();
        $isSuperAdmin = $user->isSuperAdmin();

        // Query Resources / Updates
        $resourcesQuery = SystemResource::query();
        if (!$isSuperAdmin) {
            $resourcesQuery->published();
        }

        if ($this->selectedCategory !== 'all') {
            $resourcesQuery->where('category', $this->selectedCategory);
        }

        if (!empty($this->searchResource)) {
            $resourcesQuery->where(function ($q) {
                $q->where('title', 'like', '%' . $this->searchResource . '%')
                  ->orWhere('content', 'like', '%' . $this->searchResource . '%');
            });
        }

        $resources = $resourcesQuery->pinnedFirst()->get();

        // Query Feedbacks
        $feedbacksQuery = SystemFeedback::with(['organization', 'user']);

        if (!$isSuperAdmin) {
            $feedbacksQuery->forOrganization($user->organization_id);
        }

        if ($this->selectedType !== 'all') {
            $feedbacksQuery->where('type', $this->selectedType);
        }

        if ($this->selectedStatus !== 'all') {
            $feedbacksQuery->where('status', $this->selectedStatus);
        }

        if (!empty($this->searchFeedback)) {
            $feedbacksQuery->where(function ($q) {
                $q->where('subject', 'like', '%' . $this->searchFeedback . '%')
                  ->orWhere('message', 'like', '%' . $this->searchFeedback . '%');
            });
        }

        $feedbacks = $feedbacksQuery->latest()->get();

        return view('livewire.resources.resource-index', [
            'resources' => $resources,
            'feedbacks' => $feedbacks,
            'isSuperAdmin' => $isSuperAdmin,
        ]);
    }
}
