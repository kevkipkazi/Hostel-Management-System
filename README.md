# Hostel Management System

A Laravel + Blade hostel management application for a technical training institution.

## Features

- Secure Laravel authentication with role-based access
- Student registration and profile management
- Hostel and room management
- Booking requests and room allocations
- Payment recording and receipt generation
- Reports and export support
- Responsive dashboard and admin interface

## Default login accounts

After seeding the database, use these accounts:

- Administrator: admin@hostelms.test / password123
- Hostel Manager: manager@hostelms.test / password123
- Finance Officer: finance@hostelms.test / password123

## Local setup

1. Install PHP 8.3+, Composer, Node.js, and a local database engine.
2. Clone the project and install dependencies:

```bash
composer install
npm install
```

3. Copy the environment file and generate an application key:

```bash
cp .env.example .env
php artisan key:generate
```

4. Configure the database in `.env`.

This project uses SQLite by default in the local workspace setup. If you prefer MySQL, update the database settings to:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=hostel_management_system
DB_USERNAME=root
DB_PASSWORD=
```

5. Run migrations and seeders:

```bash
php artisan migrate --seed
```

6. Build the frontend assets:

```bash
npm run build
```

7. Start the Laravel server:

```bash
php artisan serve
```

Then visit `http://127.0.0.1:8000/login` and sign in using a seeded account.

## Running tests

```bash
php artisan test
```

## Project structure

- `app/Http/Controllers` — application controllers
- `app/Models` — Eloquent models
- `database/migrations` — schema definitions
- `database/seeders` — seeded users and sample records
- `resources/views` — Blade UI templates
- `routes/web.php` — application routes

## Notes

The app is intentionally protected behind Laravel authentication and user-role checks. Unauthenticated users are redirected to the login page.
# Hostel-Management-System
