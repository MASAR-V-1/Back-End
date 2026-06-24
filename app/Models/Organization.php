<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Organization extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable = [
        'name',
        'email',
        'phone',
        'description',
        'status',
        'rejection_reason',
        'approved_at',
        'approved_by',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
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
