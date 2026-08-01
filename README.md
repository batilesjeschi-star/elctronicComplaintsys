# Barangay Electronic Complaint System (ECS)

A Laravel 11 web application that lets residents report community problems
(potholes, broken streetlights, garbage, flooding, drainage, safety concerns,
etc.) and lets barangay staff track, assign, and resolve them.

Built with **Laravel 11**, **MySQL**, **Blade + Bootstrap 5**, and **Laravel Breeze**
(for authentication only — all Breeze views have been replaced with Bootstrap 5
versions, see "How authentication is wired" below).

> **Also works on Laravel 12.x / 13.x.** This code was written and verified
> against Laravel 11.6.1, but every API it depends on (`bootstrap/app.php`'s
> middleware builder, `protected $fillable`/`casts()` on models, Breeze's
> routes and controllers, `Paginator::useBootstrapFive()`) is confirmed
> unchanged through Laravel 13.8 — Laravel 13 added optional PHP attributes
> (`#[Fillable]`, `#[Hidden]`, etc.) as an *alternative* to the property-based
> style used here, but the old style still works. The only real difference is
> the minimum PHP version: Laravel 11 needs PHP 8.2+, Laravel 13 needs PHP 8.3+.
> If your assignment specifically requires Laravel 11, pin the version in
> Step 1 below (`"11.*"`); otherwise it's safe to install whatever the latest
> version is.

---

## 1. What's included

| Area | Details |
|---|---|
| Resident side | Register/login, dashboard, submit complaint (title, description, category, location, GPS, up to 5 photos), complaint history with search/filter, status timeline, printable receipt with reference number |
| Admin side | Secure admin login, stats dashboard with chart, all-complaints table with category/status/date filters + search, complaint detail with photos, status/remarks/assignment update form, resolution photo upload, department management, daily/weekly/monthly/custom reports + CSV export |
| Notifications | Resident is emailed automatically whenever an admin changes a complaint's status |
| Data | 5 migrations, 5 Eloquent models with relationships, an audit trail (`complaint_updates`) of every status change |
| Security | `auth` middleware for residents, custom `admin` middleware + role column for staff-only routes, Form Request validation on every form |

This project was assembled as a set of **application files** (models, controllers,
migrations, views, etc.) meant to be dropped into a freshly installed Laravel 11
project. It is **not** a pre-built `vendor/` folder — you still install Laravel
and its dependencies yourself with Composer (step 2 below), the same way you
would for any Laravel project. This is intentional and normal for Laravel apps.

---

## 2. Requirements

- PHP 8.2+ (Laravel 11) or PHP 8.3+ (Laravel 12/13) — matching whichever
  Laravel version you install in Step 1
- Composer 2.x
- MySQL 5.7+ / 8.x (or MariaDB)
- Node.js — **not required**. This project uses Bootstrap 5 via CDN instead of
  compiling assets with Vite, so there is nothing to `npm install` or `npm run build`.

---

## 3. Step-by-step installation

### Step 1 — Create a fresh Laravel project

```bash
composer create-project laravel/laravel ecs-app "11.*"
cd ecs-app
```

`"11.*"` pins the install to Laravel 11, matching what this code was verified
against. If you don't need that specific version, you can drop it and just run
`composer create-project laravel/laravel ecs-app` — the latest version works too
(see the compatibility note above).

### Step 2 — Install Laravel Breeze (Blade stack)

Breeze gives us working login/register/password-reset/email-verification
controllers and routes for free, so we don't have to write that plumbing by hand.

```bash
composer require laravel/breeze --dev
php artisan breeze:install blade
```

