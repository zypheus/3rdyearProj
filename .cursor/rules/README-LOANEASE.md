# Cursor Rules for LoanEase Loan Management System

This directory contains Cursor IDE rules for the Laravel 12-based **LoanEase** loan management system.

## Files Overview

### 1-wasp-overview.mdc
**Laravel 12 Framework Overview and Core Concepts**
- What is Laravel and how it works
- Project structure and directory organization
- MVC pattern explanation
- Three-role RBAC system (Admin, Officer, Member - NO OTHER ROLES)
- Deployment overview

### 2-project-conventions.mdc
**Project Conventions and Rules**
- Common Laravel patterns for this project
- PHP import and namespace rules
- Blade template conventions
- Model relationships
- Middleware and Form Request patterns
- Laravel dependency management with Composer and Yarn
- Vite frontend asset compilation

### 3-database-operations.mdc
**Database, Models, and Operations**
- Eloquent ORM and database models
- Database migrations and schema rules
- Model relationships
- Controller operations (queries and actions)
- Form Request validation
- Server-side error handling

### 4-authentication.mdc
**Authentication and Authorization**
- Laravel Breeze authentication setup
- User model configuration with role field
- Auth middleware and route protection
- Spatie Permission for RBAC with three roles
- Role checks in controllers and middleware (Admin, Officer, Member ONLY)
- Authentication helpers and common issues

### 5-frontend-styling.mdc
**Frontend (Blade) and Styling (TailwindCSS)**
- Blade template conventions
- Data passing from controllers to views
- Blade directives and components
- TailwindCSS styling patterns
- Responsive design
- Vite asset compilation

### 6-advanced-troubleshooting.mdc
**Advanced Features & Troubleshooting**
- Jobs and queues
- API routes
- Custom middleware
- Performance optimization
- Query optimization and caching
- Common troubleshooting steps

### 7-possible-solutions-thinking.mdc
**Problem-Solving Approach**
- Think about multiple solutions before implementing
- Present the best solution with reasoning

### 8-deployment.mdc
**Deployment (Shared Hosting & Cloud Platforms)**
- Prerequisites for deployment
- Deployment steps for shared hosting
- FTP and Git deployment methods
- Server configuration
- Database setup
- SSL/HTTPS configuration
- Post-deployment verification
- Troubleshooting deployment issues

## How to Use These Rules

1. **Cursor IDE Integration**: These `.mdc` files are automatically loaded by Cursor IDE when working on this project
2. **Always Apply**: Files marked with `alwaysApply: true` are always active
3. **Conditional Apply**: Files marked with `alwaysApply: false` are applied based on context

## Quick Reference

### Key Technologies
- **Framework**: Laravel 12 (PHP 8+)
- **Database**: MySQL with Eloquent ORM
- **Frontend**: Blade templates with Tailwind CSS
- **Frontend Tooling**: Vite (yarn dev for HMR, yarn build for production)
- **Authentication**: Laravel Breeze
- **Hosting**: HTTPS-enabled servers (shared hosting, VPS, cloud platforms)

### Key Directories
- `app/Models/` - Eloquent models
- `app/Http/Controllers/` - Controllers
- `app/Http/Requests/` - Form validation
- `app/Services/` - Business logic
- `resources/views/` - Blade templates
- `database/migrations/` - Schema migrations
- `routes/web.php` - Web routes
- `vite.config.js` - Frontend asset compilation

### Common Commands
```bash
# Create migration
php artisan make:migration create_table_name

# Create model
php artisan make:model ModelName

# Create controller
php artisan make:controller FeatureController

# Run migrations
php artisan migrate

# Clear cache
php artisan cache:clear && php artisan config:clear

# Start development server
php artisan serve

# Vite frontend compilation
yarn dev                # Development with HMR
yarn build              # Production build
```

## RBAC Roles (THREE ONLY - NO EXCEPTIONS)

- **Admin**: Full system access, manage users, view all loans, generate reports, system configuration
- **Officer**: Process applications, verify documents, approve/reject loans, manage requests
- **Member**: Submit applications, upload documents, track application status

**NEVER add other roles like**: LGU Verifier, Employer, Manager, or any custom role. The system is restricted to exactly three roles.

## Important Notes

- Always reference `routes/web.php` as the source of truth for routes
- Reference `ai/docs/` for feature specifications
- Use proper namespaces and imports in PHP files
- Follow the MVC pattern strictly
- Implement RBAC middleware on protected routes (check for Admin, Officer, or Member only)
- Log all sensitive actions for compliance
- Use Tailwind CSS for all styling
- Use Vite for all frontend asset compilation (`yarn dev` and `yarn build`)
- Compile assets with `yarn dev` during development, `yarn build` for production

## Next Steps

1. Review each `.mdc` file for detailed guidelines
2. Reference the `.cursorrules` file in the project root for additional context
3. Check `ai/docs/` for feature-specific documentation
4. Check `ai/example-prompts.md` for LLM-assisted development prompts
5. Follow the patterns and conventions outlined in these rules

---

**Last Updated**: Nov 30, 2025
**Project**: LoanEase - Loan Management System
**Framework**: Laravel 12
**Database**: MySQL
**Frontend**: Blade + Tailwind CSS with Vite
**Roles**: Admin, Officer, Member (3 roles only - NO exceptions)
