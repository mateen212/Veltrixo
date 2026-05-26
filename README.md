<div align="center">

# Veltrixo

### Enterprise Multi-Tenant Recurring Delivery SaaS Platform

[![PHP 8.4](https://img.shields.io/badge/PHP-8.4-blue?logo=php)](https://php.net)
[![Laravel 13](https://img.shields.io/badge/Laravel-13-red?logo=laravel)](https://laravel.com)
[![Vue 3 + Inertia](https://img.shields.io/badge/Vue%203-Inertia.js-green?logo=vuedotjs)](https://inertiajs.com)
[![TypeScript](https://img.shields.io/badge/TypeScript-5.x-blue?logo=typescript)](https://typescriptlang.org)
[![Tailwind CSS 3](https://img.shields.io/badge/Tailwind-3.x-38bdf8?logo=tailwindcss)](https://tailwindcss.com)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](LICENSE)

**Production-grade SaaS platform for recurring delivery businesses — dairy farms, meal kits, grocery boxes, water delivery, and more. Fully multi-tenant, role-based, wallet-driven, and real-time.**

[Live Demo](#) · [API Docs](#) · [Architecture Diagram](#) · [Changelog](#)

</div>

---

## Table of Contents

- [What is Veltrixo?](#what-is-veltrixo)
- [Key Features](#key-features)
- [Screenshots](#screenshots)
- [Architecture Overview](#architecture-overview)
- [Tech Stack](#tech-stack)
- [Business Workflows](#business-workflows)
  - [Super Admin](#super-admin-flow)
  - [Admin / Manager](#admin--manager-flow)
  - [Rider](#rider-flow)
  - [Customer](#customer-flow)
  - [Support Staff](#support-staff-flow)
- [Core Business Logic](#core-business-logic)
- [Database Schema](#database-schema)
- [Folder Structure](#folder-structure)
- [Queue Architecture](#queue-architecture)
- [Real-Time Architecture](#real-time-architecture)
- [Multi-Tenancy Model](#multi-tenancy-model)
- [Installation Guide](#installation-guide)
- [Environment Setup](#environment-setup)
- [Queue & Horizon Setup](#queue--horizon-setup)
- [WebSocket / Reverb Setup](#websocket--reverb-setup)
- [PWA Support](#pwa-support)
- [Deployment](#deployment)
- [Security Architecture](#security-architecture)
- [Testing Strategy](#testing-strategy)
- [API Architecture](#api-architecture)
- [Performance Optimizations](#performance-optimizations)
- [Scaling & Future Roadmap](#scaling--future-roadmap)
- [Contributing](#contributing)
- [License](#license)

---

## What is Veltrixo?

Veltrixo is a **production-ready, multi-tenant SaaS platform** purpose-built for businesses that run **recurring physical delivery operations** — think dairy farms, water delivery companies, meal kit services, organic produce subscriptions, and any business that delivers something to a customer's door on a repeating schedule.

It solves a real operational problem: **how do you manage hundreds of customers, each with different delivery frequencies, custom product mixes, flexible pause/skip rules, wallet balances, and a fleet of riders — reliably, every single day?**

Veltrixo does exactly that. One installation can power **multiple completely isolated businesses (tenants)**, each with their own customers, riders, products, pricing, and analytics. Each business pays Veltrixo on a SaaS plan, and the platform handles everything else.

### Who uses it?

| Business Type | Use Case |
|---|---|
| Dairy farms | Daily milk/yoghurt home delivery |
| Meal kit companies | Weekly recipe box subscriptions |
| Organic grocery | Bi-weekly vegetable/fruit baskets |
| Water delivery | Monthly water jar delivery |
| Bakeries | Morning bread delivery routes |
| Pharmacy | Recurring medicine delivery |

---

## Key Features

### Platform (SaaS Layer)
- **True multi-tenancy** — fully isolated data per business
- **SaaS subscription plans** for tenant billing (Basic, Pro, Enterprise)
- **Tenant onboarding** — instant account provisioning
- **Platform-wide analytics** — revenue, active tenants, churn

### Subscription Engine
- **Flexible frequencies** — daily, weekly, bi-weekly, monthly
- **Custom product mix** per subscription (e.g. 2 litres milk + 1 yoghurt)
- **Pause & resume** — customer pauses during vacation
- **Skip individual dates** — skip next Monday's delivery
- **Product swaps** — change items without cancelling
- **Auto-renewal** — subscriptions renew automatically
- **Delivery cut-off rules** — configurable per tenant

### Delivery Management
- **Auto-generation** — system pre-creates all deliveries for the week/month
- **Bulk rider assignment** — assign 50 deliveries in one click
- **Route optimisation** — grouped deliveries by geography
- **Real-time status tracking** — pending → assigned → in-progress → delivered
- **Missed delivery handling** — auto-detect and alert
- **Delivery proof upload** — photo + GPS coordinates
- **OTP/QR verification** — customer signs off on delivery

### Wallet System
- **Pre-funded wallet** per customer (PKR-based)
- **Auto-deduction** on delivery confirmation
- **Recharge requests** — customer submits, admin approves
- **Low balance alerts** — push + in-app
- **Transaction ledger** — full audit trail
- **Refund processing** — approved refunds credited back

### Rider Portal
- **Mobile-first** Progressive Web App
- **Online/offline toggle** — rider declares availability
- **Assigned route view** — optimised delivery list
- **One-tap delivery actions** — start, complete, fail
- **GPS location capture** — on each delivery event
- **Earnings dashboard** — daily/weekly earnings

### Customer Portal
- **Subscription management** — full self-service
- **Wallet top-up** — JazzCash, EasyPaisa, bank transfer, cash, debit/credit card
- **Delivery history** — with invoice downloads (PDF)
- **Loyalty points** — earn on every delivery
- **Referral system** — earn credit for referrals
- **Notification centre** — delivery updates, alerts
- **Multiple addresses** — home, office, etc.

### Technical
- **Server-side rendering (SSR)** via Inertia.js + Vue 3
- **Real-time events** via Laravel Reverb (WebSockets)
- **Background jobs** via Laravel Horizon (Redis queues)
- **Activity logging** via Spatie ActivityLog
- **Media uploads** via Spatie MediaLibrary + S3
- **PDF generation** via barryvdh/laravel-dompdf
- **Excel exports** via Maatwebsite/Excel
- **Full-text search** via Laravel Scout
- **RBAC** via Spatie Laravel-Permission

---

## Screenshots

> Screenshots directory: `public/screenshots/`

| Dashboard | Deliveries | Rider App | Customer Portal |
|---|---|---|---|
| *(coming soon)* | *(coming soon)* | *(coming soon)* | *(coming soon)* |

---

## Architecture Overview

---

## Subdomain Multi-Tenancy

Every approved business gets a unique subdomain: `{slug}.veltrixo.com`.

### How it works

```
Request: freshmilk.veltrixo.com/admin/dashboard
         │
         ▼
   Nginx (wildcard *.veltrixo.com)
         │
         ▼
   InitializeTenancyBySubdomain   ← global web middleware
   • extracts "freshmilk" from Host header
   • finds Tenant where subdomain = "freshmilk"
   • calls TenantContext::set($tenant)
   • unknown subdomain → 404 Errors/TenantNotFound
         │
         ▼
   require.tenant middleware       ← all tenant domain routes
   • ensures TenantContext is set
         │
         ▼
   tenant.active middleware
   • suspended  → 403 Errors/TenantSuspended
   • pending    → 403 Business/Pending
   • rejected   → 403 Errors/TenantNotFound
         │
         ▼
   tenant.user middleware          ← authenticated routes only
   • verifies auth user belongs to this tenant
   • cross-tenant access → logout + redirect to correct domain
   • super_admin role is exempt (can inspect all tenants)
         │
         ▼
   Controller
```

### Central domain (veltrixo.com)

Super admin panel and public pages live on the root domain, protected by `only.central` middleware which aborts 404 if a tenant subdomain is detected.

### Middleware stack

| Alias | Class | Applied to |
|---|---|---|
| *(global web)* | `InitializeTenancyBySubdomain` | Every request |
| `require.tenant` | `RequireTenantContext` | All tenant domain routes |
| `tenant.active` | `EnsureTenantActive` | All tenant domain routes |
| `tenant.user` | `PreventCrossTenantAccess` | Authenticated tenant routes |
| `only.central` | `OnlyCentralDomain` | Central/super-admin routes |

### Key classes

| Class | Purpose |
|---|---|
| `App\Support\TenantContext` | Static singleton holding current tenant for request/job lifecycle |
| `App\Support\TenantUrl` | Generates `https://{subdomain}.veltrixo.com/path` URLs |
| `App\Jobs\Middleware\WithTenantContext` | Restores tenant context inside queued jobs |

### Local development

1. Add wildcard hosts to `/etc/hosts`:
   ```
   127.0.0.1  veltrixo.test
   127.0.0.1  alnoor.veltrixo.test
   127.0.0.1  freshmilk.veltrixo.test
   ```
   Or use `dnsmasq` to wildcard `*.veltrixo.test → 127.0.0.1`.

2. Set in `.env`:
   ```env
   APP_DOMAIN=veltrixo.test
   CENTRAL_DOMAIN=veltrixo.test
   APP_URL=http://veltrixo.test
   ```

3. Nginx already configured for `*.veltrixo.test` in `docker/nginx/delivery-saas.conf`.

### Tenant URL generation

```php
// In PHP
TenantUrl::to('/admin/dashboard', $tenant);   // https://alnoor.veltrixo.com/admin/dashboard
$tenant->url('/invoices');                     // https://alnoor.veltrixo.com/invoices

// In Vue (via Inertia shared prop)
// $page.props.currentTenant.subdomain
```

### Queue job isolation

Jobs that carry a `$tenantId` use `WithTenantContext` job middleware:
```php
public function middleware(): array {
    return [new WithTenantContext($this->tenantId)];
}
```

Jobs that operate on a `Delivery` model resolve the tenant from `$delivery->tenant_id` inside `handle()`.

---

```
┌─────────────────────────────────────────────────────────┐
│                    VELTRIXO PLATFORM                     │
│                                                         │
│  ┌───────────┐  ┌────────────┐  ┌───────────────────┐  │
│  │  Browser  │  │  Mobile    │  │  External API     │  │
│  │  (SSR +   │  │  PWA       │  │  (REST / Webhook) │  │
│  │   SPA)    │  │            │  │                   │  │
│  └─────┬─────┘  └─────┬──────┘  └─────────┬─────────┘  │
│        └──────────────┴──────────────┬─────┘            │
│                                      ▼                  │
│                    ┌─────────────────────────┐          │
│                    │     Nginx (Port 80/443)  │          │
│                    └────────────┬────────────┘          │
│                                 ▼                       │
│                    ┌─────────────────────────┐          │
│                    │  Laravel 13 (PHP-FPM)   │          │
│                    │  + Inertia.js SSR        │          │
│                    └──────┬──────────┬────────┘          │
│                           │          │                  │
│              ┌────────────┘          └───────────┐      │
│              ▼                                   ▼      │
│  ┌───────────────────┐          ┌────────────────────┐  │
│  │   MySQL 8 (DB)    │          │  Laravel Reverb    │  │
│  │   (per-tenant     │          │  (WebSocket Server)│  │
│  │   row isolation)  │          │  Port 8080         │  │
│  └───────────────────┘          └────────────────────┘  │
│              ▼                                          │
│  ┌───────────────────┐          ┌────────────────────┐  │
│  │   Redis           │          │  S3-compatible     │  │
│  │   (Cache+Queue)   │          │  Object Storage    │  │
│  └───────────────────┘          └────────────────────┘  │
│              ▼                                          │
│  ┌───────────────────┐                                  │
│  │  Laravel Horizon  │                                  │
│  │  (Queue Workers)  │                                  │
│  └───────────────────┘                                  │
└─────────────────────────────────────────────────────────┘
```

---

## Tech Stack

### Backend
| Layer | Technology |
|---|---|
| Language | PHP 8.4 |
| Framework | Laravel 13.x |
| Auth | Laravel Breeze + Sanctum |
| RBAC | Spatie Laravel-Permission 7.x |
| Queue | Laravel Horizon + Redis |
| WebSockets | Laravel Reverb |
| Full-text search | Laravel Scout |
| PDF | barryvdh/laravel-dompdf |
| Excel | Maatwebsite/Excel |
| Media | Spatie MediaLibrary + AWS S3 |
| Activity Log | Spatie ActivityLog |
| QR Codes | SimpleSoftwareIO/simple-qrcode |
| Settings | Spatie Laravel-Settings |
| Multi-tenancy | Row-level isolation (tenant_id) |

### Frontend
| Layer | Technology |
|---|---|
| Framework | Vue 3 (Composition API) |
| Routing | Inertia.js 2.x (SPA + SSR) |
| Type Safety | TypeScript 5.x |
| Styling | Tailwind CSS 3.x |
| Icons | Heroicons v2 |
| Build | Vite 6 |
| SSR | Node.js (ssr.ts) |

### Infrastructure
| Layer | Technology |
|---|---|
| Web Server | Nginx |
| PHP Runtime | PHP-FPM 8.4 |
| Database | MySQL 8.0 |
| Cache/Queue | Redis 7 |
| Object Storage | S3-compatible (AWS / MinIO) |
| Dev Environment | Laradock (Docker Compose) |
| Container | Docker (PHP 8.4-FPM Alpine) |

---

## Business Workflows

### Super Admin Flow

The Super Admin is the platform owner — they manage the entire SaaS infrastructure, onboard new businesses, and monitor platform-wide revenue and health.

**Dashboard widgets:**
- Total active tenants
- Monthly Recurring Revenue (PKR)
- New sign-ups this month
- Platform-wide delivery count
- Failed payments / pending wallet approvals
- Top-performing tenants by revenue

**Onboarding a new business:**
1. Super Admin creates a new **Tenant** record (business name, domain, contact)
2. Assigns a **SaaS plan** (Basic: up to 200 customers, Pro: unlimited)
3. System provisions the tenant automatically — creates default roles, settings, and seed data
4. Admin credentials are sent to the business owner
5. Business owner logs in and begins configuring their products and customers

**SaaS plan management:**
- Create/edit plans with feature flags and customer limits
- Monitor which tenants are on which plan
- Upgrade/downgrade tenant plans
- Handle tenant billing (external or integrated)

**Platform monitoring:**
- View all deliveries across all tenants (read-only audit)
- Platform error logs via Telescope (if enabled)
- Redis/queue health via Horizon
- WebSocket connection health via Reverb dashboard

**Revenue tracking:**
- PKR wallet recharge volume per month
- Subscription revenue estimates per tenant
- Refund/chargeback tracking

---

### Admin / Manager Flow

The Admin is the business owner or operations manager for a specific tenant. They manage everything within their business.

**Logging in:**
- Admin lands on `/admin/dashboard`
- Sees today's delivery summary: total, pending, assigned, completed, missed
- Real-time updates via WebSocket — numbers change as riders complete deliveries

**Managing products:**
1. Navigate to **Products** → create products (e.g. "Full Cream Milk 1L", "Yoghurt 500g")
2. Set price (e.g. Rs 180), unit (litre/kg/piece), category, active status
3. Product variants for different sizes/weights
4. Products appear in the subscription creation flow for customers

**Managing customers:**
1. Add customers manually or let them self-register
2. View each customer's active subscriptions, wallet balance, delivery history
3. Adjust wallet balance directly (credit/debit with reason)
4. Handle pause/cancel requests
5. View loyalty points balance

**Subscription management:**
1. View all active subscriptions with status filters
2. Drill into any subscription — see items, delivery history, next scheduled date
3. Pause on behalf of customer, resume, or cancel
4. Bulk operations — pause all subscriptions for a date range (e.g. Eid holidays)

**Delivery workflow (daily operations):**
1. System auto-generates deliveries each morning via scheduled job
2. Admin reviews the day's delivery list at `/admin/deliveries`
3. Filters by status (pending), area, or rider
4. Selects multiple deliveries → bulk assigns to a rider
5. Rider receives notification instantly (WebSocket push)
6. Monitors delivery progress in real-time — status updates as rider works
7. At end of day, reviews missed deliveries and takes action (reschedule/refund)

**Wallet management:**
1. Customer submits a recharge request (Rs 500 via JazzCash)
2. Admin sees pending requests at `/admin/wallets`
3. Verifies payment proof/reference number
4. Approves → system credits customer wallet instantly
5. Or rejects with reason → customer notified
6. Admin can manually credit wallet for complaints/refunds

**Rider management:**
1. Create rider accounts — name, phone, vehicle type
2. Assign riders to delivery routes
3. Monitor which riders are online/offline in real-time
4. View rider's delivery completion rate, average time
5. Handle rider complaints

**Analytics:**
- Daily/weekly/monthly delivery volumes
- Revenue collected via wallet
- Customer churn (cancelled subscriptions)
- Rider performance metrics
- Product popularity by subscription volume
- Peak delivery days

---

### Rider Flow

The Rider is the delivery person. Their entire experience is designed around a **mobile-first Progressive Web App** — no app store installation needed.

**Starting the day:**
1. Rider opens Veltrixo on their phone browser (PWA installed on home screen)
2. Logs in → lands on `/rider/dashboard`
3. Sees today's assigned deliveries (count, map overview if enabled)
4. Toggles the **Online** switch in the top bar — this broadcasts real-time availability to admin
5. Today's KPIs show: assigned, completed, remaining, earnings estimate

**Delivery workflow (per delivery):**
1. Opens the Deliveries list at `/rider/deliveries`
2. Sees deliveries sorted by route order (optimised by geography)
3. Taps a delivery card → sees:
   - Customer name and address
   - Items to deliver (e.g. "2× Full Cream Milk 1L")
   - Customer phone number (tap to call)
   - Map pin for navigation
4. Taps **Start Delivery** → status changes to `in_progress`, GPS captured, timestamp recorded
5. Arrives at customer's door → delivers items
6. Taps **Complete Delivery** → one of:
   - Customer signs on screen (digital signature)
   - Customer provides OTP (sent to their phone)
   - Rider scans QR code on customer's subscription card
7. Optional: upload a photo of delivered items at the door
8. System records GPS coordinates, timestamp, delivery proof
9. Wallet deduction triggers automatically for the customer
10. Invoice generated in background job

**Handling issues:**
- Mark as **Missed** with reason (not home, address wrong, customer rejected)
- Add notes for admin
- Missed deliveries appear in admin dashboard for action

**Earnings:**
- Rider sees daily/weekly earnings breakdown
- Per-delivery commission calculated automatically
- Weekly summary available

**Offline capability:**
- Rider can view assigned deliveries offline (cached via PWA)
- Status updates are queued locally and sync when back online
- Critical for areas with poor connectivity

---

### Customer Flow

The Customer is the end consumer — they interact with the platform to manage their recurring deliveries.

**Onboarding:**
1. Customer registers (or is added by admin)
2. Receives welcome notification
3. Prompted to set up their **delivery address**
4. Prompted to **top up wallet** (minimum Rs 500 recommended)
5. Creates first subscription

**Creating a subscription:**
1. Goes to `/customer/subscriptions` → "New Subscription"
2. Selects delivery frequency: daily / weekly / bi-weekly / monthly
3. Selects products and quantities (e.g. 2 litres milk + 500g yoghurt)
4. Confirms delivery address
5. Sees pricing summary (e.g. Rs 460/delivery)
6. Confirms → subscription is active immediately
7. First delivery is scheduled for the next valid delivery date

**Day-to-day usage:**
- Customer receives push/SMS notification morning of delivery: "Your delivery is on its way"
- Real-time status update: "Rider is 3 stops away" (if live tracking enabled)
- Delivery completed → notification: "Delivered! Rs 460 deducted from wallet"
- Wallet balance shown in header at all times

**Self-service operations:**
- **Pause subscription** — going on vacation? Pause from date A to date B
- **Skip a delivery** — skip just next Wednesday without pausing the whole subscription
- **Swap a product** — change "1L milk" to "2L milk" for next month
- **Change address** — update delivery address for upcoming deliveries
- **Add another subscription** — e.g. add a Saturday grocery box
- **Cancel subscription** — with reason, effective immediately or end-of-period

**Wallet management:**
1. Customer goes to `/customer/wallet`
2. Sees current balance (e.g. Rs 1,240.00)
3. Taps **Recharge Wallet**
4. Enters amount (Rs 1,000), selects payment method (JazzCash/EasyPaisa/bank transfer/cash)
5. Enters transaction reference/UTR number
6. Submits → admin receives notification to verify
7. Admin approves → Rs 1,000 credited within minutes
8. Full transaction history visible with credits and debits

**Invoices & history:**
- Every delivery generates a PDF invoice
- Customer downloads from Deliveries → Show
- Monthly summary invoices also available

**Loyalty & referrals:**
- Earn loyalty points on every delivery
- Refer a friend → earn Rs 100 wallet credit when they subscribe
- Points redeemable against wallet balance

---

### Support Staff Flow

Support staff handle customer complaints and operational issues. They have read access to customer data and limited action capabilities.

**Common workflows:**
1. Customer calls: "My delivery didn't arrive today"
   - Support opens customer account → views today's delivery status
   - If marked missed: raises refund request or reschedules
   - If still pending: contacts rider directly or escalates to admin

2. Customer reports incorrect items delivered:
   - Support logs a complaint against the delivery
   - Tags for admin review
   - Initiates partial refund to wallet if needed

3. Wallet dispute:
   - Customer says "I recharged Rs 2,000 but balance not updated"
   - Support views pending recharge requests
   - Escalates to finance/admin for approval

4. Subscription confusion:
   - Walks customer through pausing/skipping
   - Can update delivery address on behalf of customer

---

## Core Business Logic

### Recurring Subscription Engine

The subscription engine is the heart of Veltrixo. Here is how it works:

1. **Subscription creation**: Customer defines frequency + product mix + address. The system calculates the first delivery date based on cut-off rules (e.g. "orders before 9 PM are delivered next morning").

2. **Delivery generation**: A scheduled job (`GenerateDeliveriesJob`) runs daily (or weekly for weekly subscriptions). It reads all active subscriptions and creates `Delivery` records for the upcoming period. Each delivery is linked to its subscription and pre-populated with items from `SubscriptionItems`.

3. **Skip handling**: If a customer has requested a skip for a date, the job checks `SubscriptionSkips` and skips that date. The delivery is not created.

4. **Pause handling**: If subscription status is `paused`, no deliveries are generated until the subscription is `resumed`. If a `pause_until` date is set, the system auto-resumes on that date.

5. **Missed deliveries**: Another scheduled job runs each evening to check deliveries that are still `pending` or `assigned` past the expected delivery window. These are marked `missed` and alerts sent to admin.

### Wallet Deduction Workflow

Every delivery completion triggers a precise wallet deduction flow:

```
Delivery marked "delivered"
    → DeliveryObserver fires
    → WalletService::deductForDelivery($delivery)
        → Load customer wallet
        → Check sufficient balance
        → Create WalletTransaction (debit, amount, reference=delivery_id)
        → Update wallet.balance
        → Fire WalletDebited event
            → GenerateInvoiceJob dispatched
            → SendDeliveryReceiptNotification dispatched
    → If insufficient balance → flag delivery, alert admin
```

### Rider Assignment Logic

The bulk assignment feature uses a simple algorithm:

1. Admin selects N deliveries from the filtered list
2. Selects a rider from the modal
3. System calls `BulkAssignDeliveries` action:
   - Validates rider exists and is active
   - Updates all selected deliveries: `rider_id = $rider->id`, `status = assigned`
   - Fires `DeliveriesAssigned` event per rider
   - Rider notified via WebSocket push + database notification

For automated assignment (future): deliveries can be auto-assigned based on geographic proximity to rider's last known location using the `rider_locations` table.

### Notification System

Veltrixo uses a layered notification system:

| Channel | Used For |
|---|---|
| Database | In-app notification bell |
| Broadcast (Reverb) | Real-time bell count update |
| Mail | Invoices, welcome emails |
| SMS (future) | Delivery OTP, low balance alerts |

All notifications extend Laravel's `Notification` class and route through `app/Notifications/`. Custom notification records are stored in `notifications_extended` table for richer data.

### Queue System

All time-consuming operations are queued to keep the web request fast:

| Job | Queue | Purpose |
|---|---|---|
| `GenerateDeliveriesJob` | `deliveries` | Daily delivery creation |
| `GenerateInvoiceJob` | `invoices` | PDF invoice per delivery |
| `SendDeliveryReceiptNotification` | `notifications` | Post-delivery notification |
| `ApproveWalletRechargeJob` | `wallets` | Process approved recharge |
| `GenerateAnalyticsReportJob` | `reports` | Nightly analytics aggregation |

### Real-Time WebSocket Events

Laravel Reverb powers all live updates. Key events:

| Event | Channel | Triggered When |
|---|---|---|
| `DeliveryStatusUpdated` | `tenant.{id}` | Rider updates delivery |
| `WalletDebited` | `customer.{id}` | Delivery completed |
| `RiderLocationUpdated` | `admin.{tenant}` | Rider moves (GPS) |
| `NewWalletRechargeRequest` | `admin.{tenant}` | Customer requests recharge |
| `DeliveriesAssigned` | `rider.{id}` | Admin assigns deliveries |

### Tenant Isolation

Veltrixo uses **row-level multi-tenancy**. Every table that contains business data has a `tenant_id` foreign key column. The `TenantScope` global scope is applied to all relevant models via `HasTenant` trait:

```php
// Automatically applied to every query:
SELECT * FROM deliveries WHERE tenant_id = 42 AND ...
```

The tenant is resolved from the authenticated user's `tenant_id` on every request. Super admins bypass this scope when needed.

---

## Database Schema

### Core Tables

| Table | Purpose |
|---|---|
| `users` | All users (admin/rider/customer), with `tenant_id` |
| `tenants` | SaaS tenants (businesses) |
| `tenant_plans` | SaaS billing plans |
| `tenant_subscriptions` | Tenant's current SaaS plan |
| `products` | Deliverable products per tenant |
| `product_variants` | Size/weight variants of products |
| `categories` | Product categories |
| `addresses` | Customer delivery addresses |
| `subscriptions` | Customer recurring subscription |
| `subscription_items` | Products in a subscription |
| `subscription_skips` | Scheduled skips |
| `deliveries` | Individual delivery instances |
| `delivery_items` | Items in each delivery |
| `wallets` | Customer wallet balance |
| `wallet_transactions` | Credit/debit ledger |
| `wallet_recharge_requests` | Pending recharge submissions |
| `invoices` / `invoice_items` | PDF invoice records |
| `riders` | Rider profile + vehicle |
| `rider_locations` | Real-time GPS tracking |
| `routes` / `route_stops` | Delivery route definitions |
| `refund_requests` | Customer refund requests |
| `loyalty_points` | Earned loyalty points |
| `referrals` | Referral tracking |
| `announcements` | Tenant-wide announcements |
| `device_sessions` | PWA push notification tokens |
| `notifications_extended` | Rich notification records |
| `scheduled_job_runs` | Job execution audit log |

---

## Folder Structure

```
Veltrixo/
├── app/
│   ├── Console/              # Scheduled commands
│   ├── Contracts/            # Interfaces (Repository pattern)
│   ├── DTOs/                 # Data Transfer Objects
│   ├── Events/               # Domain events (DeliveryCompleted, etc.)
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/        # Admin panel controllers
│   │   │   ├── Customer/     # Customer portal controllers
│   │   │   ├── Rider/        # Rider app controllers
│   │   │   └── Api/          # REST API controllers
│   │   ├── Middleware/       # Auth, tenant resolution, role gates
│   │   ├── Requests/         # Form validation (per-action)
│   │   └── Resources/        # API resource transformers
│   ├── Jobs/
│   │   ├── Delivery/         # GenerateDeliveries, MarkMissed
│   │   ├── Invoice/          # GenerateInvoice, EmailInvoice
│   │   ├── Notification/     # SendPush, SendSMS
│   │   ├── Report/           # GenerateAnalytics
│   │   └── Wallet/           # ProcessRecharge, ProcessRefund
│   ├── Listeners/            # Event listeners
│   ├── Models/               # Eloquent models (with HasTenant trait)
│   ├── Notifications/        # Laravel notification classes
│   ├── Observers/            # Model observers (DeliveryObserver, etc.)
│   ├── Policies/             # Authorization policies
│   ├── Repositories/         # Repository classes (abstracted DB access)
│   ├── Services/
│   │   ├── Analytics/        # KPI calculations
│   │   ├── Delivery/         # Delivery generation & lifecycle
│   │   ├── Notification/     # Notification dispatch logic
│   │   ├── Payment/          # Wallet + recharge processing
│   │   ├── Product/          # Product availability logic
│   │   ├── Report/           # Report generation
│   │   ├── Subscription/     # Subscription engine
│   │   ├── Tenant/           # Tenant provisioning
│   │   └── Wallet/           # Wallet deduction + credit
│   ├── Settings/             # Spatie settings classes
│   └── Traits/               # HasTenant, HasWallet, etc.
│
├── database/
│   ├── migrations/           # 36+ migrations (full schema)
│   ├── seeders/              # Demo data (PKR pricing)
│   └── factories/            # Model factories for testing
│
├── resources/js/
│   ├── Components/           # Reusable UI components
│   │   ├── AppButton.vue     # Button (5 variants, 4 sizes, loading)
│   │   ├── AppBadge.vue      # Status badge (6 colors)
│   │   ├── AppCard.vue       # Card wrapper
│   │   ├── AppModal.vue      # Accessible modal (4 sizes)
│   │   ├── AppInput.vue      # Form input with validation
│   │   ├── AppToast.vue      # Flash message toast
│   │   ├── DataTable.vue     # Generic sortable + selectable table
│   │   └── TablePagination.vue # Inertia-based pagination
│   ├── Layouts/
│   │   ├── AdminLayout.vue   # Dark sidebar CMS layout
│   │   ├── CustomerLayout.vue # Customer portal layout
│   │   └── RiderLayout.vue   # Mobile-first rider layout
│   ├── Pages/
│   │   ├── Admin/            # Admin panel pages (Deliveries, Products, etc.)
│   │   ├── Customer/         # Customer portal pages
│   │   └── Rider/            # Rider app pages
│   └── stores/               # Pinia stores (if used)
│
├── routes/
│   ├── web.php               # Role-grouped web routes
│   ├── api.php               # REST API routes
│   └── channels.php          # Broadcast channel auth
│
├── docker/
│   ├── nginx/                # Nginx site config (WebSocket proxying)
│   └── supervisor/           # Supervisor config (PHP-FPM + queue workers)
│
├── Dockerfile                # Production Docker image (PHP 8.4-FPM Alpine)
└── docker-compose.yml        # Dev compose (via Laradock)
```

---

## Queue Architecture

Veltrixo uses named queues for priority control, monitored by Laravel Horizon.

```
┌─────────────────────────────────────────────────┐
│               Laravel Horizon                   │
│                                                 │
│  Queue: default    → General operations         │
│  Queue: deliveries → Time-critical scheduling   │
│  Queue: invoices   → PDF generation             │
│  Queue: wallets    → Payment processing         │
│  Queue: notifications → Push/email dispatch     │
│  Queue: reports    → Analytics (low priority)   │
└─────────────────────────────────────────────────┘
```

**Scheduled jobs (Kernel):**

| Schedule | Job | Effect |
|---|---|---|
| Daily 6:00 AM | `GenerateDeliveriesCommand` | Creates today's deliveries |
| Daily 10:00 PM | `MarkMissedDeliveriesCommand` | Flags incomplete deliveries |
| Daily 11:00 PM | `GenerateAnalyticsCommand` | Aggregates daily KPIs |
| Weekly Sunday | `GenerateWeeklyReportCommand` | Weekly summary emails |

---

## Real-Time Architecture

Laravel Reverb (self-hosted WebSocket server) handles all live updates.

```
Client (Vue 3)
    ↕ Echo.js / WebSocket
Laravel Reverb (Port 8080)
    ↕ Redis Pub/Sub
Laravel (Broadcasting)
```

**Channel types used:**

| Channel | Type | Auth |
|---|---|---|
| `tenant.{id}` | Private | Admin only |
| `rider.{id}` | Private | Rider only |
| `customer.{id}` | Private | Customer only |
| `admin-alerts.{tenant_id}` | Private | Admin only |

Channel authorization is handled in `routes/channels.php` using role-based policies.

---

## Multi-Tenancy Model

Veltrixo uses **shared-database, shared-schema multi-tenancy** with row-level isolation.

**Why this approach?**
- Simpler infrastructure (one database to manage)
- Shared connection pooling (efficient)
- Easy cross-tenant reporting for Super Admin
- Suitable for 10–500 tenants

**How it's enforced:**
1. Every data model uses the `HasTenant` trait
2. The trait registers a `GlobalScope` that appends `WHERE tenant_id = ?` to every query
3. Middleware resolves the tenant from the authenticated user on each request
4. Super Admin can bypass via `withoutGlobalScope(TenantScope::class)`

**Tenant data is fully isolated:**
- Products: tenant A's products never appear for tenant B
- Customers: completely separate user pools per tenant
- Deliveries, wallets, subscriptions: all scoped

---

## Installation Guide

### Prerequisites

- Docker + Docker Compose
- Laradock (cloned alongside this project)
- Node.js 20+ (on host, for asset building)

### Directory Structure

```
~/projects/
├── laradock/           ← Laradock Docker setup
└── laravel/
    └── delivery-saas/  ← Veltrixo project root
```

### Step 1 — Clone the project

```bash
git clone https://github.com/mateen212/Veltrixo.git ~/projects/laravel/delivery-saas
```

### Step 2 — Start Laradock services

```bash
cd ~/projects/laradock
docker compose up -d nginx mysql redis workspace
```

### Step 3 — Enter the workspace container

All backend commands must be run inside Docker:

```bash
docker compose exec workspace bash
cd /var/www/delivery-saas
```

### Step 4 — Install PHP dependencies

```bash
composer install
```

### Step 5 — Configure environment

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` — see [Environment Setup](#environment-setup) below.

### Step 6 — Run migrations and seed demo data

```bash
php artisan migrate --seed
```

This creates:
- Super Admin: `superadmin@veltrixo.test` / `password`
- Demo Tenant with Admin: `admin@demo.test` / `password`
- Demo Customer: `customer@demo.test` / `password`
- Demo Rider: `rider@demo.test` / `password`
- Sample products (PKR pricing), subscriptions, and deliveries

### Step 7 — Build frontend assets (on host)

```bash
cd ~/projects/laravel/delivery-saas
npm install
npm run build
```

### Step 8 — Set up storage symlink (inside container)

```bash
docker compose exec workspace bash
cd /var/www/delivery-saas
php artisan storage:link
```

### Step 9 — Visit the application

```
http://localhost
```

---

## Environment Setup

Key `.env` variables to configure:

```dotenv
APP_NAME=Veltrixo
APP_URL=http://veltrixo.test

# Database (Laradock defaults)
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=delivery_saas
DB_USERNAME=root
DB_PASSWORD=root

# Redis (Laradock defaults)
REDIS_HOST=redis
REDIS_PORT=6379

# Queue & Cache drivers
QUEUE_CONNECTION=redis
CACHE_STORE=redis
SESSION_DRIVER=database

# Broadcasting (Laravel Reverb)
BROADCAST_CONNECTION=reverb
REVERB_APP_ID=veltrixo
REVERB_APP_KEY=your-reverb-app-key
REVERB_APP_SECRET=your-reverb-app-secret
REVERB_HOST=0.0.0.0
REVERB_PORT=8080

# Mail
MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025

# S3 / Object Storage
FILESYSTEM_DISK=s3
AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=
AWS_BUCKET=

# Currency
CURRENCY=PKR
CURRENCY_SYMBOL=Rs
```

---

## Queue & Horizon Setup

### Start queue workers (development)

Inside the workspace container:

```bash
cd /var/www/delivery-saas

# Start Horizon (monitors all queues)
php artisan horizon
```

Horizon dashboard: `http://veltrixo.test/horizon` (admin only)

### Run scheduled commands manually

```bash
php artisan delivery:generate          # Generate today's deliveries
php artisan delivery:mark-missed       # Flag incomplete deliveries
php artisan analytics:generate-daily   # Aggregate KPIs
```

### In production (Supervisor)

The included `docker/supervisor/supervisord.conf` manages:
- PHP-FPM process
- Laravel Horizon worker
- Laravel Reverb WebSocket server

```ini
[program:horizon]
command=php /var/www/Veltrixo/artisan horizon
autostart=true
autorestart=true

[program:reverb]
command=php /var/www/Veltrixo/artisan reverb:start
autostart=true
autorestart=true
```

---

## WebSocket / Reverb Setup

### Development

```bash
# Inside workspace container
cd /var/www/delivery-saas
php artisan reverb:start
```

### Production (via Supervisor)

Reverb runs on port 8080 and Nginx proxies `/app/` path to it:

```nginx
location /app/ {
    proxy_pass         http://reverb:8080;
    proxy_http_version 1.1;
    proxy_set_header   Upgrade $http_upgrade;
    proxy_set_header   Connection "Upgrade";
}
```

### Frontend (Echo)

The Vue frontend connects to Reverb via Laravel Echo (configured in `resources/js/bootstrap.ts`):

```typescript
import Echo from 'laravel-echo'
import Pusher from 'pusher-js'

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT,
    forceTLS: false,
    enabledTransports: ['ws'],
})
```

---

## PWA Support

The Rider portal is designed as a **Progressive Web App**:

- Add to home screen prompt on mobile
- Service Worker caches the delivery list for offline viewing
- Background sync queues status updates when offline
- Push notifications via Web Push API (device tokens stored in `device_sessions`)

Configure push notification keys in `.env`:

```dotenv
VAPID_PUBLIC_KEY=
VAPID_PRIVATE_KEY=
```

---

## Deployment

### Option A — Docker (recommended)

```bash
# Build the production image
docker build -t veltrixo:latest .

# Run with environment variables
docker run -d \
  --env-file .env.production \
  -p 80:80 \
  veltrixo:latest
```

The `Dockerfile` is a multi-stage Alpine build that:
1. Installs PHP 8.4-FPM with all required extensions
2. Installs Composer dependencies (production-only)
3. Builds frontend assets with Node.js
4. Removes `node_modules` from final image
5. Starts Supervisor (Nginx + PHP-FPM + Horizon + Reverb)

### Option B — Traditional VPS

```bash
# Server: Ubuntu 24.04 LTS
apt install php8.4-fpm php8.4-mysql php8.4-redis nginx mysql-client redis

git clone ... /var/www/Veltrixo
cd /var/www/Veltrixo
composer install --no-dev --optimize-autoloader
php artisan config:cache
php artisan route:cache
php artisan view:cache
npm ci && npm run build
```

### Post-deployment commands

```bash
php artisan migrate --force
php artisan storage:link
php artisan horizon:terminate  # graceful restart of workers
supervisorctl restart all
```

---

## Security Architecture

- **Authentication**: Laravel Breeze (session-based) + Sanctum (API tokens)
- **RBAC**: Spatie Permission — roles: `super_admin`, `admin`, `rider`, `customer`
- **Tenant isolation**: Global scope on all models — cross-tenant data leakage impossible
- **CSRF protection**: Laravel's built-in CSRF middleware on all web routes
- **XSS prevention**: Inertia.js escapes all rendered data; CSP headers via Nginx
- **SQL injection**: Eloquent ORM with parameterised queries throughout
- **Rate limiting**: Laravel throttle middleware on auth and API routes
- **Security headers**: `X-Frame-Options`, `X-Content-Type-Options`, `X-XSS-Protection`, `Referrer-Policy` set in Nginx config
- **Input validation**: Dedicated `FormRequest` classes for every write operation
- **Activity logging**: Spatie ActivityLog records all admin and finance operations with actor, IP, payload
- **Sensitive data**: Wallet amounts and payment references stored encrypted where applicable

---

## Testing Strategy

```bash
# Run all tests (inside workspace container)
cd /var/www/delivery-saas
php artisan test

# Run a specific test suite
php artisan test --testsuite=Unit
php artisan test --testsuite=Feature

# With coverage
php artisan test --coverage
```

### Test suites

| Suite | Covers |
|---|---|
| Unit | Services, DTOs, calculation logic |
| Feature | HTTP endpoints, Inertia responses, auth |
| Integration | Queue jobs, event listeners, observers |

Key test cases:
- Subscription creation → delivery generation
- Wallet deduction on delivery completion
- Tenant isolation (cannot read other tenant's data)
- Rider assignment bulk action
- Skip logic (delivery not created for skipped dates)
- Missed delivery detection

---

## API Architecture

The REST API (`/api/v1/...`) is designed for:
- Mobile apps (future native app)
- Third-party integrations
- Partner webhooks

Authentication: Laravel Sanctum (Bearer token)

Example endpoints:

```
POST   /api/v1/auth/login
GET    /api/v1/customer/subscriptions
POST   /api/v1/customer/subscriptions
PATCH  /api/v1/customer/subscriptions/{id}/pause
GET    /api/v1/rider/deliveries/today
PATCH  /api/v1/rider/deliveries/{id}/complete
GET    /api/v1/admin/analytics/overview
```

All responses use Laravel API Resources for consistent JSON structure.

---

## Performance Optimizations

- **Database indexes** on all FK columns, `tenant_id`, `status`, `delivery_date` (migration: `add_performance_indexes`)
- **Query optimisation**: Eager loading via `with()` throughout — no N+1 queries
- **Pagination**: All list endpoints paginated — no full table scans
- **Caching**: Dashboard KPIs cached in Redis (TTL 5 minutes), invalidated on relevant events
- **SSR**: Inertia.js SSR via Node.js — first paint is fully rendered HTML
- **Asset optimisation**: Vite build with tree-shaking, code splitting, and gzip
- **Opcache**: PHP OPcache enabled in production Dockerfile
- **Lazy loading routes**: Vite code-splits per page automatically
- **Queue workers**: All heavy operations (PDF, email, analytics) are async

---

## Scaling & Future Roadmap

### Current architecture scales to

- ~50 tenants
- ~10,000 active customers
- ~50,000 deliveries/day
- Single server with Redis + MySQL

### Horizontal scaling path

```
Current: Monolith (Laravel) → DB (MySQL) → Queue (Redis/Horizon)

Phase 1: Read replicas for MySQL (analytics queries)
Phase 2: Dedicated queue worker instances
Phase 3: S3 for all media (already architected)
Phase 4: CDN for static assets

Microservice candidates (when needed):
- Notification Service (high volume SMS/push)
- Analytics Service (heavy aggregation)
- Delivery Generation Service (time-critical batch)
- Rider Tracking Service (real-time GPS stream)
```

### Roadmap features

- [ ] Native mobile apps (iOS + Android) via Capacitor
- [ ] Payment gateway integration (JazzCash, EasyPaisa, Stripe)
- [ ] AI-powered demand forecasting
- [ ] Driver route optimisation (Google Maps API)
- [ ] Customer-facing live rider tracking map
- [ ] WhatsApp / SMS delivery notifications
- [ ] Franchise/sub-tenant model
- [ ] Multi-currency support
- [ ] Inventory management module
- [ ] Automated refund processing

---

## End-to-End Testing Flow (JSON Graph)

A complete walkthrough of **every actor, every route, and every automated side-effect** — starting from a completely empty database.  
Copy the JSON into any graph visualiser (e.g. [jsoncrack.com](https://jsoncrack.com), Postman, or your own test harness) to explore the full flow.

> **Legend**
> - `id` — unique step identifier (prefix = actor: S=system, SA=super_admin, A=admin, R=rider, C=customer, SY=system-auto)
> - `prerequisites` — step IDs that **must** be completed first
> - `next` — step IDs that follow this one
> - `edges` — explicit directed graph connections (from → to)

```json
{
  "meta": {
    "name": "Veltrixo End-to-End Testing Flow",
    "version": "1.0.0",
    "description": "Complete test graph starting from an empty database, covering every actor and every feature. Follow edges in order within each phase.",
    "base_url": "http://veltrixo.test",
    "roles": ["super_admin", "admin", "rider", "customer", "system"],
    "phases": {
      "0_setup":                "Empty DB → migrate → seed roles → seed super admin → seed plans",
      "1_super_admin":          "Super admin logs in, reviews plans, provisions first tenant",
      "2_admin_setup":          "Tenant admin logs in, creates products, creates rider",
      "3_customer_onboard":     "Customer registers, tops up wallet, creates first subscription",
      "4_wallet_approval":      "Admin verifies and approves customer wallet recharge",
      "5_delivery_ops":         "Admin generates and bulk-assigns deliveries for the day",
      "6_rider_ops":            "Rider starts and completes assigned deliveries",
      "7_post_delivery":        "System: wallet deducted, invoice created, receipt notification sent",
      "8_customer_self_service":"Customer pauses, skips, views history and invoices",
      "9_admin_analytics":      "Admin views KPIs and analytics dashboard",
      "10_super_admin_monitor": "Super admin monitors platform, suspends and reactivates tenant"
    }
  },
  "nodes": [
    {
      "id": "S01",
      "actor": "system",
      "phase": "0_setup",
      "step": 1,
      "action": "Run all database migrations",
      "command": "php artisan migrate",
      "url": null,
      "method": null,
      "prerequisites": [],
      "payload": null,
      "expected_result": "All 36+ tables created in MySQL. No errors.",
      "next": ["S02"]
    },
    {
      "id": "S02",
      "actor": "system",
      "phase": "0_setup",
      "step": 2,
      "action": "Seed roles and permissions (super_admin, admin, rider, customer)",
      "command": "php artisan db:seed --class=RolesAndPermissionsSeeder",
      "url": null,
      "method": null,
      "prerequisites": ["S01"],
      "payload": null,
      "expected_result": "Roles created: super_admin, admin, manager, rider, customer, support. Permissions assigned.",
      "next": ["S03"]
    },
    {
      "id": "S03",
      "actor": "system",
      "phase": "0_setup",
      "step": 3,
      "action": "Seed super admin user",
      "command": "php artisan db:seed --class=SuperAdminSeeder",
      "url": null,
      "method": null,
      "prerequisites": ["S02"],
      "payload": {
        "name": "Super Admin",
        "email": "superadmin@veltrixo.test",
        "password": "password",
        "role": "super_admin"
      },
      "expected_result": "User created and assigned super_admin role. No tenant_id set.",
      "next": ["S04"]
    },
    {
      "id": "S04",
      "actor": "system",
      "phase": "0_setup",
      "step": 4,
      "action": "Seed tenant plans (Starter, Business, Enterprise)",
      "command": "php artisan db:seed --class=TenantPlansSeeder",
      "url": null,
      "method": null,
      "prerequisites": ["S03"],
      "payload": null,
      "expected_result": "3 rows in tenant_plans: starter (PKR 0/mo), business (PKR 49.99/mo), enterprise (PKR 299/mo).",
      "next": ["SA01"]
    },
    {
      "id": "SA01",
      "actor": "super_admin",
      "phase": "1_super_admin",
      "step": 1,
      "action": "Log in as Super Admin",
      "url": "/login",
      "method": "POST",
      "prerequisites": ["S04"],
      "payload": {
        "email": "superadmin@veltrixo.test",
        "password": "password"
      },
      "expected_result": "Redirected to /super-admin/dashboard. Role check: super_admin.",
      "next": ["SA02"]
    },
    {
      "id": "SA02",
      "actor": "super_admin",
      "phase": "1_super_admin",
      "step": 2,
      "action": "View Super Admin Dashboard — see platform KPIs",
      "url": "/super-admin/dashboard",
      "method": "GET",
      "route_name": "super-admin.dashboard",
      "prerequisites": ["SA01"],
      "payload": null,
      "expected_result": "Dashboard renders: total_tenants=0, active_tenants=0, trial_tenants=0.",
      "next": ["SA03"]
    },
    {
      "id": "SA03",
      "actor": "super_admin",
      "phase": "1_super_admin",
      "step": 3,
      "action": "Navigate to Plans — verify seeded plans are listed",
      "url": "/super-admin/plans",
      "method": "GET",
      "route_name": "super-admin.plans.index",
      "prerequisites": ["SA02"],
      "payload": null,
      "expected_result": "Table shows 3 plans: Starter, Business, Enterprise with correct PKR prices.",
      "next": ["SA04"]
    },
    {
      "id": "SA04",
      "actor": "super_admin",
      "phase": "1_super_admin",
      "step": 4,
      "action": "Create a new custom plan via the Add Plan modal",
      "url": "/super-admin/plans",
      "method": "POST",
      "route_name": "super-admin.plans.store",
      "prerequisites": ["SA03"],
      "payload": {
        "name": "Pro",
        "slug": "pro",
        "description": "For medium businesses",
        "price_monthly": 99.00,
        "price_yearly": 990.00,
        "max_customers": 2000,
        "max_riders": 20,
        "max_products": 500,
        "max_orders_per_month": 20000,
        "features": ["analytics", "api-access", "priority-support"],
        "is_active": true,
        "is_public": true,
        "sort_order": 15
      },
      "expected_result": "New row in tenant_plans. Plans list now shows 4 plans.",
      "next": ["SA05"]
    },
    {
      "id": "SA05",
      "actor": "super_admin",
      "phase": "1_super_admin",
      "step": 5,
      "action": "Navigate to Tenants — currently empty",
      "url": "/super-admin/tenants",
      "method": "GET",
      "route_name": "super-admin.tenants.index",
      "prerequisites": ["SA04"],
      "payload": null,
      "expected_result": "Tenants table is empty. 'No tenants yet' shown.",
      "next": ["SA06"]
    },
    {
      "id": "SA06",
      "actor": "super_admin",
      "phase": "1_super_admin",
      "step": 6,
      "action": "Provision first tenant — fill Create Tenant form",
      "url": "/super-admin/tenants",
      "method": "POST",
      "route_name": "super-admin.tenants.store",
      "prerequisites": ["SA05"],
      "payload": {
        "business_name": "Al-Noor Dairy",
        "owner_name": "Ahmed Ali",
        "owner_email": "admin@alnoor-dairy.test",
        "owner_phone": "03001234567",
        "plan": "starter",
        "trial_days": 30
      },
      "expected_result": "Tenant created. Admin user created (admin@alnoor-dairy.test). tenant_id linked. Subscription row created (status=trialing). Redirected to /super-admin/tenants/{id}.",
      "next": ["SA07"]
    },
    {
      "id": "SA07",
      "actor": "super_admin",
      "phase": "1_super_admin",
      "step": 7,
      "action": "View tenant detail — confirm owner, plan, trial expiry",
      "url": "/super-admin/tenants/{tenant_id}",
      "method": "GET",
      "route_name": "super-admin.tenants.show",
      "prerequisites": ["SA06"],
      "payload": null,
      "expected_result": "Shows: name=Al-Noor Dairy, owner=Ahmed Ali, plan=Starter, status=active, trial_ends=+30 days.",
      "next": ["A01"]
    },
    {
      "id": "A01",
      "actor": "admin",
      "phase": "2_admin_setup",
      "step": 1,
      "action": "Log in as tenant admin (user created during provisioning)",
      "url": "/login",
      "method": "POST",
      "prerequisites": ["SA06"],
      "payload": {
        "email": "admin@alnoor-dairy.test",
        "password": "password"
      },
      "note": "If password unknown, reset via: php artisan tinker → User::where('email','admin@alnoor-dairy.test')->first()->update(['password'=>bcrypt('password')])",
      "expected_result": "Redirected to /admin/dashboard. Role=admin. All queries scoped to Al-Noor Dairy tenant_id.",
      "next": ["A02"]
    },
    {
      "id": "A02",
      "actor": "admin",
      "phase": "2_admin_setup",
      "step": 2,
      "action": "View Admin Dashboard — empty state",
      "url": "/admin/dashboard",
      "method": "GET",
      "route_name": "admin.dashboard",
      "prerequisites": ["A01"],
      "payload": null,
      "expected_result": "0 deliveries, 0 active subscriptions. No riders assigned yet.",
      "next": ["A03"]
    },
    {
      "id": "A03",
      "actor": "admin",
      "phase": "2_admin_setup",
      "step": 3,
      "action": "Create first product: Full Cream Milk 1L",
      "url": "/admin/products",
      "method": "POST",
      "route_name": "admin.products.store",
      "prerequisites": ["A02"],
      "payload": {
        "name": "Full Cream Milk 1L",
        "description": "Fresh farm milk",
        "price": 180,
        "unit": "litre",
        "sku": "MILK-1L",
        "is_active": true
      },
      "expected_result": "products row created with tenant_id. SKU MILK-1L visible in product list.",
      "next": ["A04"]
    },
    {
      "id": "A04",
      "actor": "admin",
      "phase": "2_admin_setup",
      "step": 4,
      "action": "Create second product: Yoghurt 500g",
      "url": "/admin/products",
      "method": "POST",
      "route_name": "admin.products.store",
      "prerequisites": ["A03"],
      "payload": {
        "name": "Yoghurt 500g",
        "description": "Creamy plain yoghurt",
        "price": 120,
        "unit": "kg",
        "sku": "YOG-500G",
        "is_active": true
      },
      "expected_result": "Second product created. Products list shows 2 items.",
      "next": ["A05"]
    },
    {
      "id": "A05",
      "actor": "admin",
      "phase": "2_admin_setup",
      "step": 5,
      "action": "Create rider account",
      "url": "/admin/riders",
      "method": "POST",
      "route_name": "admin.riders.store",
      "prerequisites": ["A04"],
      "payload": {
        "name": "Usman Rider",
        "email": "rider@alnoor-dairy.test",
        "phone": "03111234567",
        "vehicle_type": "motorcycle",
        "vehicle_number": "LEA-1234"
      },
      "expected_result": "Rider record and User created with role=rider, tenant_id scoped. Rider visible in riders list.",
      "next": ["C01"]
    },
    {
      "id": "C01",
      "actor": "customer",
      "phase": "3_customer_onboard",
      "step": 1,
      "action": "Register as customer (self-service or admin-created)",
      "url": "/register",
      "method": "POST",
      "prerequisites": ["SA06"],
      "payload": {
        "name": "Sara Customer",
        "email": "sara@example.test",
        "phone": "03009876543",
        "password": "password",
        "password_confirmation": "password"
      },
      "expected_result": "User created with role=customer, tenant_id scoped. Wallet auto-created (balance=Rs 0.00). Redirected to /customer/dashboard.",
      "next": ["C02"]
    },
    {
      "id": "C02",
      "actor": "customer",
      "phase": "3_customer_onboard",
      "step": 2,
      "action": "View customer dashboard — wallet balance Rs 0",
      "url": "/customer/dashboard",
      "method": "GET",
      "route_name": "customer.dashboard",
      "prerequisites": ["C01"],
      "payload": null,
      "expected_result": "Dashboard: wallet=Rs 0.00, 0 active subscriptions. Prompt to top up wallet visible.",
      "next": ["C03"]
    },
    {
      "id": "C03",
      "actor": "customer",
      "phase": "3_customer_onboard",
      "step": 3,
      "action": "Submit wallet recharge request (Rs 2000 via JazzCash)",
      "url": "/customer/wallet/recharge",
      "method": "POST",
      "route_name": "customer.wallet.recharge",
      "prerequisites": ["C02"],
      "payload": {
        "amount": 2000,
        "payment_method": "jazzcash",
        "reference": "JZ-20260526-88374",
        "notes": "Recharge via JazzCash mobile app"
      },
      "expected_result": "wallet_recharge_requests row created (status=pending). Admin notified via WebSocket. Customer sees 'Pending approval'.",
      "next": ["C04", "A06"]
    },
    {
      "id": "C04",
      "actor": "customer",
      "phase": "3_customer_onboard",
      "step": 4,
      "action": "View wallet page — pending recharge shown",
      "url": "/customer/wallet",
      "method": "GET",
      "route_name": "customer.wallet",
      "prerequisites": ["C03"],
      "payload": null,
      "expected_result": "Balance: Rs 0.00. Recharge requests list: 1 pending (Rs 2000, JazzCash, ref JZ-20260526-88374).",
      "next": ["A06"]
    },
    {
      "id": "A06",
      "actor": "admin",
      "phase": "4_wallet_approval",
      "step": 1,
      "action": "View wallet recharge requests — verify JazzCash reference",
      "url": "/admin/wallets",
      "method": "GET",
      "route_name": "admin.wallets.index",
      "prerequisites": ["C03"],
      "payload": null,
      "expected_result": "Pending recharges list shows Sara's Rs 2000 JazzCash request with reference JZ-20260526-88374.",
      "next": ["A07"]
    },
    {
      "id": "A07",
      "actor": "admin",
      "phase": "4_wallet_approval",
      "step": 2,
      "action": "Approve wallet recharge",
      "url": "/admin/wallets/recharges/{recharge_id}/approve",
      "method": "PATCH",
      "route_name": "admin.wallets.recharges.approve",
      "prerequisites": ["A06"],
      "payload": null,
      "expected_result": "recharge.status → approved. wallet.balance: Rs 0 → Rs 2000. WalletTransaction credit created. Sara notified.",
      "next": ["C05"]
    },
    {
      "id": "C05",
      "actor": "customer",
      "phase": "3_customer_onboard",
      "step": 5,
      "action": "Wallet approved — create first subscription",
      "url": "/customer/subscriptions",
      "method": "POST",
      "route_name": "customer.subscriptions.store",
      "prerequisites": ["A07", "A04"],
      "payload": {
        "frequency": "daily",
        "items": [
          { "product_id": 1, "quantity": 2 },
          { "product_id": 2, "quantity": 1 }
        ],
        "address": {
          "line1": "House 12, Street 5",
          "city": "Lahore",
          "area": "DHA Phase 4"
        },
        "starts_at": "2026-05-27"
      },
      "expected_result": "Subscription created (status=active, frequency=daily). subscription_items: 2×Milk + 1×Yoghurt. Daily cost: Rs 480 (2×180 + 120).",
      "next": ["C06"]
    },
    {
      "id": "C06",
      "actor": "customer",
      "phase": "3_customer_onboard",
      "step": 6,
      "action": "View subscriptions list",
      "url": "/customer/subscriptions",
      "method": "GET",
      "route_name": "customer.subscriptions.index",
      "prerequisites": ["C05"],
      "payload": null,
      "expected_result": "1 active subscription: daily, Rs 480/delivery, next delivery: 2026-05-27.",
      "next": ["A08"]
    },
    {
      "id": "A08",
      "actor": "admin",
      "phase": "5_delivery_ops",
      "step": 1,
      "action": "Generate deliveries for tomorrow via admin panel",
      "url": "/admin/deliveries/generate",
      "method": "POST",
      "route_name": "admin.deliveries.generate",
      "prerequisites": ["C05"],
      "payload": {
        "date": "2026-05-27"
      },
      "expected_result": "1 delivery created: Sara's subscription, 2026-05-27. delivery_items: 2×MILK-1L + 1×YOG-500G. status=pending.",
      "next": ["A09"]
    },
    {
      "id": "A09",
      "actor": "admin",
      "phase": "5_delivery_ops",
      "step": 2,
      "action": "View deliveries list — filter pending",
      "url": "/admin/deliveries",
      "method": "GET",
      "route_name": "admin.deliveries.index",
      "prerequisites": ["A08"],
      "payload": null,
      "expected_result": "1 delivery: Sara Customer, DHA Phase 4, status=pending.",
      "next": ["A10"]
    },
    {
      "id": "A10",
      "actor": "admin",
      "phase": "5_delivery_ops",
      "step": 3,
      "action": "Bulk-assign delivery to Usman Rider",
      "url": "/admin/deliveries/bulk-assign",
      "method": "POST",
      "route_name": "admin.deliveries.bulk-assign",
      "prerequisites": ["A09", "A05"],
      "payload": {
        "delivery_ids": [1],
        "rider_id": 1
      },
      "expected_result": "delivery.status → assigned, rider_id set. Rider gets WebSocket push 'New deliveries assigned'. Admin sees 0 pending, 1 assigned.",
      "next": ["R01"]
    },
    {
      "id": "R01",
      "actor": "rider",
      "phase": "6_rider_ops",
      "step": 1,
      "action": "Log in as rider",
      "url": "/login",
      "method": "POST",
      "prerequisites": ["A05"],
      "payload": {
        "email": "rider@alnoor-dairy.test",
        "password": "password"
      },
      "expected_result": "Redirected to /rider/dashboard. Role=rider. Tenant scoped.",
      "next": ["R02"]
    },
    {
      "id": "R02",
      "actor": "rider",
      "phase": "6_rider_ops",
      "step": 2,
      "action": "View rider dashboard — 1 assigned delivery today",
      "url": "/rider/dashboard",
      "method": "GET",
      "route_name": "rider.dashboard",
      "prerequisites": ["R01", "A10"],
      "payload": null,
      "expected_result": "assigned=1, completed=0, remaining=1.",
      "next": ["R03"]
    },
    {
      "id": "R03",
      "actor": "rider",
      "phase": "6_rider_ops",
      "step": 3,
      "action": "View deliveries list — Sara's delivery card",
      "url": "/rider/deliveries",
      "method": "GET",
      "route_name": "rider.deliveries.index",
      "prerequisites": ["R02"],
      "payload": null,
      "expected_result": "1 delivery card: Sara Customer, DHA Phase 4, 2×Milk + 1×Yoghurt. Status=assigned. 'Start Delivery' button visible.",
      "next": ["R04"]
    },
    {
      "id": "R04",
      "actor": "rider",
      "phase": "6_rider_ops",
      "step": 4,
      "action": "Start delivery — tap Start",
      "url": "/rider/deliveries/{delivery_id}/start",
      "method": "POST",
      "route_name": "rider.deliveries.start",
      "prerequisites": ["R03"],
      "payload": {
        "latitude": 31.5204,
        "longitude": 74.3587
      },
      "expected_result": "delivery.status → in_progress. GPS + timestamp recorded. Admin dashboard updates in real-time via DeliveryStatusUpdated event.",
      "next": ["R05"]
    },
    {
      "id": "R05",
      "actor": "rider",
      "phase": "6_rider_ops",
      "step": 5,
      "action": "Complete delivery — tap Complete after handing items to customer",
      "url": "/rider/deliveries/{delivery_id}/complete",
      "method": "POST",
      "route_name": "rider.deliveries.complete",
      "prerequisites": ["R04"],
      "payload": {
        "latitude": 31.5201,
        "longitude": 74.3590,
        "notes": "Delivered to guard at gate"
      },
      "expected_result": "delivery.status → delivered. DeliveryObserver fires: wallet deducted, invoice queued, receipt notification queued.",
      "next": ["SY01"]
    },
    {
      "id": "SY01",
      "actor": "system",
      "phase": "7_post_delivery",
      "step": 1,
      "action": "WalletService deducts Rs 480 from Sara's wallet (DeliveryObserver)",
      "command": "Automatic — DeliveryObserver on delivery.status=delivered",
      "url": null,
      "method": null,
      "prerequisites": ["R05"],
      "payload": null,
      "expected_result": "wallets.balance: Rs 2000 → Rs 1520. wallet_transactions: debit Rs 480, reference=delivery_{id}.",
      "next": ["SY02"]
    },
    {
      "id": "SY02",
      "actor": "system",
      "phase": "7_post_delivery",
      "step": 2,
      "action": "GenerateInvoiceJob creates PDF invoice (invoices queue)",
      "command": "Dispatched by WalletDebited listener",
      "url": null,
      "method": null,
      "prerequisites": ["SY01"],
      "payload": null,
      "expected_result": "Invoice + invoice_items rows created. PDF stored in storage/S3.",
      "next": ["SY03"]
    },
    {
      "id": "SY03",
      "actor": "system",
      "phase": "7_post_delivery",
      "step": 3,
      "action": "SendDeliveryReceiptNotification dispatched to Sara (notifications queue)",
      "command": "Dispatched by GenerateInvoiceJob on completion",
      "url": null,
      "method": null,
      "prerequisites": ["SY02"],
      "payload": null,
      "expected_result": "Sara's notification bell increments. In-app: 'Delivered! Rs 480 deducted. Balance: Rs 1520.' Mail receipt sent.",
      "next": ["C07"]
    },
    {
      "id": "C07",
      "actor": "customer",
      "phase": "8_customer_self_service",
      "step": 1,
      "action": "View delivery history — completed delivery with invoice link",
      "url": "/customer/deliveries",
      "method": "GET",
      "route_name": "customer.deliveries.index",
      "prerequisites": ["SY03"],
      "payload": null,
      "expected_result": "1 delivery: status=delivered, 2026-05-27, Rs 480. Invoice PDF download link present.",
      "next": ["C08"]
    },
    {
      "id": "C08",
      "actor": "customer",
      "phase": "8_customer_self_service",
      "step": 2,
      "action": "View delivery detail and download PDF invoice",
      "url": "/customer/deliveries/{delivery_id}",
      "method": "GET",
      "route_name": "customer.deliveries.show",
      "prerequisites": ["C07"],
      "payload": null,
      "expected_result": "Detail page: items, GPS timestamps, amount=Rs 480. PDF download functional.",
      "next": ["C09"]
    },
    {
      "id": "C09",
      "actor": "customer",
      "phase": "8_customer_self_service",
      "step": 3,
      "action": "Skip next delivery",
      "url": "/customer/subscriptions/{subscription_id}/skip",
      "method": "POST",
      "route_name": "customer.subscriptions.skip",
      "prerequisites": ["C06"],
      "payload": {
        "skip_date": "2026-05-28"
      },
      "expected_result": "subscription_skips row created for 2026-05-28. GenerateDeliveriesJob will skip this date.",
      "next": ["C10"]
    },
    {
      "id": "C10",
      "actor": "customer",
      "phase": "8_customer_self_service",
      "step": 4,
      "action": "Pause subscription for vacation",
      "url": "/customer/subscriptions/{subscription_id}/pause",
      "method": "POST",
      "route_name": "customer.subscriptions.pause",
      "prerequisites": ["C09"],
      "payload": {
        "pause_from": "2026-05-29",
        "pause_until": "2026-06-03"
      },
      "expected_result": "subscription.status → paused. No deliveries generated between those dates.",
      "next": ["C11"]
    },
    {
      "id": "C11",
      "actor": "customer",
      "phase": "8_customer_self_service",
      "step": 5,
      "action": "Resume subscription early",
      "url": "/customer/subscriptions/{subscription_id}/resume",
      "method": "POST",
      "route_name": "customer.subscriptions.resume",
      "prerequisites": ["C10"],
      "payload": null,
      "expected_result": "subscription.status → active. Deliveries resume from next valid date.",
      "next": ["A11"]
    },
    {
      "id": "A11",
      "actor": "admin",
      "phase": "9_admin_analytics",
      "step": 1,
      "action": "View Admin Analytics dashboard",
      "url": "/admin/analytics",
      "method": "GET",
      "route_name": "admin.analytics",
      "prerequisites": ["R05"],
      "payload": null,
      "expected_result": "KPIs: deliveries_today=1, completed=1, revenue_today=Rs 480, active_subscriptions=1, wallet_total=Rs 1520.",
      "next": ["SA08"]
    },
    {
      "id": "SA08",
      "actor": "super_admin",
      "phase": "10_super_admin_monitor",
      "step": 1,
      "action": "Super Admin revisits Tenants list — Al-Noor Dairy active",
      "url": "/super-admin/tenants",
      "method": "GET",
      "route_name": "super-admin.tenants.index",
      "prerequisites": ["A11"],
      "payload": null,
      "expected_result": "1 tenant listed: Al-Noor Dairy, status=active, plan=Starter, owner=Ahmed Ali.",
      "next": ["SA09"]
    },
    {
      "id": "SA09",
      "actor": "super_admin",
      "phase": "10_super_admin_monitor",
      "step": 2,
      "action": "Suspend tenant (testing suspension flow)",
      "url": "/super-admin/tenants/{tenant_id}/suspend",
      "method": "PATCH",
      "route_name": "super-admin.tenants.suspend",
      "prerequisites": ["SA08"],
      "payload": {
        "reason": "Testing suspension flow"
      },
      "expected_result": "tenant.status → suspended. Admin/rider/customer logins for this tenant blocked.",
      "next": ["SA10"]
    },
    {
      "id": "SA10",
      "actor": "super_admin",
      "phase": "10_super_admin_monitor",
      "step": 3,
      "action": "Reactivate tenant",
      "url": "/super-admin/tenants/{tenant_id}/reactivate",
      "method": "PATCH",
      "route_name": "super-admin.tenants.reactivate",
      "prerequisites": ["SA09"],
      "payload": null,
      "expected_result": "tenant.status → active. All users for this tenant can log in again normally.",
      "next": []
    }
  ],
  "edges": [
    { "from": "S01",  "to": "S02",  "label": "migrate done" },
    { "from": "S02",  "to": "S03",  "label": "roles seeded" },
    { "from": "S03",  "to": "S04",  "label": "super admin seeded" },
    { "from": "S04",  "to": "SA01", "label": "plans seeded" },
    { "from": "SA01", "to": "SA02", "label": "logged in" },
    { "from": "SA02", "to": "SA03", "label": "navigate plans" },
    { "from": "SA03", "to": "SA04", "label": "create plan" },
    { "from": "SA04", "to": "SA05", "label": "navigate tenants" },
    { "from": "SA05", "to": "SA06", "label": "provision tenant" },
    { "from": "SA06", "to": "SA07", "label": "view tenant" },
    { "from": "SA06", "to": "A01",  "label": "tenant admin exists" },
    { "from": "SA06", "to": "C01",  "label": "tenant exists" },
    { "from": "A01",  "to": "A02",  "label": "logged in" },
    { "from": "A02",  "to": "A03",  "label": "create product 1" },
    { "from": "A03",  "to": "A04",  "label": "create product 2" },
    { "from": "A04",  "to": "A05",  "label": "create rider" },
    { "from": "A05",  "to": "C01",  "label": "setup complete" },
    { "from": "C01",  "to": "C02",  "label": "registered" },
    { "from": "C02",  "to": "C03",  "label": "recharge request" },
    { "from": "C03",  "to": "C04",  "label": "view wallet" },
    { "from": "C03",  "to": "A06",  "label": "admin notified" },
    { "from": "A06",  "to": "A07",  "label": "approve recharge" },
    { "from": "A07",  "to": "C05",  "label": "wallet funded" },
    { "from": "A04",  "to": "C05",  "label": "products ready" },
    { "from": "C05",  "to": "C06",  "label": "subscription created" },
    { "from": "C05",  "to": "A08",  "label": "subscription exists" },
    { "from": "A08",  "to": "A09",  "label": "deliveries generated" },
    { "from": "A09",  "to": "A10",  "label": "bulk assign" },
    { "from": "A05",  "to": "A10",  "label": "rider ready" },
    { "from": "A10",  "to": "R01",  "label": "rider assigned" },
    { "from": "R01",  "to": "R02",  "label": "logged in" },
    { "from": "R02",  "to": "R03",  "label": "view deliveries" },
    { "from": "R03",  "to": "R04",  "label": "start delivery" },
    { "from": "R04",  "to": "R05",  "label": "complete delivery" },
    { "from": "R05",  "to": "SY01", "label": "observer fires" },
    { "from": "SY01", "to": "SY02", "label": "wallet deducted" },
    { "from": "SY02", "to": "SY03", "label": "invoice created" },
    { "from": "SY03", "to": "C07",  "label": "receipt sent" },
    { "from": "C07",  "to": "C08",  "label": "view invoice" },
    { "from": "C06",  "to": "C09",  "label": "skip delivery" },
    { "from": "C09",  "to": "C10",  "label": "pause subscription" },
    { "from": "C10",  "to": "C11",  "label": "resume subscription" },
    { "from": "R05",  "to": "A11",  "label": "delivery done" },
    { "from": "A11",  "to": "SA08", "label": "super admin monitors" },
    { "from": "SA08", "to": "SA09", "label": "suspend tenant" },
    { "from": "SA09", "to": "SA10", "label": "reactivate tenant" }
  ]
}
```

---

## Contributing

We welcome contributions. Please read the guidelines before submitting a PR.

### Development workflow

```bash
# 1. Fork the repository

# 2. Start your feature branch
git checkout -b feature/your-feature-name

# 3. Make changes and write tests

# 4. Ensure tests pass (inside workspace container)
php artisan test

# 5. Build frontend (on host)
npm run build

# 6. Submit a Pull Request to main
```

### Code style

- PHP: PSR-12 (enforced via Pint)
- TypeScript: strict mode enabled
- Vue: Composition API + `<script setup>` only
- Commits: Conventional Commits format (`feat:`, `fix:`, `docs:`, etc.)

```bash
# Auto-fix PHP code style
./vendor/bin/pint

# TypeScript type check
npx vue-tsc --noEmit
```

---

## License

This project is licensed under the **MIT License** — see the [LICENSE](LICENSE) file for details.

---

<div align="center">

Built with ❤️ using Laravel 13, Vue 3, and Inertia.js

**Veltrixo** — Powering recurring delivery businesses across Pakistan

[⭐ Star this repo](https://github.com/mateen212/Veltrixo) · [🐛 Report a bug](https://github.com/mateen212/Veltrixo/issues) · [💡 Request a feature](https://github.com/mateen212/Veltrixo/issues)

</div>
