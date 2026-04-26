# Conference Scheduler

Conference Scheduler is a Symfony 8 web app for managing conferences, sessions, speakers, venues, rooms, registrations, and users.

## Current Feature Set

- Authentication with form login (`/login`) and registration (`/register`)
- Role-based access:
  - `ROLE_USER`: can access the app after login
  - `ROLE_ADMIN`: can create/edit/delete data and manage users
- Dashboard (`/`) with summary metrics and upcoming/today schedule widgets
- Full module CRUD pages:
  - Conferences (`/conference`), including detail page (`/conference/{id}/show`)
  - Sessions (`/session`) with conference/date filters and conflict validation
  - Speakers (`/speaker`)
  - Venues (`/venue`)
  - Rooms (`/room`)
  - Registrations (`/registration`)
- Public schedule browsing:
  - Weekly timetable/schedule (`/schedule`)
  - Timetable session detail (`/session/{id}/detail`)
- User self-profile management (`/profile`)
- Admin user role management (`/user`) with promote/demote and last-admin protection

## UI Highlights (2026-04 updates)

- Redesigned, non-generic card-based index pages for:
  - Conferences
  - Sessions
  - Venues
  - Speakers
  - Rooms
- Hero sections, visual metrics chips, status/metadata pills, and responsive mobile-friendly layouts
- Timetable pages are available at:
  - `/schedule` (weekly timetable)
  - `/session/{id}/detail` (session timetable detail)

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
- Edit `.env` / `.env.local`
- Set `DATABASE_URL` for your PostgreSQL database

3. Run migrations:
```bash
php bin/console doctrine:migrations:migrate
```

4. Start app:
```bash
composer serve
```

Then open `http://127.0.0.1:8000/`.

## One-command Local Startup (Windows)

These scripts automate Docker startup + migration + cache clear + PHP server:

- PowerShell: `./start.ps1`
- CMD: `start.bat`

## Main Routes

- `/` Dashboard
- `/login` Login
- `/register` Create account
- `/logout` Logout
- `/profile` User self-profile
- `/schedule` Public schedule overview
- `/session/{id}/detail` Public session/timetable detail
- `/conference` Conference listing + CRUD
- `/conference/{id}/show` Conference detail
- `/session` Session schedule + CRUD
- `/speaker` Speaker CRUD
- `/venue` Venue CRUD
- `/room` Room CRUD
- `/registration` Registration CRUD
- `/user` User role management (admin only)

## Role Permissions

- Public registration creates only `ROLE_USER` accounts.
- All authenticated users can view scheduler modules.
- Admin-only mutations are protected with `#[IsGranted('ROLE_ADMIN')]` on create/edit/delete actions.
- User management is admin-only.
- The system prevents demoting the last remaining admin.

## Default Admin Account

Seeded by migration `Version20260412103000`:

- Email: `Admin@gmail.com`
- Password: `admin@123`

## Seed Test Data

Use the command below to generate realistic test data:

```bash
php bin/console app:seed:test-data --reset
```

Creates:

- 1 admin account
- 8 normal users
- 4 speaker users + speaker profiles
- 3 venues + 6 rooms
- 4 conferences (past/running/upcoming)
- 8 sessions linked with speakers/rooms/conferences
- 10 registrations

Sample credentials:

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

This project includes `.php-dev.ini`, and `composer serve` enables:

- `opcache.enable_cli=1`
- larger `realpath_cache`

This improves Symfony bootstrap speed on Windows.

## Notes

- Passwords are hashed via Symfony `UserPasswordHasherInterface`.
- The project still contains some legacy schema naming quirks (for example `usser_id`, `createAt`) for backward compatibility with existing migrations.
