<p align="center"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></p>

# Delivery SaaS Platform

A production-grade multi-tenant recurring delivery SaaS platform (dairy / grocery / water subscription logistics).

## Features

- **Multi-tenancy** — Single-database architecture with `tenant_id` isolation
- **Subscription Management** — Daily/weekly/monthly recurring deliveries with pause, resume, cancel, skip
- **Delivery Operations** — OTP verification, QR tokens, proof-of-delivery, live rider tracking
- **Wallet System** — Auto-deduction on delivery, recharge requests, low-balance alerts
- **Role-Based Access** — Admin, Rider, Customer panels via Spatie Permissions
- **Real-time** — Laravel Reverb WebSockets for live delivery & wallet events
- **Queue Processing** — Laravel Horizon with named queues
- **Analytics Dashboard** — Revenue, churn, rider performance, delivery success rate
- **Notifications** — Email + in-app for delivery, low balance, subscription confirmation
- **Media Handling** — Spatie MediaLibrary for product images, delivery proof, receipts
- **Activity Log** — Full audit trail via Spatie ActivityLog
- **Full-text Search** — Laravel Scout for products

## Tech Stack

| Layer | Technology |
|-------|-----------|
| Backend | Laravel 13 (PHP 8.4) |
| Frontend | Vue 3 + Inertia.js + TypeScript |
| Styling | Tailwind CSS |
| State | Pinia |
| Build | Vite 8 + SSR |
| DB | MySQL 8+ |
| Cache/Queue | Redis + Laravel Horizon |
| WebSocket | Laravel Reverb |
| Auth | Laravel Sanctum + Breeze |
| Permissions | Spatie Laravel Permission |
| DevOps | Docker + Nginx + Supervisor |

## Quick Start (Laradock)

```bash
# Inside workspace container
cd /var/www/delivery-saas
composer install && npm install
cp .env.example .env && php artisan key:generate
php artisan migrate --force
CACHE_STORE=array php artisan db:seed --force
npm run build
```

## Demo Credentials

| Role | Email | Password |
|------|-------|----------|
| Admin | owner@demo.com | password |
| Customer | customer@demo.com | password |
| Rider | rider@demo.com | password |

## Scheduled Tasks

| Schedule | Task |
|----------|------|
| Daily 01:00 | `deliveries:generate` — generate tomorrow's deliveries |
| Daily 00:30 | Auto-resume paused subscriptions |

## License

MIT
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

In addition, [Laracasts](https://laracasts.com) contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

You can also watch bite-sized lessons with real-world projects on [Laravel Learn](https://laravel.com/learn), where you will be guided through building a Laravel application from scratch while learning PHP fundamentals.

## Agentic Development

Laravel's predictable structure and conventions make it ideal for AI coding agents like Claude Code, Cursor, and GitHub Copilot. Install [Laravel Boost](https://laravel.com/docs/ai) to supercharge your AI workflow:

```bash
composer require laravel/boost --dev

php artisan boost:install
```

Boost provides your agent 15+ tools and skills that help agents build Laravel applications while following best practices.

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
