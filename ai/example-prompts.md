## Structured Prompts for LLM-Assisted Development

## Structured Prompts for LLM-Assisted Development

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

> I need to design the database schema for the Application Management System.
>
> **Key Entities:**
> - User: Authentication, user accounts, and role (Admin, Officer, or Member)
> - Application: User-submitted applications with status tracking
> - Task: Work items and assignments
> - Document: User-uploaded documents for verification
> - AuditLog: System activity logging for compliance
>
> **Schema Requirements:**
> - All models must have timestamps (createdAt, updatedAt)
> - Use foreign keys for referential integrity
> - Follow naming conventions: camelCase for fields, PascalCase for models
> - Define models in `schema.prisma`
> - Run `wasp db migrate-dev` to create migrations
> - Define relationships using Prisma syntax
>
> **User Role Enum (Required):**
> ```prisma
> enum UserRole {
>   ADMIN
>   OFFICER
>   MEMBER
> }
> ```
>
> **Relationships to implement:**
> - User has role (UserRole enum)
> - User hasMany Applications
> - Application belongsTo User
> - Application hasMany Documents
> - Document belongsTo Application

### RBAC & Security Prompt

Use this prompt when implementing authentication and authorization:

> I need to implement role-based access control (RBAC) for the Application Management System.
>
> **User Roles (ONLY these three - never add others):**
> - **Admin**: Full system access - manage users, view all data, generate reports, system configuration
> - **Officer**: Manage applications, verify data, approve/reject items, process requests
> - **Member**: Submit applications, upload documents, view own data and status
>
> **Implementation:**
> 1. Define UserRole enum in `schema.prisma` with ADMIN, OFFICER, MEMBER
> 2. Add `role` field to User model with @default(MEMBER)
> 3. Create authorization helpers in `src/lib/auth.ts`
> 4. Check permissions in server operations using `context.user.role`
> 5. Use `authRequired: true` on protected pages in `main.wasp`
> 6. Implement role checks with HttpError(403) for unauthorized access
> 7. Log all sensitive actions in AuditLog
>
> **Example Authorization Helper:**
> ```typescript
> // src/lib/auth.ts
> import { type User } from 'wasp/entities'
> import HttpError from 'wasp/server/HttpError'
> 
> export function requireAdmin(user: User | null) {
>   if (!user) throw new HttpError(401, 'Not authenticated')
>   if (user.role !== 'ADMIN') throw new HttpError(403, 'Admin access required')
> }
> 
> export function requireOfficerOrAdmin(user: User | null) {
>   if (!user) throw new HttpError(401)
>   if (user.role !== 'ADMIN' && user.role !== 'OFFICER') {
>     throw new HttpError(403, 'Officer or Admin access required')
>   }
> }
> ```
>
> **Security Best Practices:**
> - Always check authorization in server operations, not just client-side
> - Use TypeScript for type safety
> - Validate and sanitize all inputs
> - Use Prisma's prepared statements (automatic protection from SQL injection)
> - Implement rate limiting on sensitive endpoints
> - Use HTTPS for all communication
> - Never expose sensitive data in error messages

### Testing & Debugging Prompt

Use this prompt when testing or debugging features:

> I need to test/debug the **[FEATURE_NAME]** feature.
>
> **Testing Checklist:**
> - [ ] Test with each user role (Admin, Officer, Member)
> - [ ] Verify role-based authorization blocks unauthorized access
> - [ ] Test input validation with invalid data
> - [ ] Check Prisma migrations applied correctly (`wasp db migrate-dev`)
> - [ ] Verify Prisma relationships work as expected
> - [ ] Test error handling and logging
> - [ ] Check React component rendering
> - [ ] Verify Shadcn-ui styling and responsiveness
> - [ ] Test in different browsers
>
> **Debugging Tools:**
> - Use `console.log()` for client-side debugging
> - Check server logs in the terminal running `wasp start`
> - Use browser developer tools (React DevTools, Network tab)
> - Use Prisma Studio: `wasp db studio` to view database
> - Run migrations: `wasp db migrate-dev`
> - Reset database: `wasp db reset` (development only)
> - Check `.wasp/out/` directory for generated code
>
> **Common Issues:**
> - Routes not working: Check `main.wasp` route declarations
> - Database queries failing: Check Prisma schema and relationships
> - Authentication failing: Verify auth config in `main.wasp` and User model
> - Components not rendering: Check React component syntax and imports
> - Permission denied: Verify role checks in server operations
> - Migration issues: Check `schema.prisma` syntax, run `wasp db migrate-dev`

### Documentation Prompt

Use this prompt when documenting features:

> I need to document the **[FEATURE_NAME]** feature in `./ai/docs/[feature].md`.
>
> **Documentation Structure:**
> 1. **Overview**: Brief description of the feature
> 2. **User Roles**: Which roles can access this feature (Admin, Officer, and/or Member)
> 3. **Database Schema**: Related Prisma models and relationships
> 4. **User Workflows**: Step-by-step workflows for each role
> 5. **Business Logic**: Key rules and calculations
> 6. **Implementation Files**: Point to relevant code files
>    - Models: `schema.prisma` (specific models)
>    - Pages: `main.wasp` (route declarations)
>    - Components: `src/features/[feature]/index.tsx`
>    - Operations: `src/features/[feature]/operations.ts`
>    - UI Components: `src/features/[feature]/components/`
> 7. **Validation Rules**: Input validation requirements
> 8. **Error Handling**: How errors are handled and logged
> 9. **Authorization**: Role-based access control implementation
>
> **Don't repeat information already in:**
> - `.cursor/rules/` (general guidelines)
> - Code comments in implementation files
> - Prisma schema (schema is self-documenting)
> - Rather, reference these and focus on workflows and business logic