# Deploying ECS to InfinityFree

InfinityFree is free shared hosting, which comes with real constraints that
change how you deploy a Laravel app. Read this section first so nothing
surprises you mid-deploy:

| Limitation | What it means for you |
|---|---|
| **No SSH / terminal access** | You can't run `php artisan migrate`, `composer install`, or `storage:link` *on the server*. Everything must be prepared locally and uploaded. |
| **No Composer on the server** | You must run `composer install` on your own computer and upload the resulting `vendor/` folder. |
| **PHP version varies** | InfinityFree's available PHP versions change over time. **Check your control panel → PHP Configuration before starting** and pick the highest available. Laravel 11 requires **PHP 8.2+**. If only 8.1 or lower is offered on your account, Laravel 11 will not run — let me know and I can adapt this project to Laravel 10 (which supports PHP 8.1) instead. |
| **`symlink()` is disabled** | `php artisan storage:link` won't work even if you could run it. This package includes a config patch that avoids needing a symlink at all (see Step 3). |
| **Remote MySQL access is unreliable** | Free accounts often can't connect to their MySQL database from outside InfinityFree's own servers, so running migrations from your local machine against the live DB is not reliable. Instead, this package includes a ready-made `infinityfree/schema-and-seed.sql` file you import through **phpMyAdmin** (which does work) — no artisan needed. |
| **50 MB database cap, 30,000 inode/file cap** | Fine for a capstone demo. Just don't upload `node_modules`, `.git`, or test files (see `.dockerignore`-style exclusions below). |
| **Document root is fixed to `htdocs/`** | Laravel needs its web root at `/public`, not the project root. Free accounts can't repoint the document root, so we restructure folders instead (Step 4). |

None of this is a dealbreaker — it's the same trick thousands of student
Laravel deployments on free/shared hosting use. It just takes a few extra
manual steps instead of one `git push`.

---

## Step 1 — Build the project locally (with Composer, on your own machine)

Follow `README.md` fully first: `composer create-project`, install Breeze,
copy this package's files in. At the end you should have a complete,
working `ecs-app/` folder with a `vendor/` directory, and it should run
fine with `php artisan serve` locally.

Then install dependencies in **production mode** (smaller, faster, no dev
tools):

```bash
cd ecs-app
composer install --no-dev --optimize-autoloader
```

## Step 2 — Generate your APP_KEY locally

```bash
php artisan key:generate --show
```

Copy the output (looks like `base64:XXXXXXXX...`) — you'll paste it into
`.env` in Step 3.

## Step 3 — Prepare `.env` and the filesystem patch

1. Copy `.env.example` to `.env` and fill in:
   ```env
   APP_NAME="Electronic Complaint System"
   APP_ENV=production
   APP_KEY=base64:PASTE_YOUR_KEY_HERE
   APP_DEBUG=false
   APP_URL=http://yoursite.infinityfreeapp.com

   DB_CONNECTION=mysql
   DB_HOST=sqlXXX.infinityfree.com     # from your InfinityFree MySQL Databases page
   DB_PORT=3306
   DB_DATABASE=if0_XXXXXXXX_ecs        # from your InfinityFree MySQL Databases page
   DB_USERNAME=if0_XXXXXXXX
   DB_PASSWORD=your_db_password

   FILESYSTEM_DISK=public
   SESSION_DRIVER=file
   CACHE_STORE=file
   QUEUE_CONNECTION=sync

   MAIL_MAILER=log
   ```
   (Using `file` for session/cache and `sync` for queue avoids needing extra
   database tables that the default Laravel 11 setup expects — one less
   thing to manage on a host with no artisan access.)

2. Apply the symlink-avoidance patch: open
   `infinityfree/filesystems-public-disk-patch.php` in this package and
   follow its instructions to edit `config/filesystems.php` in your project
   — this makes uploaded complaint photos save directly into
   `public/storage` instead of needing `php artisan storage:link`.

3. In your local project, create an empty folder: `public/storage`
   (this is where uploaded images will physically live once deployed).

## Step 4 — Restructure folders for InfinityFree's `htdocs/`

InfinityFree only serves files placed inside `htdocs/`, but Laravel's web
root must be the `public/` folder specifically — everything else
(`app/`, `vendor/`, `.env`, etc.) must **not** be publicly accessible.
The standard fix: put the app code in a sibling folder next to `htdocs/`,
and only put the *contents* of `public/` inside `htdocs/`.

On your computer, prepare an upload folder like this:

```
upload/
├── htdocs/                  <- becomes InfinityFree's htdocs/ (web root)
│   ├── index.php            <- copy from infinityfree/public-index.php.template
│   ├── .htaccess            <- copy as-is from your project's public/.htaccess
│   ├── favicon.ico          <- (and any other files from public/, except index.php)
│   └── storage/             <- the empty folder from Step 3
└── laravel/                 <- everything else
    ├── app/
    ├── bootstrap/
    ├── config/
    ├── database/
    ├── resources/
    ├── routes/
    ├── storage/
    ├── vendor/
    ├── artisan
    ├── composer.json
    ├── composer.lock
    └── .env
```

