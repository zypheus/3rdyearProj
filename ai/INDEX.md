# LoanEase Documentation Index

## 📍 Start Here

You're working on **LoanEase** - a Laravel 12 loan management system.

**Three roles ONLY:** Admin, Officer, Member

**Main prompt file:** `ai/example-prompts-loanease.md`

---

## 📚 Documentation Files Quick Navigation

### For Development

| File | Purpose | Read Time |
|------|---------|-----------|
| **`.cursor/rules/1-wasp-overview.mdc`** | Laravel 12 framework overview | 10 min |
| **`.cursor/rules/2-project-conventions.mdc`** | Code patterns and conventions | 10 min |
| **`.cursor/rules/4-authentication.mdc`** | Auth & RBAC patterns | 15 min |

### For LLM Prompts

| File | Purpose | Read Time |
|------|---------|-----------|
| **`ai/PROMPT_GUIDE.md`** ⭐ | How to use prompts effectively | 5 min |
| **`ai/example-prompts-loanease.md`** ⭐ | 6 optimized LLM prompts | Copy-paste ready |
| **`ai/README.md`** | Guides overview | 5 min |

### For Reference

| File | Purpose | Read Time |
|------|---------|-----------|
| **`ai/ROLE_BASED_ACCESS_CONTROL.md`** ⭐ | Complete RBAC guide & code examples | 20 min |
| **`ai/PROJECT_STRUCTURE.md`** | Architecture & project structure | 10 min |
| **`ai/example-plan.md`** | Implementation phases | 10 min |
| **`ai/example-PRD.md`** | Product requirements | 10 min |

### For Information

| File | Purpose | Read Time |
|------|---------|-----------|
| **`ai/COMPLETION_SUMMARY.md`** | What was updated | 10 min |
| **`ai/LOANEASE_UPDATE_SUMMARY.md`** | Detailed change list | 15 min |
| **`ai/PROMPT_GUIDE.md`** (this file) | How to use prompts | 10 min |

---

## 🚀 Getting Started (First Time)

### 1. Read (5 minutes)
```
→ ai/PROMPT_GUIDE.md (this file's overview)
```

### 2. Review (5 minutes)
```
→ ai/example-prompts-loanease.md (skim all 6 prompts)
```

### 3. Understand Roles (5 minutes)
```
→ ai/ROLE_BASED_ACCESS_CONTROL.md (read role definitions)
```

### 4. Ready to Develop
```
→ Copy System Context Prompt from ai/example-prompts-loanease.md
→ Paste into your LLM
→ Start implementing!
```

**Total Time: 15 minutes**

---

## 💻 For Different Tasks

### "I'm starting a new feature"
1. Copy **System Context Prompt** from `ai/example-prompts-loanease.md`
2. Use **Feature Implementation Prompt** as checklist
3. Reference `.cursor/rules/` for patterns
4. Ask LLM to implement based on prompts

### "I need to design a database table"
1. Read `ai/ROLE_BASED_ACCESS_CONTROL.md` (if it has roles)
2. Use **Database Schema Prompt** from `ai/example-prompts-loanease.md`
3. Reference `ai/PROJECT_STRUCTURE.md` for entity info

### "I'm adding authentication/roles"
1. Copy **System Context Prompt** (refresh context)
2. Use **RBAC & Security Prompt** from `ai/example-prompts-loanease.md`
3. Reference `ai/ROLE_BASED_ACCESS_CONTROL.md` for detailed examples

### "I'm testing a feature"
1. Use **Testing & Debugging Prompt** from `ai/example-prompts-loanease.md`
2. Test with all 3 roles: Admin, Officer, Member
3. Reference `.cursor/rules/4-authentication.mdc` if issues

### "I need to document a feature"
1. Use **Documentation Prompt** from `ai/example-prompts-loanease.md`
2. Reference `ai/PROJECT_STRUCTURE.md` for structure
3. Use `ai/example-plan.md` for feature context

### "I don't understand the architecture"
1. Read `ai/PROJECT_STRUCTURE.md`
2. Review `.cursor/rules/` files
3. Check `ai/example-plan.md` for phases

---

## 🎯 Critical Rules to Remember

### ✅ DO ALWAYS
- ✅ Use exactly 3 roles: **admin**, **officer**, **member**
- ✅ Use **Laravel 12** for backend
- ✅ Use **MySQL** for database
- ✅ Use **Blade + Tailwind** for frontend
- ✅ Use **Vite** for asset compilation
- ✅ Check roles in **middleware or controllers** (server-side)
- ✅ Log **sensitive actions** to AuditLog
- ✅ Test with **all 3 roles**

### ❌ NEVER
- ❌ Add roles like "LGU Verifier", "Employer", "Manager"
- ❌ Use Wasp, React, TypeScript, Prisma, PostgreSQL
- ❌ Trust client-side role checking
- ❌ Allow users to change their own role
- ❌ Skip authorization checks
- ❌ Hardcode permissions in controllers

---

## 🔍 File Dependency Map

