# Conference Scheduler - Current Project Documentation

Generated from a full repository read on 2026-04-12.

## 1) Project Overview

This repository is a Symfony web application for conference scheduling domains: conferences, sessions, speakers, venues, rooms, registrations, and users.

Current implementation status:
- Backend entities, form types, repositories, and one migration are present.
- Controllers and Twig templates are scaffold-level (single `index()` action per module).
- Frontend is minimal (Stimulus bootstrap + simple CSS/JS setup).
- Security provider is configured for `App\\Entity\\User`, but no authentication mechanism is wired yet.

## 2) Runtime and Stack

- PHP: `>=8.4` (`composer.json`)
- Framework: Symfony `8.0.*` components
- ORM/DB: Doctrine ORM 3.x + PostgreSQL (DATABASE_URL defaults to `conference_scheduler` DB)
- Frontend tooling: Webpack Encore + AssetMapper + Stimulus + Turbo
- Testing: PHPUnit 13 (`phpunit.dist.xml`)
- Containers: Docker Compose includes PostgreSQL and Mailpit

## 3) Application Routing

Attribute routes are imported from controllers. Current page routes:
- `/home` -> `HomeController::index()`
- `/admin` -> `AdminController::index()`
- `/conference` -> `ConferenceController::index()`
- `/registration` -> `RegistrationController::index()`
- `/session` -> `SessionController::index()`
- `/speaker` -> `SpeakerController::index()`
- `/user` -> `UserController::index()`
- `/venue` -> `VenueController::index()`

All these actions currently render basic Twig placeholder pages.

## 4) Data Model (Doctrine Entities)

### Conference
- Fields: `name`, `description`, `location`, `startDate`, `endDate`, `status`, `createAt`, `createdAt`, `maxAttendees`, `isActive`
- Relations: ManyToOne `organizer` (`User`), OneToMany `sessions`, ManyToMany `registrations`

### Registration
- Fields: `registrationDate`, `status`, `ticketType`
- Relations: ManyToMany users (`usser` property), ManyToMany conferences (`conference` property)

### Room
- Fields: `name`, `capacity`, `building`, `floor`, `equipment`
- Relations: ManyToMany sessions, ManyToMany venues (`venue` property)

### Session
- Fields: `title`, `description`, `startTime`, `endTime`, `maxAttendees`, `sessionType`, `status`, `track`, `capacity`
- Relations: ManyToOne conference, ManyToMany speakers, ManyToMany rooms

### Speaker
- Fields: `name`, `email`, `company`, `jobTitle`, `bio`, `expertise`, `photo`, `socialLinks`
- Relations: OneToOne user (`usser`), ManyToMany sessions

### User
- Fields: `email`, `roles` (JSON), `password`, `firstName`, `lastName`, `phone`, `role`, `createdAt`
- Relations: ManyToMany registrations
- Implements: `UserInterface`, `PasswordAuthenticatedUserInterface`

### Venue
- Fields: `name`, `address`, `city`, `state`, `zipCode`, `capacity`, `facilities`
- Relations: ManyToMany rooms

## 5) Forms and Repositories

- Form classes exist for `Conference`, `Registration`, `Room`, `Session`, `Speaker`, and `Venue`.
- Repository classes exist for all entities.
- Repositories are mostly generated templates (no custom query logic yet).
- `UserRepository` includes password upgrade support (`PasswordUpgraderInterface`).

## 6) Database and Migration State

Migration file `Version20260407121044.php` creates:
- Core tables: `conference`, `registration`, `room`, `session`, `speaker`, `user`, `venue`
- Join tables: `registration_user`, `registration_conference`, `room_venue`, `session_speaker`, `session_room`
- Infrastructure table: `messenger_messages`

This migration aligns with current entity relations.

## 7) Frontend and Templates

- `templates/base.html.twig` includes Encore entry tags and `importmap('app')`.
- Each module template is a Symfony-generated placeholder page.
- `assets/app.js` imports Stimulus bootstrap and CSS.
- `assets/styles/app.css` currently sets `body` background to `skyblue`.
- `assets/controllers/csrf_protection_controller.js` provides CSRF token cookie/header behavior for Turbo/form submissions.

## 8) Configuration Summary

- `config/services.yaml`: default autowire/autoconfigure with `App\\` resource import.
- `config/packages/doctrine.yaml`: DB URL from env, attributes mapping, postgres identity strategy.
- `config/packages/security.yaml`: entity provider on `User.email`; no login/authenticator configured.
- `config/packages/messenger.yaml`: doctrine transport for async and failed queues.
- `config/packages/webpack_encore.yaml` + `webpack.config.js`: Encore build to `public/build`.
- `config/packages/asset_mapper.yaml` + `importmap.php`: importmap entry for `app` and Stimulus/Turbo.
- `compose.yaml` / `compose.override.yaml`: PostgreSQL + Mailpit.

## 9) Notable Observations

