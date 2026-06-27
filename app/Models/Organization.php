<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Organization extends Model
{
    use HasFactory, SoftDeletes;
    // القيم المسموحة - مركزية بمكان واحد، تستخدمها بالـ validation وبأي مكان تاني
    public const REGIONS = ['gaza', 'west_bank', 'jerusalem'];
    public const TYPES = ['ngo', 'private_company', 'government'];
    public const STATUS_INCOMPLETE = 'incomplete';
    public const STATUS_PENDING_REVIEW = 'pending_review';
    public const STATUS_NEEDS_CHANGES = 'needs_changes';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';
    public const MAIN_FIELDS = ['name', 'region', 'organization_type']; // + admin_name (بجدول users)

    protected $fillable = [
        'name',
        'email',
        'phone',
        'description',
        'region',
        'organization_type',
        'agreed_to_terms',
        'status',
        'is_active',
        'rejection_reason',
        'review_notes',
        'approved_at',
        'approved_by',
        'suspended_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'suspended_at' => 'datetime',
        'agreed_to_terms' => 'boolean',
        'is_active' => 'boolean',
        'review_notes' => 'array',
    ];
    
    // كل المستخدمين التابعين لهاي المؤسسة (org_admin + employees)
    public function users()
    {
        return $this->hasMany(User::class)->withTrashed();
    }

    // مين السوبر ادمن الي وافق عليها
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by')->withDefault();
    }

    // Scopes مفيدة للفلترة
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING_REVIEW);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function canEditProfile(): bool
    {
        return in_array($this->status, [self::STATUS_INCOMPLETE, self::STATUS_NEEDS_CHANGES]);
    }
    // الحقول الناقصة المطلوبة قبل الإرسال للمراجعة
    public function missingRequiredFields(User $admin): array
    {
        $missing = [];

        if (!$admin->name)
            $missing[] = 'admin_name';
        if (!$this->name)
            $missing[] = 'organization_name';
        if (!$this->region)
            $missing[] = 'organization_region';
        if (!$this->organization_type)
            $missing[] = 'organization_type';

        return $missing;
    }
    public function canEditMainFields(): bool
    {
        return in_array($this->status, [self::STATUS_INCOMPLETE, self::STATUS_NEEDS_CHANGES]);
    }

    public function canEditMinorFields(): bool
    {
        // الحقول الثانوية مفتوحة بكل الحالات إلا rejected (وهو أساسًا محذوف ومش قادر يسجل دخول)
        return $this->status !== self::STATUS_REJECTED;
    }
    public function isSuspended(): bool
    {
        return $this->suspended_at !== null;
    }
}
