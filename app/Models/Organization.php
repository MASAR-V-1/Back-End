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
    protected $fillable = [
        'name',
        'email',
        'phone',
        'description',
        'region',
        'organization_type',
        'agreed_to_terms',
        'status',
        'rejection_reason',
        'approved_at',
        'approved_by',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'agreed_to_terms' => 'boolean',
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
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }
}
