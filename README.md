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
- **Wallet top-up** — UPI, bank transfer, cash
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
1. Customer submits a recharge request (Rs 500 via UPI)
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
4. Enters amount (Rs 1,000), selects payment method (UPI/bank transfer/cash)
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
