<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class VirtualIdCard extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'organization_id',
        'event_id',
        'member_id_number',
        'full_name',
        'email',
        'phone',
        'photo_path',
        'institution',
        'law_faculty',
        'admission_year',
        'completion_year',
        'custom_fields',
        'qr_token',
        'status',
        'card_template',
        'issued_at',
        'expires_at',
    ];

    protected $casts = [
        'custom_fields' => 'array',
        'card_template' => 'array',
        'issued_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
            if (empty($model->qr_token)) {
                $model->qr_token = 'VID-' . strtoupper(Str::random(16));
            }
            if (empty($model->member_id_number)) {
                $model->member_id_number = 'FALAS-' . date('Y') . '-' . str_pad(rand(100, 99999), 5, '0', STR_PAD_LEFT);
            }
            if (empty($model->issued_at)) {
                $model->issued_at = now();
            }
        });
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function getPhotoUrlAttribute(): ?string
    {
        if ($this->photo_path) {
            if (str_starts_with($this->photo_path, 'http://') || str_starts_with($this->photo_path, 'https://')) {
                return $this->photo_path;
            }
            return asset('storage/' . $this->photo_path);
        }
        return null;
    }

    public function getInstitutionLogoUrlAttribute(): ?string
    {
        // 1. Direct card template override
        if (!empty($this->card_template['logo_path'])) {
            $path = $this->card_template['logo_path'];
            return str_starts_with($path, 'http') ? $path : asset('storage/' . $path);
        }

        // 2. Organization ID card config
        if ($this->organization && !empty($this->organization->settings)) {
            $settings = is_array($this->organization->settings) ? $this->organization->settings : json_decode($this->organization->settings, true);
            $logoPath = $settings['id_card_config']['institution_logo_path'] ?? null;
            if ($logoPath) {
                return str_starts_with($logoPath, 'http') ? $logoPath : asset('storage/' . $logoPath);
            }
        }

        // 3. Organization base logo fallback
        if ($this->organization && !empty($this->organization->logo_path)) {
            $path = $this->organization->logo_path;
            return str_starts_with($path, 'http') ? $path : asset('storage/' . $path);
        }

        return null;
    }

    public function getQrCodeUrlAttribute(): string
    {
        $verifyUrl = route('virtual-cards.public.view', ['uuid' => $this->uuid]);
        return "https://api.qrserver.com/v1/create-qr-code/?size=400x400&data=" . urlencode($verifyUrl);
    }
}
