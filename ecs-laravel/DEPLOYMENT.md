# Going Live: Deploying ECS to the Internet

This guide assumes you've already followed `README.md` to build the full
Laravel project locally (`ecs-app/` with these files copied in, `composer
install` run, `vendor/` folder present). You need that complete project —
with `composer.json`, `artisan`, and the `vendor/` folder — pushed to a
**GitHub repo** before deploying, since the hosting platforms below build
straight from your repo.

```bash
cd ecs-app
git init
git add .
git commit -m "Initial ECS commit"
git branch -M main
git remote add origin https://github.com/<your-username>/ecs-app.git
git push -u origin main
```

> Make sure `.env` is in your `.gitignore` (Laravel adds this by default) —
> never commit real credentials. `.env.example` is fine to commit.

This package includes everything needed for a **Docker-based deploy**, which
works identically on Railway, Render, Fly.io, or any VPS with Docker
installed: `Dockerfile`, `docker/entrypoint.sh`, `.dockerignore`.

---

## Option A — Railway (recommended, easiest, free tier)

1. Go to [railway.app](https://railway.app) and sign in with GitHub.
2. **New Project → Deploy from GitHub repo** → select `ecs-app`.
   Railway detects the `Dockerfile` automatically (via `railway.json`).
3. **Add a database:** in the same project, click **+ New → Database →
   MySQL**. Railway provisions it and gives you connection variables.
4. **Set environment variables** on your app service (Settings → Variables):
   ```
   APP_NAME=Electronic Complaint System
   APP_ENV=production
   APP_DEBUG=false
   APP_KEY=                      (leave blank — entrypoint.sh generates one on first boot)
   APP_URL=https://<your-app>.up.railway.app

   DB_CONNECTION=mysql
   DB_HOST=${{MySQL.MYSQLHOST}}
   DB_PORT=${{MySQL.MYSQLPORT}}
   DB_DATABASE=${{MySQL.MYSQLDATABASE}}
   DB_USERNAME=${{MySQL.MYSQLUSER}}
   DB_PASSWORD=${{MySQL.MYSQLPASSWORD}}

   FILESYSTEM_DISK=public
   MAIL_MAILER=log
   ```
   (Railway lets you reference the MySQL service's variables directly like
   `${{MySQL.MYSQLHOST}}` — click "Add Variable Reference" in the UI instead
   of typing them by hand.)
5. **Generate a public domain:** Settings → Networking → **Generate Domain**.
   Railway gives you a free `https://ecs-app-production.up.railway.app` URL.
6. Click **Deploy**. Railway builds the Docker image, runs
   `docker/entrypoint.sh` (which auto-runs migrations), and starts Apache.
7. Once deployed, run the seeder once via Railway's web shell (Settings →
   the "⋮" menu → **Shell**, or the `railway run` CLI):
   ```bash
   php artisan db:seed
   ```
8. Visit your Railway URL — the app is live.

**About uploaded photos on Railway:** by default the container's filesystem
resets on every redeploy, so uploaded complaint photos would be lost. For a
capstone demo this is usually fine, but if you want photos to persist, add a
**Volume** in Railway (Settings → Volumes) mounted at
`/var/www/html/storage/app/public`.

---

## Option B — Render.com (also free, uses the included `render.yaml`)

1. Push your repo to GitHub (see above).
2. On [render.com](https://render.com): **New → Blueprint**, connect your
   repo. Render reads `render.yaml` and creates the web service + database
   automatically.
3. Add the remaining secrets Render asks for (`APP_KEY`, `DB_PASSWORD`, mail
   settings) in the dashboard.
4. Render builds the Dockerfile and deploys. You get a free
   `https://ecs-app.onrender.com` URL.
5. Run migrations/seeders the first time via Render's **Shell** tab:
   ```bash
   php artisan migrate --force
   php artisan db:seed
   ```

*Note: Render's free tier spins the service down after inactivity, so the
first request after idling takes ~30–50 seconds to wake up — worth
mentioning if your adviser tests it cold.*

---

## Option C — Any VPS (DigitalOcean, Linode, AWS Lightsail) with Docker

This gives you a real domain + full control — good if your school wants a
proper production deployment, not just a demo link.

1. Create a $4–6/mo droplet (Ubuntu 22.04), then SSH in and install Docker:
   ```bash
   curl -fsSL https://get.docker.com | sh
   ```
2. Clone your repo onto the server:
   ```bash
   git clone https://github.com/<your-username>/ecs-app.git
   cd ecs-app
   ```
3. Create a production `.env` on the server (copy `.env.example`, fill in
   real DB credentials — you can run MySQL in Docker too, see
   `docker-compose.yml` for the pattern, or use a managed MySQL database).
4. Build and run:
   ```bash
   docker build -t ecs-app .
   docker run -d --name ecs-app -p 80:80 --env-file .env ecs-app
   ```
5. Point your domain's DNS **A record** at the droplet's IP address.
6. Add HTTPS with a free certificate:
   ```bash
   sudo apt install certbot
   # then either run certbot in standalone mode, or put Nginx in front of
   # the container as a reverse proxy and use certbot's nginx plugin
   ```
   For a from-scratch capstone, the simplest HTTPS setup is putting
   [Caddy](https://caddyserver.com) in front of the container — it gets you
   free auto-renewing HTTPS with a 3-line Caddyfile pointing to
   `localhost:80`.

---

## Option D — Shared/cPanel hosting (no Docker, budget hosts)

If your school specifically wants a `.com` on a traditional host
(Hostinger, Namecheap, etc.):

1. Make sure the host supports **PHP 8.2+** and lets you set the document
   root to a subfolder (`public/`) — this is the #1 thing to check before
   buying, since Laravel *requires* the web root to point at `/public`, not
   the project root.
2. Upload the whole project via FTP/File Manager (excluding `.env`).
3. In cPanel, create a MySQL database + user, note the credentials.
4. Create `.env` on the server with those credentials and a real `APP_KEY`
   (generate it locally with `php artisan key:generate --show` and paste the
   value in).
5. Via cPanel's Terminal (if available) or SSH:
   ```bash
   composer install --no-dev --optimize-autoloader
   php artisan migrate --seed
   php artisan storage:link
   php artisan config:cache
   ```
6. If the host doesn't let you point the domain at `/public` directly, move
   the contents of `public/` into the document root and edit `index.php`'s
   `require` paths to point up one directory — this is the standard
   workaround documented by most shared hosts for Laravel.

---

## Quick recommendation

For a student capstone/thesis defense: **Railway (Option A)** is the
fastest path to a real, shareable `https://` link — no server admin
knowledge needed, free tier is enough for a demo, and it rebuilds
automatically every time you `git push`.
