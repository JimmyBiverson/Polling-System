<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## Kenya National Polling System

A Laravel application for collecting, reviewing, verifying, and tallying polling-station election results.

## What It Does

- Authenticates polling agents, county administrators, and super administrators.
- Lets agents submit station results using admin-managed stations and presiding officers.
- Validates candidate votes plus spoilt votes against total votes cast.
- Tracks pending, verified, rejected, and disputed submissions.
- Provides category-aware review for Presidential, Governor, MP, Women Rep, MCA, and future election types.
- Shows candidate tallies from verified results and supports CSV export.
- Records audit events and submission hashes for integrity monitoring.

## Roles

- **Polling Agent**: submits station results and tracks their submissions.
- **County Admin**: reviews and verifies results from agents and county operations.
- **Super Admin**: manages users and electoral data, monitors audit logs, overrides statuses, and runs system controls.

## Local Setup

Requirements: PHP 8.3+, Composer, Node.js, and a configured database.

```bash
composer install
copy .env.example .env
php artisan key:generate
php artisan migrate --seed
npm install
npm run build
php artisan serve
```

Open `http://127.0.0.1:8000` after starting the server. Configure database credentials in `.env`; never commit `.env` or local credential files.

## Verification

```bash
php artisan test --compact
```

The feature suite covers station and officer selection, submission validation, category filtering, pending review, and role permissions.

