# Cursor Rules for Pag-IBIG Loan Management System

This directory contains Cursor IDE rules for the Laravel-based Pag-IBIG Loan Management System.

## Files Overview

### 1-laravel-overview.mdc
**Laravel Framework Overview and Core Concepts**
- What is Laravel and how it works
- Project structure and directory organization
- MVC pattern explanation
- Deployment overview

### 2-project-conventions.mdc
**Project Conventions and Rules**
- Common patterns for this project
- PHP import and namespace rules
- Blade template conventions
- Model relationships
- Middleware and Form Request patterns
- Laravel dependency management

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
- User model configuration
- Auth middleware and route protection
- Spatie Permission for RBAC
- Authentication helpers and common issues

### 5-frontend-styling.mdc
**Frontend (Blade) and Styling (TailwindCSS)**
- Blade template conventions
- Data passing from controllers to views
- Blade directives and components
- TailwindCSS styling patterns
- Responsive design
- Pag-IBIG branding colors

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
- **Framework**: Laravel 10 (PHP 8+)
- **Database**: MySQL with Eloquent ORM
- **Frontend**: Blade templates with Tailwind CSS
- **Authentication**: Laravel Breeze + Spatie Permission
- **Hosting**: InfinityFree / 000WebHost (HTTPS-enabled)

### Key Directories
- `app/Models/` - Eloquent models
- `app/Http/Controllers/` - Controllers
- `app/Http/Requests/` - Form validation
- `app/Services/` - Business logic
- `resources/views/` - Blade templates
- `database/migrations/` - Schema migrations
- `routes/web.php` - Web routes

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
```

## RBAC Roles

- **Member**: Apply for loans, upload documents, view own loans
- **Loan Officer**: Verify applications, compute TAV, approve/reject loans
- **Admin**: Manage users, view all loans, generate reports
- **LGU Verifier**: Verify calamity-affected areas
- **Employer**: Certify employment and salary deductions

## Important Notes

- Always reference `routes/web.php` as the source of truth for routes
- Reference `ai/docs/` for feature specifications
- Use proper namespaces and imports
- Follow the MVC pattern strictly
- Implement RBAC middleware on protected routes
- Log all sensitive actions for compliance
- Use Tailwind CSS for all styling
- Maintain Pag-IBIG branding (blue and yellow colors)

## Next Steps

1. Review each `.mdc` file for detailed guidelines
2. Reference the `.cursorrules` file in the project root for additional context
3. Check `ai/docs/` for feature-specific documentation
4. Follow the patterns and conventions outlined in these rules

---

**Last Updated**: Nov 30, 2025
**Project**: Pag-IBIG Loan Management System
**Framework**: Laravel 10
