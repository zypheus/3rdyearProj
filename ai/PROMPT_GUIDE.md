# LoanEase - LLM Prompt Guide

## Quick Start: Which Prompt to Use?

```
Start of Session?
    ↓
YES → Use SYSTEM CONTEXT PROMPT
    • Sets up project context
    • Establishes Laravel 12 + MySQL
    • Defines Admin/Officer/Member roles
    • 5-10 min to review


Implementing a New Feature?
    ↓
YES → Use FEATURE IMPLEMENTATION PROMPT
    • Step-by-step checklist
    • Models, migrations, controllers
    • Form requests, views, routes
    • 15-20 min per feature


Designing Database Schema?
    ↓
YES → Use DATABASE SCHEMA PROMPT
    • Entity definitions
    • Eloquent relationships
    • Migration examples
    • 10-15 min


Setting Up Authentication/Roles?
    ↓
YES → Use RBAC & SECURITY PROMPT
    • Role definitions
    • Middleware setup
    • Authorization checks
    • 10-15 min


Testing/Debugging Features?
    ↓
YES → Use TESTING & DEBUGGING PROMPT
    • Testing checklist
    • Debugging tools
    • Common issues
    • 10-20 min


Documenting a Feature?
    ↓
YES → Use DOCUMENTATION PROMPT
    • Documentation structure
    • File references
    • Role requirements
    • 10-15 min
```

---

## The Six Prompts Explained

### 1️⃣ SYSTEM CONTEXT PROMPT
**When to Use:** Start of every LLM session or when working on new feature  
**Time Required:** 5-10 minutes  
**Includes:**
- Project name: LoanEase
- Framework: Laravel 12 (PHP 8+)
- Database: MySQL with Eloquent
- Frontend: Blade + Tailwind
- Build Tool: Vite
- Roles: Admin, Officer, Member (exactly 3)
- Architecture: MVC pattern
- Key rules and constraints

**Why Use It:** Ensures LLM understands the complete context for accurate code generation

---

### 2️⃣ FEATURE IMPLEMENTATION PROMPT
**When to Use:** When starting work on a new feature  
**Time Required:** 15-20 minutes per feature  
**Includes:**
- Feature requirements checklist
- Step-by-step implementation checklist
- Required role specification
- Files to create/update
- Pre-implementation checks
- Best practices

**Why Use It:** Provides structured approach to feature implementation

**Example Features:**
- Loan application submission
- Document upload & verification
- Loan approval workflow
- User management (Admin)
- Payment processing

---

### 3️⃣ DATABASE SCHEMA PROMPT
**When to Use:** When designing database schema or adding new entities  
**Time Required:** 10-15 minutes  
**Includes:**
- Key entities definition
- Schema requirements
- Naming conventions
- Relationships
- Migration examples
- Example SQL/code

**Why Use It:** Ensures proper database design and relationships

**Examples:**
- Loan table relationships
- Document upload tracking
- Payment tracking
- User roles storage
- Audit logging schema

---

### 4️⃣ RBAC & SECURITY PROMPT
**When to Use:** When implementing authentication, authorization, or role-based features  
**Time Required:** 10-15 minutes  
**Includes:**
- Role definitions (Admin/Officer/Member)
- Implementation steps
- Middleware code
- Authorization helpers
- Security best practices
- Code examples

**Why Use It:** Ensures consistent and secure role-based access control

**Implementation Covers:**
- User model with roles
- Middleware for role checking
- Authorization services
- Route protection
- Controller-level checks
- Logging sensitive actions

---

### 5️⃣ TESTING & DEBUGGING PROMPT
**When to Use:** When testing features or debugging issues  
**Time Required:** 10-20 minutes  
**Includes:**
- Testing checklist
- Testing with all three roles
- Debugging tools
- Common issues and solutions
- Laravel-specific debugging
- Vite HMR debugging

**Why Use It:** Comprehensive testing approach for all roles

**Covers:**
- Unit testing
- Integration testing
- Role-based testing
- Database verification
- Frontend/asset testing
- Performance testing

---

### 6️⃣ DOCUMENTATION PROMPT
**When to Use:** When documenting completed features  
**Time Required:** 10-15 minutes  
**Includes:**
- Documentation structure
- What to document
- What not to repeat
- File organization
- Code examples
- Role requirements documentation

**Why Use It:** Consistent documentation across features

**Documents:**
- Feature overview
- User workflows
- Business logic
- Implementation files
- Role requirements
- Validation rules

---

## Usage Pattern

### Typical Development Day

```
Morning Start
    ↓
1. Copy SYSTEM CONTEXT PROMPT into LLM
   (Skip if already done in session)
    ↓
Implementing Feature
    ↓
2. Use FEATURE IMPLEMENTATION PROMPT
    ↓
3. Need database work?
   → Use DATABASE SCHEMA PROMPT
    ↓
4. Need auth work?
   → Use RBAC & SECURITY PROMPT
    ↓
After Implementation
    ↓
5. Use TESTING & DEBUGGING PROMPT
    ↓
6. Use DOCUMENTATION PROMPT
    ↓
Done!
```

---

## Prompt File Locations

**Main Prompts File:**
```
ai/example-prompts-loanease.md
```

**Reference Documents:**
```
ai/PROJECT_STRUCTURE.md          (Architecture overview)
ai/ROLE_BASED_ACCESS_CONTROL.md  (Role definitions & examples)
.cursor/rules/                   (Development guidelines)
```

---

## Copy-Paste Instructions

### To Use a Prompt:

