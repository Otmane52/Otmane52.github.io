# Event Key Redemption App

PHP + MySQL application for managing premium event seats with key-based redemption, live seat updates, and lightweight admin tools. Deploy-ready for shared hosting (e.g., Hostinger).

## Features
- Bootstrap 5 frontend with dynamic event cards and modal key redemption.
- Live refresh every 15 seconds without page reloads.
- Secure key redemption flow with prepared statements, seat capping, and status updates when events become full.
- Simple admin screens for adding events and keys (password protected).
- Importable SQL schema with starter data.

## Getting Started
1. Create a MySQL database and update credentials in `public_html/db_connect.php` (or set `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`).
2. Import `database/schema.sql` via phpMyAdmin to create tables and seed demo data.
3. Upload the `public_html` directory to your hosting account root (or point your web root to it).
4. Open `/admin/admin_add_event.php` and `/admin/admin_add_keys.php` with the admin password (default `changeme123`, override via `ADMIN_PASSWORD` env var).
5. Visit `/index.php` to view events and redeem keys.

## Step-by-step: preview locally (no hosting needed)
These steps spin up the app on your laptop with PHP’s built-in server.

1. **Install dependencies**
   - Install PHP 8+ with `pdo_mysql` enabled and MySQL or MariaDB server. On Ubuntu/Debian:
     ```bash
     sudo apt update && sudo apt install php php-mysql mysql-server
     ```

2. **Create the database and import demo data**
   ```bash
   # enter MySQL shell (set a password if prompted)
   sudo mysql
   CREATE DATABASE event_keys DEFAULT CHARACTER SET utf8mb4;
   EXIT;

   # import schema + seed rows
   mysql -u root -p event_keys < database/schema.sql
   ```

3. **Point the app at your database**
   - Edit `public_html/db_connect.php` and set `DB_HOST`, `DB_NAME`, `DB_USER`, and `DB_PASS` to match your local MySQL credentials.
   - Alternatively, export environment variables before running PHP:
     ```bash
     export DB_HOST=127.0.0.1
     export DB_NAME=event_keys
     export DB_USER=root
     export DB_PASS=your_password
     export ADMIN_PASSWORD=changeme123  # optional override
     ```

4. **Start the frontend**
   ```bash
   php -S 0.0.0.0:8000 -t public_html
   ```
   This serves the site at http://localhost:8000.

5. **Try it out**
   - Go to `http://localhost:8000/` to view events and redeem a demo key.
   - Visit `http://localhost:8000/admin/admin_add_event.php` (password prompt) to add events.
   - Visit `http://localhost:8000/admin/admin_add_keys.php` (password prompt) to add keys.

6. **Resetting demo data (optional)**
   If you want to start fresh, re-run the import:
   ```bash
   mysql -u root -p event_keys < database/schema.sql
   ```

## File Structure
```
public_html/
│── index.php              # Frontend
│── redeem.php             # Redeem API (POST)
│── fetch_events.php       # Events API (GET)
│── db_connect.php         # PDO connection (protected by .htaccess)
│── assets/
│   ├── style.css          # Custom styles
│   └── script.js          # Frontend JS
│── admin/
│   ├── admin_add_event.php
│   └── admin_add_keys.php
└── database/
    ├── schema.sql         # Schema + seed data
    └── .htaccess          # Blocks direct access
```

## Security Notes
- All database operations use prepared statements and sanitized inputs.
- `public_html/.htaccess` blocks direct access to `db_connect.php` and disables directory listing.
- Adjust the admin password and rotate seed keys before production use.
