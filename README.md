# Conference Scheduler

Conference Scheduler is a Symfony 8 web application for managing:
- Conferences
- Sessions
- Speakers
- Venues
- Rooms
- Registrations
- Users

## Tech Stack

- PHP 8.4+
- Symfony 8
- Doctrine ORM + Doctrine Migrations
- PostgreSQL
- Twig templates
- Webpack Encore + Stimulus/Turbo support

## Quick Start

1. Install dependencies:
```bash
composer install
```

2. Configure environment:
- Edit `.env`
- Set `DATABASE_URL` for your PostgreSQL database

3. Run migration:
```bash
php bin/console doctrine:migrations:migrate
```

4. Start server:
```bash
composer serve
```

Then open: `http://127.0.0.1:8000/`

## Main Routes

- `/` Dashboard
- `/login` Login
- `/register` Create account
- `/profile` User self-profile
- `/conference` Conference CRUD
- `/session` Session schedule + CRUD
- `/speaker` Speaker CRUD
- `/venue` Venue CRUD
- `/room` Room CRUD
- `/registration` Registration CRUD
- `/user` User role management (admin only)

## Role Permissions

- Public registration creates only `ROLE_USER` accounts.
- Users can view conference data and edit their own profile.
- User management page is accessible only by `ROLE_ADMIN`.
- Admins can promote users to admin and demote admins back to users.
- The system always keeps at least one admin account.

## Default Admin Account

Seeded via migration:
- Email: `Admin@gmail.com`
- Password: `admin@123`

## Seed Test Data

Use the built-in seed command to generate realistic data for all modules:

```bash
php bin/console app:seed:test-data --reset
```

What it creates:
- 1 admin account
- 8 normal users
- 4 speaker accounts + speaker profiles
- 3 venues + 6 rooms
- 4 conferences (past, running, upcoming)
- 8 sessions linked to speakers/rooms/conferences
- 10 registrations across conferences

Known login credentials:
- Admin: `Admin@gmail.com` / `admin@123`
- Normal user: `john.doe@example.com` / `user@123`
- Speaker user: `alex.chen.speaker@example.com` / `speaker@123`

## Validation Commands

```bash
php bin/console lint:container
php bin/console lint:twig templates
php bin/console doctrine:schema:validate
php bin/phpunit
```

## Performance Note (Dev)

This project includes `.php-dev.ini` and the `composer serve` script enables:
- `opcache.enable_cli=1`
- larger `realpath_cache`

This avoids very slow Symfony bootstrap times on Windows when using `php -S`.

## Notes

- Passwords are hashed via Symfony `UserPasswordHasherInterface`.
- Current test suite has no test cases yet (`No tests executed!`).
