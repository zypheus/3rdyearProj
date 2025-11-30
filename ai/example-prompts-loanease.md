## Structured Prompts for LLM-Assisted Development - LoanEase

### System Context Prompt

Use this prompt when starting work on a feature to establish project context:

> I'm working on **LoanEase**, a modern loan management system built with Laravel 12, MySQL, and Tailwind CSS. This is a web application for managing loan applications, approvals, and reporting with comprehensive role-based access control.
>
> **Key Context:**
> - Framework: Laravel 12 (PHP 8+)
> - Frontend: Blade templates with Tailwind CSS
> - Frontend Tooling: Vite with Yarn/npm for asset bundling and HMR
> - Database: MySQL with Eloquent ORM
> - Authentication: Laravel Breeze / Custom auth with role-based access
> - **User Roles (ONLY these three)**: Admin, Officer, Member
>
> **Architecture:**
> - Three-tier: Presentation (Blade/Tailwind) → Application (Laravel) → Database (MySQL)
> - Models in `app/Models/` with Eloquent relationships
> - Controllers in `app/Http/Controllers/` organized by feature
> - Business logic in `app/Services/` for reusability
> - RBAC middleware for route protection
> - Migrations in `database/migrations/` for schema management
> - Frontend assets compiled via Vite (`vite.config.js`)
>
> **Important Rules:**
> - Always reference `.cursor/rules/` for development guidelines
> - Reference `./ai/docs/{feature}.md` for feature specifications
> - **CRITICAL**: Only use three roles: Admin, Officer, Member (never add other roles)
> - Implement role checks in controllers and middleware (server-side)
> - Log all sensitive actions in AuditLog
> - Use Tailwind CSS for all styling
> - Compile assets with Vite: `yarn dev` for development, `yarn build` for production
> - Implement proper error handling with try-catch and logging
>
> When implementing features, always:
> 1. Check `.cursor/rules/` for Laravel-specific patterns
> 2. Reference feature docs in `./ai/docs/`
> 3. Follow the three-tier architecture
> 4. Implement RBAC using only Admin, Officer, and Member roles
> 5. Write migrations for schema changes with `php artisan make:migration`
> 6. Use Eloquent relationships for data associations
> 7. Validate inputs with Form Request classes
> 8. Log errors and sensitive actions to AuditLog
> 9. Run `yarn dev` for frontend HMR during development

### Feature Implementation Prompt

Use this prompt when implementing a new feature:

> I need to implement the **[FEATURE_NAME]** feature for the LoanEase system.
>
> **Feature Requirements:**
> - [List key requirements]
> - Required roles: [Admin / Officer / Member - specify which roles can access]
>
> **Implementation Checklist:**
> 1. Create/update Eloquent models in `app/Models/` with relationships
> 2. Create migration with `php artisan make:migration [name]`
> 3. Create controller with `php artisan make:controller [name]` (organized by feature)
> 4. Create Form Request with `php artisan make:request [name]` for validation
> 5. Create service in `app/Services/` if needed for business logic
> 6. Create views in `resources/views/[feature]/` using Blade + Tailwind
> 7. Add routes in `routes/web.php` with appropriate middleware
> 8. Add role-based middleware checks (Admin, Officer, or Member)
> 9. Add audit logging for sensitive actions
> 10. Document in `./ai/docs/[feature].md` with role requirements
> 11. Compile frontend assets with `yarn dev` during development
>
> **Before starting:**
> - Check if models already exist in `app/Models/`
> - Review existing migrations in `database/migrations/`
> - Check `.cursor/rules/` for Laravel patterns and best practices
> - Reference `./ai/docs/` for related features
> - Verify which roles should have access to this feature

### Database Schema Prompt

Use this prompt when designing or updating database schema:

> I need to design the database schema for the LoanEase system.
>
> **Key Entities:**
> - User: Authentication, user accounts, and role (Admin, Officer, or Member)
> - Loan: Loan applications with status tracking (Pending, Approved, Rejected)
> - Document: User-uploaded documents for verification
> - AuditLog: System activity logging for compliance and tracking
> - Payment: Loan payment records (optional)
>
> **Schema Requirements:**
> - All tables must have timestamps (created_at, updated_at)
> - Use foreign keys for referential integrity
> - Follow naming conventions: snake_case for columns, plural for tables
> - Create migrations in `database/migrations/`
> - Define relationships in Eloquent models in `app/Models/`
>
> **User Role Storage (Required):**
> Store roles as VARCHAR or ENUM in database:
> - admin
> - officer
> - member
>
> **Example Migration:**
> ```php
> Schema::create('users', function (Blueprint $table) {
>   $table->id();
>   $table->string('name');
>   $table->string('email')->unique();
>   $table->string('password');
>   $table->enum('role', ['admin', 'officer', 'member'])->default('member');
>   $table->timestamps();
> });
> ```
>
> **Relationships to implement:**
> - User hasMany Loans
> - Loan belongsTo User
> - Loan hasMany Documents
> - Document belongsTo Loan

