<?php

use Illuminate\Support\Facades\Broadcast;

// Default user channel
Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Tenant-wide admin channel — admin sees all real-time updates for their tenant
Broadcast::channel('tenant.{tenantId}', function ($user, $tenantId) {
    return $user->hasRole('admin') && (int) $user->tenant_id === (int) $tenantId;
});

// Admin-only alerts channel (wallet recharges, missed deliveries, etc.)
Broadcast::channel('admin-alerts.{tenantId}', function ($user, $tenantId) {
    return $user->hasRole('admin') && (int) $user->tenant_id === (int) $tenantId;
});

// Per-rider channel — rider gets their delivery assignments and updates
Broadcast::channel('rider.{riderId}', function ($user, $riderId) {
    if (!$user->hasRole('rider')) {
        return false;
    }
    return $user->rider && (int) $user->rider->id === (int) $riderId;
});

// Per-customer channel — customer gets delivery status and wallet events
Broadcast::channel('customer.{userId}', function ($user, $userId) {
    return $user->hasRole('customer') && (int) $user->id === (int) $userId;
});
