# PartyManager (PMS) - Party Management & Geo-Explorer System

A modern, high-performance web application built with **Laravel 12**, **Tailwind CSS**, and **Leaflet.js** for organizing, discovering, and managing parties, venues, and client leads with live GPS geolocation and interactive mapping.

[![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=flat&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.4-777BB4?style=flat&logo=php&logoColor=white)](https://php.net)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind-CSS-06B6D4?style=flat&logo=tailwindcss&logoColor=white)](https://tailwindcss.com)
[![Leaflet](https://img.shields.io/badge/Leaflet-1.9-199900?style=flat&logo=leaflet&logoColor=white)](https://leafletjs.com)

---

## Key Features

- **Interactive Map Explorer**
  - Live interactive Leaflet map with custom priority markers.
  - Multi-criteria real-time client-side and server-side filtering: **Priority** (Priority / Normal), **Party Type** (New / Old), **Lead Status** (Lead / Non-Lead), and **Distance Radius** (≤1 km, ≤5 km, ≤10 km, ≤25 km).
  - Accurate distance calculation using the Haversine formula from user's live GPS coordinates.
  - Split-screen desktop explorer & smooth mobile bottom-sheet modal.
  - One-click Google Maps Navigation integration.

- **Party & Lead Management**
  - Complete party records management (Party Name, Mobile Number, Map Links, Coordinates, Priority Status, Party Type, and Lead Checkbox).
  - Smart coordinate extraction from standard and short Google Maps links.
  - Duplicate detection on party creation and updates.

- **Bulk Excel & CSV Importer**
  - High-speed spreadsheet importing supporting `.xlsx`, `.xls`, and `.csv`.
  - Automatic column mapping, duplicate detection, and validation error download.
  - Downloadable starter sample template.

- **Soft Deletes & Recycle Bin**
  - Safe deletion with dedicated Recycle Bin.
  - Instant restore or permanent force deletion capabilities.

- **Curated Multi-Theme Engine**
  - **Shadow & Citrus**: High-contrast dark mode with lime-citrus accents.
  - **Black Iris & Frozen**: Midnight slate theme with ice blue highlights.
  - **Classic Light**: Crisp clean administrative interface.
  - Fully responsive custom select dropdowns adhering to zero-shadow theme constraints.

- **Super Admin & Profile Management**
  - Role-based administrator authentication.
  - Dedicated Profile Edit page allowing admins to update name, contact email, and password with real-time visibility toggles.

---

## Tech Stack

- **Backend**: Laravel 12, PHP 8.4
- **Database**: SQLite / MySQL / PostgreSQL supported
- **Frontend**: Blade Components, Tailwind CSS, Vanilla JS
- **Maps**: Leaflet.js & OpenStreetMap
- **Spreadsheets**: PhpSpreadsheet / Maatwebsite Excel
- **Tooling**: Vite, Pest / PHPUnit, Laravel Pint

---

## Installation & Setup

1. **Clone the repository:**
   ```bash
   git clone https://github.com/infiniteatpc7-lab/party-management-system.git
   cd party-management-system
   ```

2. **Install PHP dependencies:**
   ```bash
   composer install
   ```

3. **Install JavaScript dependencies:**
   ```bash
   npm install
   ```

4. **Configure environment:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Run migrations and database seeders:**
   ```bash
   php artisan migrate --seed
   ```

6. **Build frontend assets:**
   ```bash
   npm run build
   ```

7. **Start local development server:**
   ```bash
   php artisan serve
   ```

Visit the application in your browser at `http://127.0.0.1:8000`.

---

## Testing

Run the automated test suite with PHPUnit:
```bash
php artisan test --compact
```

Format code according to project conventions:
```bash
vendor/bin/pint
```

---

## License

This project is licensed under the [MIT License](LICENSE).