- README currently describes Symfony 5/API Platform architecture, but actual dependencies are Symfony 8 and there is no API Platform package in `composer.json`.
- Entity naming has multiple typos/inconsistencies (for example `usser`, lowercase type references like `conference`, and both `createAt` + `createdAt` in `Conference`).
- Controllers/templates are scaffold placeholders, so feature flows are not implemented yet.
- `config/reference.php` is an auto-generated Symfony config reference file.
- `composer.lock` includes 95 runtime packages and 32 dev packages.
- `symfony.lock` contains 27 Symfony recipe entries.

## 10) File Inventory (Current Snapshot)
- .editorconfig (13 lines)
- .env (43 lines)
- .env.dev (3 lines)
- .env.test (3 lines)
- .gitignore (23 lines)
- assets\app.js (9 lines)
- assets\controllers.json (15 lines)
- assets\controllers\csrf_protection_controller.js (62 lines)
- assets\controllers\hello_controller.js (15 lines)
- assets\stimulus_bootstrap.js (4 lines)
- assets\styles\app.css (3 lines)
- bin\console (15 lines)
- bin\phpunit (3 lines)
- compose.override.yaml (16 lines)
- compose.yaml (23 lines)
- composer.json (112 lines)
- composer.lock (9964 lines)
- config\bundles.php (16 lines)
- config\packages\asset_mapper.yaml (10 lines)
- config\packages\cache.yaml (15 lines)
- config\packages\csrf.yaml (10 lines)
- config\packages\debug.yaml (5 lines)
- config\packages\doctrine.yaml (41 lines)
- config\packages\doctrine_migrations.yaml (6 lines)
- config\packages\framework.yaml (12 lines)
- config\packages\mailer.yaml (3 lines)
- config\packages\messenger.yaml (21 lines)
- config\packages\monolog.yaml (52 lines)
- config\packages\notifier.yaml (12 lines)
- config\packages\property_info.yaml (3 lines)
- config\packages\routing.yaml (9 lines)
- config\packages\security.yaml (37 lines)
- config\packages\translation.yaml (5 lines)
- config\packages\twig.yaml (5 lines)
- config\packages\ux_turbo.yaml (4 lines)
- config\packages\validator.yaml (10 lines)
- config\packages\web_profiler.yaml (11 lines)
- config\packages\webpack_encore.yaml (36 lines)
- config\preload.php (4 lines)
- config\reference.php (1623 lines)
- config\routes.yaml (8 lines)
- config\routes\framework.yaml (4 lines)
- config\routes\security.yaml (3 lines)
- config\routes\web_profiler.yaml (7 lines)
- config\services.yaml (18 lines)
- importmap.php (27 lines)
- migrations\.gitignore (0 lines)
- migrations\Version20260407121044.php (90 lines)
- package.json (19 lines)
- phpunit.dist.xml (39 lines)
- PROJECT_DOCUMENTATION.md (179 lines)
- public\index.php (6 lines)
- README.md (153 lines)
- src\Controller\.gitignore (0 lines)
- src\Controller\AdminController.php (15 lines)
- src\Controller\ConferenceController.php (15 lines)
- src\Controller\HomeController.php (15 lines)
- src\Controller\RegistrationController.php (15 lines)
- src\Controller\SessionController.php (15 lines)
- src\Controller\SpeakerController.php (15 lines)
- src\Controller\UserController.php (15 lines)
- src\Controller\VenueController.php (15 lines)
- src\Entity\Conference.php (201 lines)
- src\Entity\Registration.php (104 lines)
- src\Entity\Room.php (129 lines)
- src\Entity\Session.php (182 lines)
- src\Entity\Speaker.php (151 lines)
- src\Entity\User.php (177 lines)
- src\Entity\Venue.php (126 lines)
- src\Form\ConferenceType.php (42 lines)
- src\Form\RegistrationType.php (36 lines)
- src\Form\RoomType.php (38 lines)
- src\Form\SessionType.php (47 lines)
- src\Form\SpeakerType.php (40 lines)
- src\Form\VenueType.php (34 lines)
- src\Kernel.php (8 lines)
- src\Repository\.gitignore (0 lines)
- src\Repository\ConferenceRepository.php (38 lines)
- src\Repository\RegistrationRepository.php (38 lines)
- src\Repository\RoomRepository.php (38 lines)
- src\Repository\SessionRepository.php (38 lines)
- src\Repository\SpeakerRepository.php (38 lines)
- src\Repository\UserRepository.php (53 lines)
- src\Repository\VenueRepository.php (38 lines)
- symfony.lock (341 lines)
- templates\admin\index.html.twig (16 lines)
- templates\base.html.twig (24 lines)
- templates\conference\index.html.twig (16 lines)
- templates\home\index.html.twig (16 lines)
- templates\registration\index.html.twig (16 lines)
- templates\session\index.html.twig (16 lines)
- templates\speaker\index.html.twig (16 lines)
- templates\user\index.html.twig (16 lines)
- templates\venue\index.html.twig (16 lines)
- tests\bootstrap.php (9 lines)
- translations\.gitignore (0 lines)
- webpack.config.js (60 lines)