1. **Open the prompts file:**
   ```
   ai/example-prompts-loanease.md
   ```

2. **Find the prompt you need:**
   - System Context Prompt
   - Feature Implementation Prompt
   - Database Schema Prompt
   - RBAC & Security Prompt
   - Testing & Debugging Prompt
   - Documentation Prompt

3. **Copy the entire prompt** (from `>` to end of section)

4. **Paste into LLM** (ChatGPT, Claude, etc.)

5. **Add your specific details** (feature name, requirements, etc.)

6. **Execute the prompt**

---

## Pro Tips

### Tip 1: Multi-Prompt Sessions
When implementing a complex feature:
1. Use SYSTEM CONTEXT PROMPT first
2. Use FEATURE IMPLEMENTATION PROMPT
3. When adding DB: Use DATABASE SCHEMA PROMPT
4. When adding auth: Use RBAC & SECURITY PROMPT
5. When testing: Use TESTING & DEBUGGING PROMPT
6. When documenting: Use DOCUMENTATION PROMPT

### Tip 2: Bookmark Prompts
Copy prompts you use frequently to:
- Notepad/Doc
- Keyboard shortcuts
- IDE snippets
- LLM bookmarks (if available)

### Tip 3: Customize Context
The System Context Prompt can be customized:
- Add specific requirements
- Add team guidelines
- Add project-specific rules
- Add deadline information

### Tip 4: Combine with Guidelines
Always reference these alongside prompts:
- `.cursor/rules/` for Laravel patterns
- `ROLE_BASED_ACCESS_CONTROL.md` for role definitions
- `PROJECT_STRUCTURE.md` for architecture
- `example-plan.md` for feature list

### Tip 5: Iterate Quickly
If LLM response isn't quite right:
1. Specify what needs to change
2. Point to relevant prompt sections
3. Reference `.cursor/rules/` for patterns
4. Ask for revised code

---

## Prompt Customization Examples

### For Database Features:
```
Start with: SYSTEM CONTEXT PROMPT
Then add: DATABASE SCHEMA PROMPT
Customize: "I need to add a [Entity] table with these fields: [list]"
```

### For Authentication:
```
Start with: SYSTEM CONTEXT PROMPT
Then add: RBAC & SECURITY PROMPT
Customize: "I need [Role] users to be able to [Action]"
```

### For Admin Features:
```
Start with: SYSTEM CONTEXT PROMPT
Then add: FEATURE IMPLEMENTATION PROMPT
Customize: "This feature is for [ADMIN/OFFICER/MEMBER] role only"
```

### For Complex Features:
```
1. SYSTEM CONTEXT PROMPT
2. FEATURE IMPLEMENTATION PROMPT
3. DATABASE SCHEMA PROMPT (if DB changes needed)
4. RBAC & SECURITY PROMPT (if auth changes needed)
5. TESTING & DEBUGGING PROMPT
6. DOCUMENTATION PROMPT
```

---

## Checklists for Each Prompt

### ✅ Before Using System Context Prompt:
- [ ] Review the prompt once
- [ ] Make note of roles (Admin, Officer, Member)
- [ ] Note tech stack (Laravel 12, MySQL, Vite)
- [ ] Ready to copy-paste

### ✅ Before Using Feature Implementation Prompt:
- [ ] Know the feature name
- [ ] List requirements
- [ ] Know required roles
- [ ] Have existing code reviewed (if updating)

### ✅ Before Using Database Schema Prompt:
- [ ] List entities needed
- [ ] Know relationships
- [ ] Know fields required
- [ ] Review MySQL best practices

### ✅ Before Using RBAC & Security Prompt:
- [ ] Know which roles need access
- [ ] Know what each role can do
- [ ] Review existing auth code
- [ ] Have middleware examples ready

### ✅ Before Using Testing & Debugging Prompt:
- [ ] Feature implemented
- [ ] Code ready for testing
- [ ] Test scenarios identified
- [ ] Debugging tools available

### ✅ Before Using Documentation Prompt:
- [ ] Feature completely implemented
- [ ] Code reviewed
- [ ] Testing complete
- [ ] File structure known

---

## Quick Reference: Prompt Purposes

| Prompt | Purpose | Audience |
|--------|---------|----------|
| System Context | Establish full project context | All users |
| Feature Implementation | Step-by-step feature checklist | Developers |
| Database Schema | Design database structure | Backend developers |
| RBAC & Security | Role-based access control | Security-focused devs |
| Testing & Debugging | QA and troubleshooting | QA engineers, Debuggers |
| Documentation | Record features | Technical writers, Architects |

---

## Emergency: What If Something Is Wrong?

If LLM generates incorrect code:

1. **Check the System Context Prompt was used** - Ensure it mentions Laravel 12, not Wasp
2. **Check role restrictions** - Verify only Admin, Officer, Member used
3. **Reference `.cursor/rules/`** - Point to specific Laravel pattern
4. **Use RBAC & Security Prompt** - If it's an authorization issue
5. **Ask for specific file** - Point to existing implementation
6. **Review ROLE_BASED_ACCESS_CONTROL.md** - Get correct examples

---

## Support

**Questions about prompts?**
- See: `ai/COMPLETION_SUMMARY.md`
- See: `ai/README.md`

**Questions about roles?**
- See: `ai/ROLE_BASED_ACCESS_CONTROL.md`

**Questions about architecture?**
- See: `ai/PROJECT_STRUCTURE.md`

**Questions about patterns?**
- See: `.cursor/rules/`

---

**Remember:** Always start with the System Context Prompt. It sets up everything correctly! 🚀
