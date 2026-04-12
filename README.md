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
php -S 127.0.0.1:8000 -t public
```

Then open: `http://127.0.0.1:8000/`

## Main Routes

- `/` Dashboard
- `/admin` Admin summary
- `/conference` Conference CRUD
- `/session` Session CRUD
- `/speaker` Speaker CRUD
- `/venue` Venue CRUD
- `/room` Room CRUD
- `/registration` Registration CRUD
- `/user` User CRUD

## Validation Commands

```bash
php bin/console lint:container
php bin/console lint:twig templates
php bin/console doctrine:schema:validate
php bin/phpunit
```

## Notes

- Passwords are hashed via Symfony `UserPasswordHasherInterface` in user create/edit flows.
- Current test suite has no test cases yet (`No tests executed!`).
