# AI Development Guides - LoanEase

This directory contains structured guidelines and prompts for LLM-assisted development of the LoanEase loan management system (Laravel 12).

## Files

### `.cursor/rules/` (Directory)
**Development Standards & Best Practices**
- Wasp framework patterns and architecture
- Database design with Prisma ORM
- Authentication and RBAC implementation (Admin, Officer, Member only)
- Code organization and file structure
- Security best practices
- Performance optimization
- Troubleshooting guide

**Use this directory**: When implementing features, check here for Wasp patterns and best practices.

### `ai/example-prompts-loanease.md`
**Structured Prompts for LLM Context**

Six reusable prompts designed to maintain context and guide feature implementation:

1. **System Context Prompt** - Establish project context
   - Use at the start of a session or when working on a new feature
   - Provides framework, architecture, and key rules
   - **Enforces three-role system**: Admin, Officer, Member

2. **Feature Implementation Prompt** - Implementation checklist
   - Use when implementing a new feature
   - Includes step-by-step checklist and pre-implementation checks

3. **Database Schema Prompt** - Design database schema
   - Use when designing or updating database schema
   - Includes entity definitions and Eloquent relationships

4. **RBAC & Security Prompt** - Implement role-based access control
   - Use when implementing authentication or authorization
   - **Includes only Admin, Officer, and Member roles**
   - Implementation steps and security practices

5. **Testing & Debugging Prompt** - Test and debug features
   - Use when testing or debugging features
   - Includes testing checklist and debugging tools

6. **Documentation Prompt** - Document features
   - Use when documenting features in `./ai/docs/`
   - Includes documentation structure and best practices

### `ai/PROJECT_STRUCTURE.md`
**Project Overview & Architecture**
- Project structure and directory layout
- Key entities and database design
- RBAC roles and permissions
- How to use the guidelines
- Key technologies and practices
- Next steps for implementation

## Quick Start

### For a New Feature:
1. Copy the **System Context Prompt** from `example-prompts.md`
2. Paste it into your LLM conversation
3. Use the **Feature Implementation Prompt** as a checklist
4. Reference `.cursorrules` for Laravel patterns
5. Check `./docs/` for related features

### For Database Changes:
1. Copy the **Database Schema Prompt** from `example-prompts.md`
2. Follow the schema requirements
3. Update models in `schema.prisma`
4. Run `wasp db migrate-dev` to create migration
5. Define Prisma relationships in models

### For RBAC Implementation:
1. Copy the **RBAC & Security Prompt** from `example-prompts.md`
2. Define UserRole enum in `schema.prisma` (ADMIN, OFFICER, MEMBER only)
3. Create authorization helpers in `src/lib/auth.ts`
4. Add role checks in server operations using `context.user.role`
5. **Never add roles beyond Admin, Officer, and Member**

### For Testing:
1. Copy the **Testing & Debugging Prompt** from `example-prompts.md`
2. Follow the testing checklist
3. Use Wasp development tools (`wasp db studio`, server logs)
4. Test with each user role: Admin, Officer, and Member

### For Documentation:
1. Copy the **Documentation Prompt** from `example-prompts.md`
2. Create a markdown file in `./docs/{feature}.md`
3. Follow the documentation structure
4. Point to implementation files rather than repeating code

## Project Context

**System**: LoanEase - Loan Management System
**Framework**: Laravel 12 (PHP 8+)
**Frontend**: Blade templates with Tailwind CSS
**Frontend Tooling**: Vite with Yarn/npm
**Database**: MySQL with Eloquent ORM
**Authentication**: Laravel Breeze or custom auth

**User Roles (ONLY these three):**
- **Admin**: Full system access, manage users, view all loans, reports, configuration
- **Officer**: Process applications, verify documents, approve/reject loans
- **Member**: Submit applications, upload documents, view own application status

**Key Features**:
- Loan application submission and tracking
- Loan application verification and approval (Officer/Admin)
- Document uploads and validation
- User management (Admin only)
- Payment tracking and processing
- Reporting and analytics (Admin/Officer)
- Audit logging

## Tips for LLM Usage

1. **Establish Context First**: Always start with the System Context Prompt
2. **Use Feature Checklists**: Reference the Feature Implementation Prompt for each feature
3. **Check Guidelines**: Always reference `.cursor/rules/` for patterns and best practices
4. **Reference Documentation**: Check `./ai/docs/` for related features to avoid duplication
5. **Follow Architecture**: Maintain Wasp's full-stack architecture (React → Server Operations → Prisma)
6. **Implement RBAC**: Always implement role-based access control in server operations
7. **Role Restriction**: **CRITICAL** - Only use Admin, Officer, and Member roles. Never add other roles.
8. **Log Sensitive Actions**: Use AuditLog for compliance and debugging
9. **Document as You Go**: Use the Documentation Prompt to document features

## Directory Structure

```
ai/
├── README.md                      # This file
├── example-prompts.md             # Structured prompts for LLM
├── Project Structure & Guidelines.md  # Project overview
└── docs/                          # Feature documentation
    ├── loan-application.md
    ├── verification.md
    ├── approval.md
    ├── payments.md
    ├── reporting.md
    └── ...
```

## Next Steps

1. Review `.cursor/rules/` for development standards
2. Review `example-prompts.md` for available prompts
3. Update `schema.prisma` with UserRole enum (ADMIN, OFFICER, MEMBER)
4. Run `wasp db migrate-dev` to apply schema changes
5. Start implementing features using the prompts
6. Document features in `./ai/docs/` as you go
7. Reference this README when onboarding new developers or LLMs
