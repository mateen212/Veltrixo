<?php

namespace App\Traits;

use App\Models\Tenant;
use App\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait HasTenant
{
    public static function bootHasTenant(): void
    {
        static::addGlobalScope(new TenantScope());

        static::creating(function ($model) {
            if (empty($model->tenant_id) && $tenantId = static::resolveTenantId()) {
                $model->tenant_id = $tenantId;
            }
        });
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    protected static function resolveTenantId(): ?int
    {
        $user = auth()->user();

        if (!$user) {
            return null;
        }

        // Super admins don't have a tenant
        if ($user->hasRole('super_admin')) {
            return null;
        }

        return $user->tenant_id ?? null;
    }

    public function scopeForTenant($query, int $tenantId)
    {
        return $query->where($this->getTable() . '.tenant_id', $tenantId);
    }
}
