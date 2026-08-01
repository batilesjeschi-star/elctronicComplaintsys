# Electronic Complaint System (ECS) — Barangay/LGU Capstone Project

A Laravel 11 web app that lets residents report community problems (potholes,
broken street lights, garbage, flooding, drainage, safety concerns, etc.) and
lets barangay admins track, assign, and resolve them.

This package contains the **application-layer code only** (models, migrations,
controllers, routes, views, seeders). You will generate the actual Laravel 11
skeleton with Composer first, then drop these files in. This is the standard
way Laravel projects are shared, and it keeps this package small and easy to
inspect file-by-file.

---

## 1. What's included in this zip

```
ecs-laravel/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Controller.php
│   │   │   ├── DashboardController.php          (resident dashboard)
│   │   │   ├── ComplaintController.php           (resident: create/view/receipt)
│   │   │   └── Admin/
│   │   │       ├── DashboardController.php       (admin stats dashboard)
│   │   │       └── ComplaintController.php       (admin: list/filter/update/report)
│   │   └── Middleware/
│   │       └── AdminMiddleware.php               (blocks non-admins from /admin/*)
│   ├── Mail/
│   │   └── ComplaintStatusUpdated.php            (status-change email)
│   └── Models/
│       ├── User.php
│       ├── Complaint.php
│       ├── ComplaintImage.php
│       ├── ComplaintUpdate.php
│       └── Department.php
├── bootstrap/
│   └── app.php                                   (registers the 'admin' middleware alias)
├── database/
│   ├── migrations/                               (5 migration files, see below)
│   └── seeders/
│       ├── DatabaseSeeder.php
│       ├── AdminUserSeeder.php                   (admin + sample resident accounts)
│       ├── DepartmentSeeder.php
│       └── ComplaintSeeder.php                   (5 sample complaints)
├── resources/views/
│   ├── layouts/app.blade.php                     (Bootstrap 5 layout + navbar)
│   ├── welcome.blade.php
│   ├── dashboard.blade.php                       (resident dashboard)
│   ├── complaints/
│   │   ├── create.blade.php                      (form + image preview + GPS button)
│   │   ├── index.blade.php                       (my complaints, search + pagination)
│   │   ├── show.blade.php                        (details + status history + photos)
│   │   └── receipt.blade.php                     (printable receipt)
│   ├── admin/
│   │   ├── dashboard.blade.php                   (stats cards)
│   │   ├── report.blade.php                      (daily/weekly/monthly report)
│   │   └── complaints/
│   │       ├── index.blade.php                   (all complaints, filters)
│   │       └── show.blade.php                    (manage: status/remarks/assign/resolve)
│   └── emails/complaint-status-updated.blade.php
├── routes/web.php
├── .env.example.additions                        (settings to copy into your .env)
└── README.md                                      (this file)
```

---

## 2. Prerequisites