Steps:
1. Copy every file from your project's `public/` folder **except**
   `index.php`** into `upload/htdocs/`.
2. Copy `infinityfree/public-index.php.template` into
   `upload/htdocs/index.php` (it's the same file with corrected paths —
   see comments inside it).
3. Copy everything else (`app`, `bootstrap`, `config`, `database`,
   `resources`, `routes`, `storage`, `vendor`, `artisan`, `composer.json`,
   `composer.lock`, `.env`) into `upload/laravel/`.
4. **Do not upload** `.git`, `tests`, `node_modules`, or
   `storage/logs/*.log` — keeps you well under the inode limit.

## Step 5 — Create the database and import the schema

1. In your InfinityFree control panel (VistaPanel): **MySQL Databases** →
   create a new database. Note the generated database name, username,
   password, and hostname (e.g. `sql301.infinityfree.com`) — put these into
   your `.env` from Step 3.
2. Open **phpMyAdmin** from the control panel, select your new database.
3. Go to the **Import** tab, choose the file
   `infinityfree/schema-and-seed.sql` from this package, and run the
   import. This creates all 5 tables (`users`, `departments`, `complaints`,
   `complaint_images`, `complaint_updates` + Breeze's
   `password_reset_tokens`) and inserts:
   - **Admin login:** `admin@ecs.gov.ph` / `password`
   - **Sample resident login:** `juan@example.com` / `password`
   - 4 sample departments and 5 sample complaints across every status

   > **Change these passwords after your first login** — go to each
   > account's Profile page, or just re-run
   > `UPDATE users SET password = '...' WHERE email = '...';` in phpMyAdmin
   > with a fresh bcrypt hash if you'd rather set your own before going
   > live.

## Step 6 — Set your PHP version

In VistaPanel, go to **PHP Configuration / PHP Version Selector** and choose
the highest version offered (8.2 or 8.3 if available). If your account
only offers up to 8.1, Laravel 11 will throw fatal errors — message me and
I'll adapt this package to Laravel 10 instead, which only needs PHP 8.1+.

## Step 7 — Upload everything via FTP

InfinityFree's web File Manager is slow and can time out on Laravel's
`vendor/` folder (thousands of small files). Use an FTP client instead —
**FileZilla** is free and works well:

1. Get your FTP hostname/username/password from VistaPanel → **FTP
   Accounts**.
2. Connect with FileZilla.
3. Upload the contents of your local `upload/htdocs/` folder into the
   server's `htdocs/` folder.
4. Upload your local `upload/laravel/` folder as a new folder named
   `laravel` **next to** (not inside) `htdocs/`, at the account root.
5. This will take a while — the `vendor/` folder alone is thousands of
   files. Let it finish; don't interrupt large uploads.

## Step 8 — Fix permissions

Through the File Manager or your FTP client, set these folders to be
writable by the web server (usually `755`, sometimes hosts require `777`
for storage folders specifically — try `755` first):

- `laravel/storage/` (and everything inside it, recursively)
- `laravel/bootstrap/cache/`
- `htdocs/storage/` (where uploaded complaint photos will be written)

## Step 9 — Test it

Visit `http://yoursite.infinityfreeapp.com`. You should see the ECS welcome
page. Log in with the admin or sample resident account from Step 5.

**Free SSL:** InfinityFree offers free Let's Encrypt SSL through VistaPanel
(takes 5–15 minutes to provision) — enable it, then update `APP_URL` in
`.env` to `https://` once it's active.

---

## Troubleshooting

- **500 Internal Server Error, blank page** → set `APP_DEBUG=true`
  temporarily in `.env` to see the real error (turn it back off
  afterward), or check `laravel/storage/logs/laravel.log` via File Manager.
- **"The stream or file could not be opened" / storage errors** → recheck
  Step 8 permissions on `laravel/storage/` and its subfolders.
- **Uploaded photos 404** → confirm you applied the `config/filesystems.php`
  patch from Step 3 (photos should be landing directly in `htdocs/storage/`,
  not `laravel/storage/app/public/`).
- **"Class not found" errors** → you likely uploaded `vendor/` without
  running `composer install --no-dev --optimize-autoloader` first, or an
  upload got interrupted partway. Re-check the `vendor/` folder is complete.
- **Login always fails even with the seeded accounts** → double check you
  imported `schema-and-seed.sql` completely (all `INSERT` statements ran,
  not just the `CREATE TABLE` ones) — check the `users` table has 2 rows in
  phpMyAdmin.
- **Emails never arrive** → expected, since `.env` uses `MAIL_MAILER=log`.
  InfinityFree free accounts don't support outbound SMTP for custom mailers
  reliably; if you need real emails, use a transactional email API like
  Brevo or Resend (both have free tiers and work over HTTPS, which
  InfinityFree does allow).

---

## Honest recommendation

InfinityFree works for a **capstone demo link**, but the no-SSH/no-Composer
constraints mean every future code change has to be re-uploaded manually
the same way (no `git push` auto-deploy). If you later want push-to-deploy
convenience, Railway or Render (covered in `DEPLOYMENT.md`) get you there
in minutes with a real free tier built for frameworks like Laravel — worth
switching to once the demo deadline passes.
