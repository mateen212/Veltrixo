<?php

namespace App\Models;

use App\Traits\HasWallet;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Activitylog\Models\Concerns\CausesActivity;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class User extends Authenticatable implements MustVerifyEmail, HasMedia
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;
    use HasRoles, CausesActivity, LogsActivity, InteractsWithMedia;
    use HasWallet;

    protected $fillable = [
        'tenant_id', 'name', 'email', 'password', 'phone', 'avatar',
        'status', 'account_status', 'referral_code', 'referred_by', 'loyalty_points',
        'two_factor_enabled', 'two_factor_secret', 'two_factor_recovery_codes',
        'two_factor_confirmed_at', 'preferences', 'timezone', 'locale',
        'last_login_at', 'last_login_ip',
    ];

    protected $hidden = ['password', 'remember_token', 'two_factor_secret'];

    protected function casts(): array
    {
        return [
            'email_verified_at'          => 'datetime',
            'two_factor_confirmed_at'    => 'datetime',
            'last_login_at'              => 'datetime',
            'password'                   => 'hashed',
            'two_factor_enabled'         => 'boolean',
            'two_factor_recovery_codes'  => 'array',
            'preferences'                => 'array',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logFillable()->logOnlyDirty();
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function referrer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referred_by');
    }

    public function referrals(): HasMany
    {
        return $this->hasMany(Referral::class, 'referrer_id');
    }

    public function wallet(): HasOne
    {
        return $this->hasOne(Wallet::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function deliveries(): HasMany
    {
        return $this->hasMany(Delivery::class);
    }

    public function addresses(): HasMany
    {
        return $this->morphMany(Address::class, 'addressable');
    }

    public function defaultAddress(): HasOne
    {
        return $this->morphOne(Address::class, 'addressable')->where('is_default', true);
    }

    public function loyaltyPoints(): HasMany
    {
        return $this->hasMany(LoyaltyPoint::class);
    }

    public function walletTransactions(): HasMany
    {
        return $this->hasMany(WalletTransaction::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function refundRequests(): HasMany
    {
        return $this->hasMany(RefundRequest::class);
    }

    public function rider(): HasOne
    {
        return $this->hasOne(Rider::class);
    }

    public function deviceSessions(): HasMany
    {
        return $this->hasMany(DeviceSession::class);
    }

    public function isCustomer(): bool
    {
        return $this->hasRole('customer');
    }

    public function isRider(): bool
    {
        return $this->hasRole('rider');
    }

    public function isAdmin(): bool
    {
        return $this->hasAnyRole(['super-admin', 'admin', 'manager']);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('avatar')->singleFile();
    }
}
