# BerfaERP

A modern, highly aesthetic, premium Enterprise Resource Planning (ERP) and apparel management system designed specifically for garment factories and custom tailors (Konveksi), built using Laravel 12, Filament v3, and Tailwind CSS.

---

# Overview

- **About the Project**: BerfaERP manages the complete garment lifecycle—from high-converting premium landing pages, real-time public stock checking, customizable ordering systems, pre-orders (quotations), production line tracking, to dual-entry Chart of Accounts (COA) bookkeeping, and robust role-permission matrices.
- **Main Objectives**: Provide garment business owners with a premium, sleek administrative cockpit and modern customer-facing platform that eliminates logistical errors, simplifies accounting, and speeds up wholesale production pipelines.
- **Target Users**: Custom apparel manufacturers, garment factories, large tailoring businesses, admin staff, production managers, and accounting departments.

---

# Features

- **Landing Page Premium**: Sleek modern storefront with dynamic value grids, reviews, and CTA checkouts.
- **Catalog & Real-time Stock**: Interactive customer catalog and public real-time inventory checking for variant codes, colors, and sizes.
- **Order & Pre-Order (Quotation) Management**: Seamless transaction flows with overall custom discounts, deposit payments, and automatic WhatsApp invoicing.
- **Role-Permission Matrix**: Granular authorization for administrators, staff, accountants, and custom roles.
- **Garment Production Tracking**: Track cut-make-trim (CMT) garment manufacturing progress, material utilization, and stock in/out details.
- **Accounting & General Ledger**: Complete financial tracking with Chart of Accounts (COA), Journal entries, cash books, Profit & Loss reports, and Balance Sheets.

---

# System Architecture

## Tech Stack
- **Framework**: Laravel 12
- **Admin & Widgets**: Filament v3 (Livewire 3)
- **Frontend CSS**: Tailwind CSS
- **Interactions**: Alpine.js
- **Database**: SQLite / MySQL

## Architecture Overview
BerfaERP uses a robust modular architecture utilizing Laravel's MVC pattern unified with Filament's declarative resources. The system bridges the public customer checkout flow directly to the admin sales pipeline. A dedicated role-permission checker controls panel navigation, while the accounting module hooks directly into transaction state mutations.

---

# How to Install

### 1. Clone Project
```bash
git clone https://github.com/riveroo/berfacms-konveksi.git
cd berfacms-konveksi
```

### 2. Install Dependencies
```bash
composer install
npm install
```

### 3. Setup Environment
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Database Setup
```bash
php artisan migrate
php artisan db:seed
```

### 5. Build Assets & Start Serve
```bash
npm run build
php artisan serve
```

---

# Extra Functions

These special diagnostic and maintenance routes are available for managing the platform without direct terminal/SSH access (ideal for shared hosting environments):

* **`/run-seeder`**: Executes the master `DatabaseSeeder` which seeds all default test data, transactions, and items.
* **`/run-auth-seeder`**: Executes essential auth seeders only (`RbacSeeder` and `AccountSeeder`) to setup/restore default Roles, Permissions, Super Admin user, and standard Chart of Accounts (COA) without touching existing business data.
* **`/run-migration`** or **`/migrate-database`**: Runs `php artisan migrate --force` to update database schemas instantly.
* **`/clear-config`**: Clears system configuration, routes, views, and application caches safely.
* **`/clear-view`**: Clears Blade view compiler cache specifically (`php artisan view:clear`) to compile fresh templates.
* **`/admin/fix-storage`**: Creates a physical public storage link directory if `symlink` or terminal functions are restricted by the hosting provider.