### RBAC & Security Prompt

Use this prompt when implementing authentication and authorization:

> I need to implement role-based access control (RBAC) for the LoanEase system.
>
> **User Roles (ONLY these three - never add others):**
> - **Admin**: Full system access - manage users, view all loans, generate reports, system configuration
> - **Officer**: Process loan applications, verify documents, approve/reject loans
> - **Member**: Submit loan applications, upload documents, view own application status
>
> **Implementation:**
> 1. Add `role` column to users table migration (enum or varchar)
> 2. Add helper methods to User model: `isAdmin()`, `isOfficer()`, `isMember()`
> 3. Create middleware in `app/Http/Middleware/RoleMiddleware.php`
> 4. Register middleware in `app/Http/Kernel.php`
> 5. Create authorization service in `app/Services/AuthService.php`
> 6. Protect routes with `role:admin`, `role:officer`, `role:member` middleware
> 7. Check permissions in controllers and services
> 8. Log all sensitive actions in AuditLog
>
> **Example Middleware:**
> ```php
> // app/Http/Middleware/RoleMiddleware.php
> namespace App\\Http\\Middleware;
> 
> use Closure;
> use Illuminate\\Http\\Request;
> 
> class RoleMiddleware
> {
>   public function handle(Request $request, Closure $next, string $role): mixed
>   {
>     if (!auth()->check() || auth()->user()->role !== $role) {
>       abort(403, 'Unauthorized');
>     }
>     return $next($request);
>   }
> }
> ```
>
> **Security Best Practices:**
> - Always check authorization in controllers, not just UI
> - Use Form Request classes for validation
> - Hash passwords with Hash::make()
> - Validate and sanitize all inputs
> - Use prepared statements (Eloquent prevents SQL injection)
> - Implement rate limiting on sensitive endpoints
> - Use HTTPS for all communication

### Testing & Debugging Prompt

Use this prompt when testing or debugging features:

> I need to test/debug the **[FEATURE_NAME]** feature.
>
> **Testing Checklist:**
> - [ ] Test with each user role (Admin, Officer, Member)
> - [ ] Verify role-based middleware blocks unauthorized access
> - [ ] Test form validation with invalid inputs
> - [ ] Check database migrations applied correctly (`php artisan migrate`)
> - [ ] Verify Eloquent relationships work as expected
> - [ ] Test error handling and logging
> - [ ] Check Blade template rendering
> - [ ] Verify Tailwind CSS styling
> - [ ] Test in multiple browsers
>
> **Debugging Tools:**
> - Use `dd()` for quick debugging
> - Use Laravel Tinker: `php artisan tinker`
> - Check logs in `storage/logs/laravel.log`
> - Use browser developer tools for frontend issues
> - Run migrations: `php artisan migrate`
> - Clear cache: `php artisan cache:clear && php artisan config:clear`
> - Use Vite dev server: `yarn dev` for HMR debugging
>
> **Common Issues:**
> - Routes not working: Check `routes/web.php` and middleware registration
> - Database queries failing: Check model relationships and migrations
> - Authentication failing: Verify `.env` and user roles in database
> - Views not rendering: Check Blade syntax and variable availability
> - Permission denied: Verify role middleware and authorization checks
> - Vite not compiling: Check `vite.config.js` and run `yarn dev`

### Documentation Prompt

Use this prompt when documenting features:

> I need to document the **[FEATURE_NAME]** feature in `./ai/docs/[feature].md`.
>
> **Documentation Structure:**
> 1. **Overview**: Brief description of the feature
> 2. **User Roles**: Which roles can access this feature (Admin, Officer, and/or Member)
> 3. **Database Schema**: Related models and relationships
> 4. **User Workflows**: Step-by-step workflows for each role
> 5. **Business Logic**: Key rules and calculations
> 6. **Implementation Files**: Point to relevant code files
>    - Models: `app/Models/[Model].php`
>    - Controllers: `app/Http/Controllers/[Controller].php`
>    - Views: `resources/views/[feature]/`
>    - Services: `app/Services/[Service].php`
>    - Migrations: `database/migrations/`
> 7. **Validation Rules**: Input validation requirements
> 8. **Error Handling**: How errors are handled and logged
> 9. **API Endpoints**: List routes and their purposes
>
> **Don't repeat information already in:**
> - `.cursor/rules/` (general guidelines)
> - Code comments in implementation files
> - Database migrations (schema is self-documenting)
> - Rather, reference these and focus on workflows and business logic
