# Conference Scheduler 🎤

A modern web application for managing conferences, speakers, sessions, and attendees. Built with professional web technologies for a scalable and maintainable solution.

## 🚀 Tech Stack

- **Backend Framework:** Symfony 5.x
- **API Layer:** API Platform (RESTful with Swagger/OpenAPI)
- **Database:** PostgreSQL
- **ORM:** Doctrine ORM
- **Frontend Language:** TypeScript + JavaScript
- **Frontend Markup:** HTML5, CSS3, Bootstrap 5
- **Frontend Approach:** API-first (SPA or frontend-agnostic)
- **Version Control:** Git

### Why This Stack?

**Symfony 5.x** provides a robust, professional framework with excellent architecture patterns and community support.

**API Platform** offers zero-configuration REST/GraphQL APIs with automatic Swagger documentation—perfect for building flexible, decoupled applications.

**PostgreSQL** with **Doctrine ORM** delivers type-safe database interactions, powerful query capabilities, and excellent data integrity through migrations and constraints.

**TypeScript + JavaScript** on the frontend enables type-safe development while remaining flexible.

**API-First Architecture** allows the frontend to evolve independently from the backend, enabling future mobile apps, different frontends, or third-party integrations to use the same API.

## 📋 Project Overview

Conference Scheduler is designed to simplify the management of conferences with features including:
- Conference management and scheduling
- Speaker registration and management
- Session organization and tracking
- Attendee registration and enrollment
- Real-time conference details and updates
- Interactive web interface
- RESTful API for programmatic access

## 🛠️ Prerequisites

Before getting started, ensure you have:
- PHP 7.4 or higher
- Composer
- PostgreSQL 12 or higher
- Git
- Node.js & npm (optional, for frontend assets)

## ⚡ Quick Start

### 1. Clone & Setup

```bash
# Navigate to project directory
cd conference_scheduler_fgw_hn

# Install PHP dependencies
composer install

# Set up environment
cp .env.example .env
# Edit .env and configure your PostgreSQL connection
```

### 2. Database Configuration

```bash
# Create database
php bin/console doctrine:database:create

# Run migrations
php bin/console doctrine:migrations:migrate

# (Optional) Load sample data
php bin/console doctrine:fixtures:load --no-interaction
```

### 3. Start Development Server

```bash
php bin/console server:run
```

The API will be available at `http://localhost:8000/api`

**Frontend Setup:** Create a separate TypeScript/JavaScript application to consume this API.

## 📌 Access Points

| Component | URL | Purpose |
|-----------|-----|---------|
| **API Base** | `http://localhost:8000/api` | RESTful API endpoints |
| **API Docs** | `http://localhost:8000/api/doc` | Swagger UI documentation |
| **OpenAPI JSON** | `http://localhost:8000/api/openapi.json` | OpenAPI specification |

## 📁 Project Structure

```
conference_scheduler_fgw_hn/
├── src/
│   ├── Controller/      # API Controllers
│   ├── Entity/          # Doctrine entities with @ApiResource
│   ├── Repository/      # Database queries
│   ├── Service/         # Business logic
│   └── Security/        # Authentication & authorization
├── public/              # Static assets
├── migrations/          # Doctrine migrations
├── config/              # Configuration files
│   ├── packages/api_platform.yaml
│   └── routes.yaml
└── vendor/              # Dependencies
```

**Note:** Frontend application (TypeScript/JavaScript) is separate and consumes the API via HTTP requests.

## 🔑 Key Features

✨ Comprehensive REST API for all operations  
✨ Automatic Swagger/OpenAPI documentation  
✨ Conference, Speaker, and Session management  
✨ Attendee registration & enrollment  
✨ Type-safe database interactions with Doctrine ORM  
✨ PostgreSQL with migrations & constraints  
✨ API-first architecture for frontend flexibility  
✨ Authentication & authorization ready  

## 📖 Documentation

For detailed information, refer to:
- **Setup Guide:** [SETUP_GUIDE.md](SETUP_GUIDE.md)
- **API Guide:** [API_PLATFORM_GUIDE.md](API_PLATFORM_GUIDE.md)
- **Database Setup:** [PostgreSQL_SETUP.md](PostgreSQL_SETUP.md)
- **Implementation Guide:** [IMPLEMENTATION_GUIDE.md](IMPLEMENTATION_GUIDE.md)

## 🤝 Team Roles

Development is managed by a collaborative team with shared responsibility for:
- Backend development & APIs
- Frontend design & implementation
- Database architecture & optimization

## 📝 Contributing

- Make regular, focused commits
- Test features thoroughly before pushing
- Update documentation following the development progress
- Follow PSR-12 coding standards

## 🔒 Security

- SQL injection prevention via Doctrine ORM
- XSS protection through Twig escaping
- CSRF tokens on forms
- Password hashing with bcrypt
- Input validation on all endpoints

## ⚠️ Common Commands

```bash
# Clear cache
php bin/console cache:clear

# View all routes
php bin/console debug:router

# Create new entity
php bin/console make:entity

# Create database migration
php bin/console make:migration

# Create controller
php bin/console make:controller
```

## 🆘 Troubleshooting

**Issue:** Database connection error
- **Solution:** Verify PostgreSQL is running and .env DATABASE_URL is correct

**Issue:** Migrations fail
- **Solution:** Check database exists and user has proper permissions

**Issue:** Vendor folder issues
- **Solution:** Run `composer install` and `composer update`

## 📚 Resources

- [Symfony Docs](https://symfony.com/doc)
- [API Platform](https://api-platform.com)
- [Doctrine ORM](https://www.doctrine-project.org/)
- [PostgreSQL](https://www.postgresql.org/docs/)
- [TypeScript](https://www.typescriptlang.org/)
- [REST API Best Practices](https://restfulapi.net/)

## 📅 Development Timeline

- **Week 1:** Foundation & Setup
- **Week 2:** Core Implementation
- **Week 3:** Testing & Polish

## ✅ Success Criteria

- ✅ All features implemented
- ✅ API fully documented
- ✅ Database properly structured
- ✅ Responsive UI
- ✅ Clean git history with regular commits
- ✅ No security vulnerabilities

---

**Ready to build something great! 🎉**
