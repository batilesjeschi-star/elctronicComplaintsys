<?php
/**
 * PATCH FOR: config/filesystems.php
 *
 * InfinityFree (and most free shared hosts) disable PHP's symlink()
 * function for security, so `php artisan storage:link` won't work — and
 * you have no SSH to run it anyway. This patch stores uploaded files
 * directly inside public/storage instead of storage/app/public, so no
 * symlink is ever needed.
 *
 * HOW TO APPLY:
 * Open config/filesystems.php in your project and replace the 'public'
 * disk block (inside the 'disks' => [ ... ] array) with this version:
 */

'public' => [
    'driver' => 'local',
    'root' => public_path('storage'),   // <-- changed from storage_path('app/public')
    'url' => env('APP_URL').'/storage',
    'visibility' => 'public',
    'throw' => false,
],

/**
 * Then, before uploading, create an empty folder named "storage" inside
 * your local project's /public folder (it will be uploaded along with
 * everything else — see INFINITYFREE-DEPLOYMENT.md step 5).
 *
 * You do NOT need to run `php artisan storage:link` anywhere with this
 * change — remove that line if you had it in any deploy script.
 */
