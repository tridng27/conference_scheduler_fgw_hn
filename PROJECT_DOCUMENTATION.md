# Conference Scheduler - Current Project Documentation

Generated from a full repository read on 2026-04-20.

## 1) Project Overview

This repository is a Symfony 8 web application for conference operations:

- Conferences
- Sessions
- Speakers
- Venues
- Rooms
- Registrations
- Users and role administration

Current implementation is feature-complete for server-rendered CRUD flows and dashboard/schedule browsing. It is no longer scaffold-only.

## 2) Runtime and Stack

- PHP: `>=8.4`
- Framework: Symfony `8.0.*`
- ORM/DB: Doctrine ORM + PostgreSQL
- Frontend: Twig + Webpack Encore + Stimulus/Turbo bootstrap
- Testing: PHPUnit 13
- Local infra: Docker Compose (PostgreSQL + Mailpit)

## 3) Security and Access Control

Configured in `config/packages/security.yaml`:

- Provider: Doctrine entity provider (`App\\Entity\\User`, by email)
- Auth: `form_login` with CSRF enabled
- Logout configured via firewall
- Public routes:
  - `/login`
  - `/register`
  - `/logout`
  - `/schedule`
  - `/session/{id}/detail`
  - `/conference/{id}/show`
- All other routes require `ROLE_USER`

Authorization model in controllers:

- Module create/edit/delete actions use `#[IsGranted('ROLE_ADMIN')]`
- User management (`/user`) is fully admin-guarded
- Last-admin protection exists when demoting users

## 4) Routing Map

### Core

- `/` and `/home` -> dashboard (`HomeController`)
- `/schedule` -> timetable/schedule overview (`ScheduleController::index`)
- `/session/{id}/detail` -> timetable session detail (`ScheduleController::sessionDetail`)

### Auth/Profile

- `/login` -> login form (`AuthController::login`)
- `/register` -> account registration (`AuthController::register`)
- `/logout` -> logout endpoint
- `/profile` -> self-profile edit + optional password update (`ProfileController`)

### Domain Modules

- `/conference`
  - index, new, edit, delete
  - detail page: `/{id}/show`
- `/session`
  - index with filters (conference/date)
  - new, edit, delete
- `/speaker` -> index, new, edit, delete
- `/venue` -> index, new, edit, delete
- `/room` -> index, new, edit, delete
- `/registration` -> index, new, edit, delete
- `/user` (admin only)
  - index
  - `/{id}/promote`
  - `/{id}/demote`

## 5) Data Model Snapshot

### Conference

- Fields: `name`, `description`, `location`, `startDate`, `endDate`, `status`, `createAt`, `createdAt`, `maxAttendees`, `isActive`
- Relations: organizer (`User`), sessions, registrations

### Session

- Fields: `title`, `description`, `startTime`, `endTime`, `maxAttendees`, `sessionType`, `status`, `track`, `capacity`
- Relations: conference, speakers, rooms

### Speaker

- Fields: `name`, `email`, `company`, `jobTitle`, `bio`, `expertise`, `photo`, `socialLinks`
- Relations: one-to-one with user (`usser_id` column), many-to-many sessions

### Venue

- Fields: `name`, `address`, `city`, `state`, `zipCode`, `capacity`, `facilities`
- Relations: many-to-many rooms

### Room

- Fields: `name`, `capacity`, `building`, `floor`, `equipment`
- Relations: many-to-many sessions, many-to-many venues

### Registration

- Fields: `registrationDate`, `status`, `ticketType`
- Relations: many-to-many users, many-to-many conferences

### User

- Fields: `email`, `roles`, `password`, `firstName`, `lastName`, `phone`, `role`, `createdAt`
- Interfaces: `UserInterface`, `PasswordAuthenticatedUserInterface`

## 6) Repository and Domain Logic

Custom repository logic includes:

- `ConferenceRepository`
  - `findRunningConferences()`
  - `findUpcomingConferences()`
- `SessionRepository`
  - `findSchedule(?conferenceId, ?date)`
  - `findTodaySchedule()`
  - `findUpcomingSessions(limit)`
  - `findByConferenceForConflict()`
- `UserRepository`
  - password upgrade support
  - `countAdmins()`

Controller-level business logic includes:

- Session schedule conflict checks on create/edit:
  - time overlap validation
  - room conflict detection
  - speaker conflict detection
- Conference create/edit timestamp fallback handling
- Admin demotion safety checks

## 7) Frontend and Template Status

The UI is actively implemented with custom Twig pages and shared base styling.

Recent UI refresh (April 2026):

- Redesigned index pages for:
  - conferences
  - sessions
  - venues
  - speakers
  - rooms
- Shift from plain generic tables to card/grid layouts with:
  - hero sections
  - metrics chips
  - metadata/status pills
  - responsive behavior for mobile/desktop

Additional implemented pages include:

- Dashboard with metrics and schedule widgets
- Conference detail view
- Timetable pages: schedule overview and session detail
- Auth and profile forms

## 8) Forms

Form types currently present:

- `ConferenceType`
- `SessionType`
- `SpeakerType`
- `VenueType`
- `RoomType`
- `RegistrationType`
- `RegistrationAccountType` (public signup)
- `ProfileType` (self profile editing)

## 9) Migrations and Seed Data

Current migration files:

- `Version20260407121044` (base schema)
- `Version20260412103000` (default admin seed)
- `Version20260420120000` (bulk mock data seed)
- `Version20260420130000` (speaker/session-room relationship correction)
- `Version20260420140000` (speaker assignment fill-in)

Runtime seed command:

- `php bin/console app:seed:test-data --reset`

Provides deterministic local test data for all core modules and sample credentials.

## 10) Local Run Workflow

Typical local bootstrap:

1. `composer install`
2. Configure `DATABASE_URL`
3. `php bin/console doctrine:migrations:migrate`
4. Start app with `composer serve`

Windows helper scripts are available:

- `start.ps1`
- `start.bat`

These scripts automate Docker startup, migrations, cache clear, and `php -S` startup.

## 11) Known Technical Quirks

- Legacy naming artifacts remain in schema/entity mapping for compatibility:
  - `speaker.usser_id`
  - `conference.createAt`
- A conservative future cleanup should introduce additive migrations before renaming to avoid breaking existing environments.

## 12) Current State Summary

As of 2026-04-20, this project has:

- Working authentication and role-based authorization
- Working CRUD flows for all main domains
- Working dashboard and public schedule browsing
- Admin user-role governance
- Data seeding support and sample credentials
- Updated visual design on major listing pages