- PHP 8.2+
- Composer
- MySQL 8 (or MariaDB)
- Node.js + npm (only needed if you want to compile assets; this project uses
  Bootstrap 5 via CDN, so it's optional)

---

## 3. Step-by-step setup

### Step 1 — Create a fresh Laravel 11 project

```bash
composer create-project laravel/laravel ecs-app "11.*"
cd ecs-app
```

### Step 2 — Install Laravel Breeze (authentication scaffolding)

Breeze gives you login, registration, password reset, email verification, and
the `ProfileController` referenced in `routes/web.php`.

```bash
composer require laravel/breeze --dev
php artisan breeze:install blade
npm install && npm run build
```

Choose the **Blade** stack when prompted (this project's views are plain Blade
+ Bootstrap, not Breeze's default Tailwind styling — that's fine, we
overwrite the layout below).

### Step 3 — Copy the files from this package into your project

Copy everything from this zip's `ecs-laravel/` folder into your new `ecs-app/`
project, **overwriting** where prompted:

```bash
# from inside the extracted ecs-laravel/ folder
cp -r app/*        ../ecs-app/app/
cp -r bootstrap/*  ../ecs-app/bootstrap/
cp -r database/*   ../ecs-app/database/
cp -r resources/views/* ../ecs-app/resources/views/
cp routes/web.php  ../ecs-app/routes/web.php
```

> Breeze already created `app/Http/Controllers/ProfileController.php` and the
> `resources/views/auth/*`, `resources/views/profile/*` folders — **do not
> delete those**, we only replace `layouts/app.blade.php`, `dashboard.blade.php`,
> and add the new `complaints/` and `admin/` folders alongside them.

### Step 4 — Configure your `.env`

Open `.env` in `ecs-app/` and set your database + mail values. See
`.env.example.additions` in this package for the exact keys to add/confirm:

```
DB_CONNECTION=mysql
DB_DATABASE=ecs_db
DB_USERNAME=root
DB_PASSWORD=

FILESYSTEM_DISK=public
MAIL_MAILER=log
```

Create the database in MySQL:

```sql
CREATE DATABASE ecs_db;
```

### Step 5 — Link storage (for uploaded images)

```bash
php artisan storage:link
```

This makes `storage/app/public/complaints/...` accessible at
`public/storage/complaints/...`, which is what `asset('storage/...')` in the
views points to.

### Step 6 — Run migrations and seeders

```bash
php artisan migrate --seed
```

This creates all tables (`users`, `complaints`, `complaint_images`,
`complaint_updates`, `departments`) and seeds:

- **Admin login:** `admin@ecs.gov.ph` / `password`
- **Sample resident login:** `juan@example.com` / `password`
- 4 sample departments
- 5 sample complaints in different statuses

### Step 7 — Serve the app

```bash
php artisan serve
```

Visit `http://localhost:8000`.

- Log in as the **resident** account to file a complaint, view "My
  Complaints", and print a receipt.
- Log in as the **admin** account and go to `http://localhost:8000/admin/dashboard`
  to see stats, manage complaints, assign departments, and generate reports.

---

## 4. How the main features map to files

| Feature | Files |
|---|---|
| Resident registration/login | Breeze (`routes/auth.php`, `resources/views/auth/*`) |
| Complaint form + multi-image upload | `complaints/create.blade.php`, `ComplaintController@store` |
| Reference number / receipt | `Complaint::generateReferenceNumber()`, `complaints/receipt.blade.php` |
| Status tracking / audit trail | `ComplaintUpdate` model + `complaints/show.blade.php` "Status History" |
| Admin stats dashboard | `Admin\DashboardController`, `admin/dashboard.blade.php` |
| Filter by category/status/date | `Admin\ComplaintController@index`, `admin/complaints/index.blade.php` |
| Update status + remarks + assign | `Admin\ComplaintController@update`, `admin/complaints/show.blade.php` |
| Resolution photo upload | Same `update()` method, `resolution_photo` field |
| Email on status change | `App\Mail\ComplaintStatusUpdated`, sent from `Admin\ComplaintController@update` |
| Reports (daily/weekly/monthly) | `Admin\ComplaintController@report`, `admin/report.blade.php` |
| Admin-only route protection | `App\Http\Middleware\AdminMiddleware`, registered in `bootstrap/app.php` |

---

## 5. Validation rules used

**Resident complaint form** (`ComplaintController@store`):
- `title` — required, string, max 255
- `description` — required, string, max 5000
- `category` — required, must be one of Road/Garbage/Drainage/Street Light/Safety/Others
- `location` — required, string, max 255
- `latitude` / `longitude` — optional, numeric, valid GPS range
- `images.*` — optional, image, jpeg/jpg/png only, max 4MB each, max 5 files

**Admin update form** (`Admin\ComplaintController@update`):
- `status` — required, must be a valid status
- `admin_remarks` — optional, max 5000
- `assigned_to` — optional, max 255
- `department_id` — optional, must exist in `departments` table
- `resolution_photo` — optional, image, jpeg/jpg/png, max 4MB

---

## 6. Extending this project (ideas for your capstone writeup)

- Add SMS notifications (e.g. via Semaphore/Twilio) alongside email.
- Export reports to PDF/Excel (`barryvdh/laravel-dompdf` or
  `maatwebsite/excel`).
- Add a map picker (Leaflet.js + OpenStreetMap) instead of manual lat/lng
  entry.
- Add roles beyond "admin" (e.g. department staff who can only see complaints
  assigned to their department) using a `role` column or a package like
  Spatie Laravel-Permission.
- Add a public "complaints heat map" showing where issues cluster.

---

## 7. Troubleshooting

- **Images don't show up** → run `php artisan storage:link` and make sure
  `FILESYSTEM_DISK=public` is set in `.env`.
- **"Class ProfileController not found"** → make sure you ran
  `php artisan breeze:install blade` before copying these files; Breeze
  generates that controller and its views.
- **403 Forbidden on /admin/...** → your logged-in user's `is_admin` column
  is `false`. Either log in with the seeded admin account or update the
  column manually: `UPDATE users SET is_admin = 1 WHERE email = 'you@example.com';`
- **Emails not sending** → with `MAIL_MAILER=log`, emails are written to
  `storage/logs/laravel.log` instead of actually being sent — this is
  expected for local development.