```
ai/example-prompts-loanease.md (PRIMARY)
    ├── Needs: System Context first
    ├── Uses: .cursor/rules/ for patterns
    └── References: ROLE_BASED_ACCESS_CONTROL.md

.cursor/rules/
    ├── 1-wasp-overview.mdc (Laravel 12 concepts)
    ├── 2-project-conventions.mdc (Code patterns)
    └── 4-authentication.mdc (Auth patterns)

ai/ROLE_BASED_ACCESS_CONTROL.md
    ├── Defines: Admin, Officer, Member
    ├── Shows: Code examples for each role
    └── Provides: Database schema examples

ai/PROJECT_STRUCTURE.md
    ├── Shows: Directory layout
    ├── Defines: Database entities
    └── Explains: Implementation workflow

ai/PROMPT_GUIDE.md (this document)
    └── Explains: How to use all prompts
```

---

## 📊 Tech Stack at a Glance

```
┌─────────────────────────────────┐
│       LOANEASE SYSTEM           │
├─────────────────────────────────┤
│ Frontend:  Blade + Tailwind CSS │
│ Build:     Vite (yarn dev)      │
│ Backend:   Laravel 12 (PHP 8+)  │
│ Database:  MySQL + Eloquent     │
│ Auth:      Laravel Breeze       │
│ Roles:     Admin, Officer,      │
│            Member (exactly 3)   │
└─────────────────────────────────┘
```

---

## 🔗 Common Workflows

### Workflow 1: Add a New Feature
```
1. Copy System Context Prompt
2. Follow Feature Implementation Prompt
3. Database Schema Prompt (if needed)
4. RBAC & Security Prompt (if needed)
5. Testing & Debugging Prompt
6. Documentation Prompt
```

### Workflow 2: Debug an Issue
```
1. Check .cursor/rules/ for patterns
2. Review ROLE_BASED_ACCESS_CONTROL.md
3. Use Testing & Debugging Prompt
4. Ask LLM for specific pattern
```

### Workflow 3: Add a New Role (DON'T)
```
❌ DO NOT ADD NEW ROLES
→ Use existing: Admin, Officer, Member
→ Combine permissions as needed
```

### Workflow 4: Update Database
```
1. Use Database Schema Prompt
2. Create migration: php artisan make:migration
3. Update Eloquent relationships
4. Test with php artisan migrate
```

---

## 📝 File Checklist

Essential files to keep:
- [ ] `ai/example-prompts-loanease.md` ⭐ PRIMARY
- [ ] `ai/ROLE_BASED_ACCESS_CONTROL.md` ⭐ REFERENCE
- [ ] `.cursor/rules/` (all 3 files)
- [ ] `ai/PROJECT_STRUCTURE.md`
- [ ] `ai/README.md`

Optional but helpful:
- [ ] `ai/PROMPT_GUIDE.md` (this file)
- [ ] `ai/COMPLETION_SUMMARY.md`
- [ ] `ai/LOANEASE_UPDATE_SUMMARY.md`

---

## ❓ Troubleshooting

### "LLM is generating Wasp/React code"
**Solution:** Make sure System Context Prompt mentions Laravel 12

### "Role restrictions not working"
**Solution:** Check `.cursor/rules/4-authentication.mdc` or `ROLE_BASED_ACCESS_CONTROL.md`

### "Database structure is wrong"
**Solution:** Use Database Schema Prompt and review migration examples

### "Can't find where to implement something"
**Solution:** Check `PROJECT_STRUCTURE.md` for file locations

### "Need code examples"
**Solution:** See `.cursor/rules/` files or `ROLE_BASED_ACCESS_CONTROL.md`

---

## 📞 Quick Help Links

| Question | File |
|----------|------|
| What roles are there? | `ai/ROLE_BASED_ACCESS_CONTROL.md` |
| How do I implement X? | `ai/example-prompts-loanease.md` |
| Where does this go? | `.cursor/rules/` or `ai/PROJECT_STRUCTURE.md` |
| What patterns to use? | `.cursor/rules/` |
| How to use prompts? | `ai/PROMPT_GUIDE.md` |
| Need code examples? | `ai/ROLE_BASED_ACCESS_CONTROL.md` |
| What was changed? | `ai/COMPLETION_SUMMARY.md` |

---

## 🎓 Learning Path

**New to LoanEase?**
1. Read `ai/PROMPT_GUIDE.md` (10 min)
2. Skim all prompts in `ai/example-prompts-loanease.md` (5 min)
3. Read `ai/ROLE_BASED_ACCESS_CONTROL.md` for roles (15 min)
4. Ready to develop!

**First feature?**
1. Copy System Context Prompt
2. Copy Feature Implementation Prompt
3. Follow the checklist in the prompt
4. Use other prompts as needed

**Stuck?**
1. Check relevant `.cursor/rules/` file
2. See `ai/ROLE_BASED_ACCESS_CONTROL.md` for examples
3. Review `ai/PROJECT_STRUCTURE.md` for structure
4. Ask LLM with specific pattern reference

---

## 📅 Version Info

- **System Name:** LoanEase
- **Backend:** Laravel 12
- **Database:** MySQL
- **Roles:** Admin, Officer, Member (exactly 3)
- **Last Updated:** November 30, 2025
- **Status:** ✅ Ready for development

---

## 🎯 Remember

> **All you need is in `ai/example-prompts-loanease.md`**  
> Copy a prompt → Paste in LLM → Follow the checklist → Ship it!

**Three roles. Laravel 12. MySQL. Done.**

---

**Start with:** `ai/example-prompts-loanease.md`  
**Reference:** `.cursor/rules/`  
**Roles guide:** `ai/ROLE_BASED_ACCESS_CONTROL.md`  
**Prompts help:** `ai/PROMPT_GUIDE.md`  

🚀 Ready to build LoanEase!
