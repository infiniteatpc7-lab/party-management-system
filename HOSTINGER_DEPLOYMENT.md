# Hostinger Shared Hosting Deployment Guide - PartyManager (PMS)

This guide walks you through deploying **PartyManager (PMS)** on your **Hostinger Shared Hosting** account, based on the proven configuration from `company-admin`.

---

## Pre-Configured Files Included

Your repository already includes all necessary configuration files for Hostinger:
1. **Root `.htaccess`**: Automatically forces HTTPS and routes all incoming web requests into `/public` without exposing root files.
2. **`public/.htaccess`**: Protects `.env`, sets PHP memory limit (256M), max upload size (64M), and front-controller routing.
3. **`php.ini`**: Hostinger PHP extensions and performance settings.
4. **`.env.hostinger`**: Pre-configured production template with file session/cache drivers and MySQL settings.
5. **`public/build/`**: Pre-compiled Vite production CSS & JavaScript assets (no Node.js/npm required on Hostinger).
6. **`public/migrate-run.php`**: One-click web runner for database migrations, storage linking, and asset caching.
7. **`public/seed-run.php`**: One-click web runner for Super Admin account initialization.

---

## Step 1: Create MySQL Database in Hostinger hPanel

1. Log in to **Hostinger hPanel**.
2. Navigate to **Databases** &rarr; **MySQL Databases**.
3. Create a new database:
   - **Database Name**: e.g. `u123456789_pms`
   - **Username**: e.g. `u123456789_pmsuser`
   - **Password**: (Choose a strong password)
4. Note down your Database Name, Username, and Password.

---

## Step 2: Deploy Project Files to Hostinger

### Option A: Using Hostinger Git (Recommended)
1. In hPanel, go to **Advanced** &rarr; **Git**.
2. Fill in:
   - **Repository URL**: `https://github.com/infiniteatpc7-lab/party-management-system.git`
   - **Branch**: `main`
   - **Install directory**: `public_html` (or your subdomain folder)
3. Click **Create** & then **Deploy**.

### Option B: Upload via File Manager / ZIP
1. Zip your project folder (excluding `vendor/` and `.git/`).
2. Open Hostinger **File Manager** &rarr; `public_html`.
3. Upload the ZIP file and extract it.
4. If `vendor/` is missing, install dependencies via SSH (`composer install --no-dev --optimize-autoloader`) or upload the `vendor` folder.

---

## Step 3: Configure `.env` on Hostinger

1. In Hostinger **File Manager**, find `.env.hostinger`.
2. Rename it to `.env` (or duplicate it as `.env`).
3. Edit the following variables:
   ```dotenv
   APP_NAME="Party Management System"
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://yourdomain.com

   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=your_hostinger_database_name
   DB_USERNAME=your_hostinger_database_username
   DB_PASSWORD=your_hostinger_database_password
   ```

---

## Step 4: Run Migrations & Setup in Browser

If you do not have SSH access, run the setup directly in your web browser:

1. Open this URL in your browser:
   ```
   https://yourdomain.com/migrate-run.php?token=PMS_HOSTINGER_DEPLOY_2026
   ```
2. You will see green success messages:
   - Database tables migrated
   - Database seeded with Super Admin (`infiniteatpc7@gmail.com`) and sample venues
   - Storage link created
   - Config, routes, and views cached

*(Optional)* If you only ever need to re-seed or verify the Super Admin:
```
https://yourdomain.com/seed-run.php?token=PMS_HOSTINGER_DEPLOY_2026
```

---

## Step 5: Post-Deployment Security (Crucial!)

Once you see the setup success screen:
1. Go back to Hostinger **File Manager** &rarr; `public_html/public/` (or `public/`).
2. **Delete** `migrate-run.php`.
3. **Delete** `seed-run.php`.

---

## Step 6: Log In as Super Admin

1. Visit: `https://yourdomain.com/login`
2. Log in with:
   - **Email**: `infiniteatpc7@gmail.com`
   - **Password**: `Admin@123#`
3. You can change your email and password anytime under **Admin Profile** (`/profile`).
