# LoanEase: Project Structure & Guidelines

## Overview

**LoanEase** is a **Laravel 12-based Loan Management System** for managing loan applications, approvals, payments, and reporting with comprehensive role-based access control using three user roles: Admin, Officer, and Member.

## Development Guidelines

### 1. `.cursor/rules/` - Development Standards
Comprehensive Laravel development guidelines including:
- Framework architecture and patterns (Models, Controllers, Services, Middleware)
- Database design with Eloquent ORM and MySQL
- Authentication and RBAC implementation (Admin, Officer, Member roles only)
- Blade templating and Tailwind CSS styling
- Vite asset bundling for frontend development
- Validation, error handling, and security best practices
- Performance optimization and troubleshooting

### 2. `ai/example-prompts-loanease.md` - Structured Prompts for LLM
Reusable prompts designed to maintain context and guide feature implementation:
1. **System Context Prompt** - Establish project context with key information
2. **Feature Implementation Prompt** - Checklist for implementing new features
3. **Database Schema Prompt** - Design database schema with Eloquent relationships
4. **RBAC & Security Prompt** - Implement role-based access control (Admin, Officer, Member)
5. **Testing & Debugging Prompt** - Test and debug features
6. **Documentation Prompt** - Document features in `./ai/docs/`

## Project Structure

### Directory Layout
```
app/
  Models/          # Eloquent models (User, Loan, Document, Payment, etc.)
  Http/
    Controllers/   # Feature-based controllers
    Middleware/    # RBAC middleware
    Requests/      # Form validation
  Services/        # Business logic

resources/
  views/
    layouts/       # Main layout templates
    auth/          # Authentication views
    loans/         # Loan-related views
    admin/         # Admin dashboard
    reports/       # Report views

database/
  migrations/      # Schema migrations
  seeders/         # Initial data

routes/
  web.php          # Web routes with middleware

vite.config.js     # Vite configuration for asset bundling
package.json       # Frontend dependencies (Tailwind, etc.)
```

## Key Entities

The system manages these core entities:

- **User**: Authentication, user accounts, and role assignment (Admin, Officer, or Member)
- **Loan**: Loan applications with status tracking (Pending, Approved, Rejected)
- **Document**: User-uploaded documents for verification
- **Payment**: Loan payment records and tracking
- **AuditLog**: System activity logging for compliance and tracking

## RBAC Roles

**This system uses exactly three user roles:**

- **Admin**: Full system access
  - Manage all users and assign roles
  - View all loans and their status
  - Generate system reports
  - Configure application settings

- **Officer**: Loan processing and verification
  - Process and review loan applications
  - Verify and validate documents
  - Approve or reject loan applications
  - View loans within their jurisdiction

- **Member**: Basic user permissions
  - Submit loan applications
  - Upload required documents
  - View own loan application status
  - Update own profile

**Important**: No other roles should be created or referenced. All functionality must be implemented using only these three roles.

## How to Use These Guidelines

### When Starting a New Feature:
1. Read the **System Context Prompt** in `ai/example-prompts-loanease.md` to establish context
2. Check `.cursor/rules/` for Laravel patterns and best practices
3. Use the **Feature Implementation Prompt** as a checklist
4. Reference `./ai/docs/` for related features
5. Verify role requirements (Admin, Officer, or Member)

### When Implementing:
- Follow the three-tier architecture (Presentation → Application → Database)
- Use Eloquent models with relationships
- Implement RBAC middleware on protected routes
- Write migrations for schema changes with `php artisan make:migration`
- Use Form Request classes for validation
- Create services for business logic
- Use Blade templates with Tailwind CSS
- Compile frontend assets with Vite (`yarn dev` for HMR)
- Implement role checks using middleware and authorization services

### When Documenting:
- Use the **Documentation Prompt** structure
- Create one markdown file per feature in `./ai/docs/`
- Point to implementation files rather than repeating code
- Focus on workflows and business logic
- Clearly specify which roles can access each feature

## Key Technologies & Practices

- **Framework**: Laravel 12 with PHP 8+
- **Database**: MySQL with Eloquent ORM
- **Frontend**: Blade templates with Tailwind CSS
- **Frontend Tooling**: Vite for asset bundling and HMR
- **Authentication**: Laravel Breeze or custom auth with RBAC
- **Authorization**: Role-based (Admin, Officer, Member only)
- **Security**: HTTPS, CSRF protection, input validation, server-side authorization
- **Compliance**: All sensitive actions logged in AuditLog
- **Development Workflow**: `php artisan serve` for Laravel, `yarn dev` for Vite HMR

## Next Steps

1. Run `php artisan migrate` to set up initial tables
2. Update User migration to include `role` column (enum or varchar)
3. Create Eloquent models for Loan, Document, Payment, etc.
4. Create middleware for role-based access control
5. Create controllers and views for each feature
6. Add routes with appropriate middleware in `routes/web.php`
7. Implement RBAC checks in all operations
8. Set up Vite with `yarn dev` for frontend development
9. Document features in `./ai/docs/` with role requirements
10. Test with each user role (Admin, Officer, Member)