When prompted, the defaults are fine (no dark mode needed, choose either
Pest or PHPUnit — this project doesn't add its own tests).

### Step 3 — Copy in the files from this package

Copy every file from this package into your new `ecs-app` folder, **overwriting**
when prompted. Concretely:

```bash
# from inside the folder you unzipped this package into:
cp -r app/*        /path/to/ecs-app/app/
cp -r bootstrap/*  /path/to/ecs-app/bootstrap/
cp -r database/*   /path/to/ecs-app/database/
cp -r resources/*  /path/to/ecs-app/resources/
cp -r routes/*     /path/to/ecs-app/routes/
```

This will:
- **Add** new files (all the complaint/admin controllers, models, migrations, views).
- **Overwrite** a few Breeze-generated files on purpose:
  - `app/Providers/AppServiceProvider.php` (adds Bootstrap 5 pagination)
  - `app/Http/Requests/ProfileUpdateRequest.php` (adds phone/address validation)
  - `bootstrap/app.php` (registers the `admin` middleware alias)
  - `routes/web.php` (adds all resident/admin routes)
  - `resources/views/auth/*.blade.php`, `resources/views/layouts/*.blade.php`,
    `resources/views/profile/edit.blade.php`, `resources/views/dashboard.blade.php`
    (replaces Breeze's default Tailwind views with Bootstrap 5 versions)

Since we no longer use Breeze's Tailwind components, you can delete the now-unused
component files (this is optional cleanup, nothing breaks if you skip it):

```bash
rm -rf resources/views/components
rm -f resources/css/app.css
```

### Step 4 — Configure your `.env`

Open `.env` and set your database credentials:

```env
APP_NAME="Barangay ECS"

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ecs_db
DB_USERNAME=root
DB_PASSWORD=

# For local development, this writes emails to storage/logs/laravel.log
# instead of actually sending them, so you can test notifications for free.
MAIL_MAILER=log
```

Create the database itself (adjust for your MySQL client):

```bash
mysql -u root -p -e "CREATE DATABASE ecs_db;"
```

### Step 5 — Migrate and seed

```bash
php artisan migrate --seed
```

This creates every table and seeds:
- 1 admin account
- 5 departments
- 5 sample resident accounts + 15 sample complaints across every status/category

### Step 6 — Link storage (required for uploaded photos to display)

```bash
php artisan storage:link
```

This creates `public/storage` pointing at `storage/app/public`, which is where
complaint photos and resolution photos are saved.

### Step 7 — Run it

```bash
php artisan serve
```

Visit **http://127.0.0.1:8000**.

---

## 4. Default login credentials

| Role | Email | Password |
|---|---|---|
| Admin (barangay staff) | `admin@ecs.test` | `password123` |
| Sample resident | any of the 5 generated by the seeder — check `users` table, or just register your own | `password123` |

**Change the admin password before deploying this anywhere public.**

---

## 5. Project structure

```
app/
  Http/
    Controllers/
      DashboardController.php        # resident dashboard (redirects admins)
      ComplaintController.php        # resident: submit/view/history/receipt
      Admin/
        DashboardController.php      # admin stats dashboard
        ComplaintController.php      # admin: list/filter/view/update complaints
        DepartmentController.php     # admin: manage departments
        ReportController.php         # admin: reports + CSV export
    Middleware/
      EnsureUserIsAdmin.php          # blocks non-admins from /admin/*
    Requests/
      StoreComplaintRequest.php      # validates the complaint submission form
      UpdateComplaintStatusRequest.php
      ProfileUpdateRequest.php       # overrides Breeze's version (adds phone/address)
  Models/
    User.php, Complaint.php, ComplaintImage.php, ComplaintUpdate.php, Department.php
  Notifications/
    ComplaintStatusUpdated.php       # emails the resident on every status change

database/
  migrations/                       # 5 migrations, see table below
  seeders/                          # AdminUserSeeder, DepartmentSeeder, ComplaintSeeder

resources/views/
  layouts/     app.blade.php (resident navbar), admin.blade.php (sidebar), guest.blade.php (auth pages)
  partials/    styles.blade.php (design tokens/fonts/CDN links), flash-messages.blade.php
  auth/        login, register, forgot/reset password, verify email, confirm password
  welcome.blade.php, dashboard.blade.php
  complaints/  create, index, show, receipt
  profile/     edit
  admin/       dashboard, complaints/{index,show}, departments/index, reports/index

routes/web.php                      # all routes (Breeze's routes/auth.php is required at the bottom)
```

### Database tables

| Table | Purpose |
|---|---|
| `users` | Breeze's default table + `role` (`resident`/`admin`), `phone`, `address` |
| `departments` | Teams a complaint can be assigned to (e.g. Engineering Office) |
| `complaints` | Core table — see columns below |
| `complaint_images` | Multiple photos per complaint (`complaint_id`, `image_path`) |
| `complaint_updates` | Audit trail: every status change, who made it, and remarks |

`complaints` columns: `id`, `reference_number`, `user_id`, `title`, `description`,
`category`, `location`, `latitude`, `longitude`, `status`, `admin_remarks`,
`assigned_to`, `department_id`, `resolution_photo`, `resolved_at`, `created_at`, `updated_at`.

`reference_number` (e.g. `ECS-20260726-4F2K9`) is generated automatically in
`Complaint::booted()` — you never set it yourself.

---

## 6. How authentication is wired

- Breeze generates working controllers for register/login/logout/password
  reset/email verification. We didn't touch those.
- All Breeze **views** were replaced with Bootstrap 5 equivalents in this package.
- The single `/dashboard` route (which Breeze always redirects to after login)
  is handled by `DashboardController`, which checks `$user->isAdmin()` and
  redirects staff to `/admin/dashboard` — so you don't need two separate login forms.
- New registrations automatically get `role = resident` from the migration's
  column default — nothing extra to configure.
- Admin-only routes are protected by `Route::middleware(['auth', 'admin'])`.
  The `admin` alias is registered in `bootstrap/app.php` (Laravel 11 no longer
  uses `app/Http/Kernel.php` for this).
- Email verification is **not** enforced (the `User` model does not implement
  `MustVerifyEmail`), so residents aren't blocked from reporting problems while
  waiting on a verification email. You can turn it on later if you want it.

---

## 7. Routes reference

| Method | URI | Name | Who |
|---|---|---|---|
| GET | `/` | `home` | everyone |
| GET | `/dashboard` | `dashboard` | logged in |
| GET/POST | `/complaints...` | `complaints.*` | logged in |
| GET | `/profile` | `profile.edit` | logged in |
| GET | `/admin/dashboard` | `admin.dashboard` | admin |
| GET | `/admin/complaints` | `admin.complaints.index` | admin |
| GET | `/admin/complaints/{id}` | `admin.complaints.show` | admin |
| PUT | `/admin/complaints/{id}` | `admin.complaints.update` | admin |
| GET/POST/PUT/DELETE | `/admin/departments...` | `admin.departments.*` | admin |
| GET | `/admin/reports` | `admin.reports.index` | admin |
| GET | `/admin/reports/export` | `admin.reports.export` | admin (CSV download) |

---

## 8. Testing email notifications locally

With `MAIL_MAILER=log` in `.env`, every email (status-change notifications,
password reset links, verification links) is written to `storage/logs/laravel.log`
instead of actually being sent — open that file after changing a complaint's
status to see the full email content. To send real emails, switch to `smtp`
and fill in `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD` (e.g.
using Mailtrap for testing, or Gmail/SendGrid for production).

---

## 9. Troubleshooting

- **Uploaded photos show a broken image icon** → you forgot `php artisan storage:link`.
- **"Class not found" errors** → run `composer dump-autoload`.
- **419 Page Expired on form submit** → the form is missing `@csrf`, or your session expired — just resubmit.
- **"SQLSTATE... Unknown database"** → create the database first (Step 4).
- **Admin routes give a 403** → the logged-in account's `role` column isn't `admin`; check the `users` table.
- **Migration order errors** → make sure you copied all 5 files from `database/migrations/` — `complaints` depends on `departments` existing first.

---

## 10. Ideas if you want to extend this further

- Move authorization checks into Laravel Policies instead of inline checks.
- Add PHP 8.1 backed enums for `category`/`status` instead of plain strings.
- Queue the status-change email (`ShouldQueue` is already imported, just add `implements ShouldQueue`) so it doesn't block the request.
- Add SMS notifications (e.g. via a local telco SMS gateway) alongside email.
- Turn on `MustVerifyEmail` if you want confirmed email addresses.
- Export reports to PDF in addition to CSV.
- Add a super-admin role that can manage other admin accounts.

---

Built as a capstone/thesis-friendly reference implementation — the code favors
clarity and comments over cleverness, so it's meant to be read, not just run.
