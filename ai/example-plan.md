# Application Management System Implementation Plan

This plan outlines the steps for building the application using a Milestone-Based approach combined with User Stories, suitable for LLM-assisted coding and a vertical slice methodology.

**CRITICAL**: This system uses **exactly three user roles**: Admin, Officer, and Member. No other roles should be added.

---

**Phase 1: Core Setup & User Management**

*   **Goal:** Establish user authentication with role-based access control using the three defined roles.
*   **Steps/User Stories:**
    *   - [x] **1.1. Auth Setup:** Configure and verify breeze built-in Auth (Username/Password). Ensure users can sign up, log in, and log out.
    *   - [x] **1.2. User Role Enum:** Create `UserRole` enum in `schema.prisma` with ADMIN, OFFICER, MEMBER values.
    *   - [x] **1.3. User Model with Role:** Update `User` entity in `schema.prisma` to include `role` field with default value of MEMBER.
    *   - [ ] **1.4. Run Migration:** Execute `wasp db migrate-dev` to apply schema changes.
    *   - [ ] **1.5. Authorization Helpers:** Create `src/lib/auth.ts` with helper functions:
        *   - [ ] `requireAdmin(user)` - Throws 403 if not admin
        *   - [ ] `requireOfficerOrAdmin(user)` - Throws 403 if not officer or admin
        *   - [ ] `requireAuthenticated(user)` - Throws 401 if not logged in
    *   - [ ] **1.6. User Management UI (Admin Only):** Create a `UsersPage` (`/users`) accessible only to Admins:
        *   - [ ] Display list of all users with their roles
        *   - [ ] Allow Admin to change user roles
        *   - [ ] Implement role update action with admin-only authorization

---

**Phase 2: Application Submission (Member)**

*   **Goal:** Allow Members to submit applications and track their status.
*   **Steps/User Stories:**
    *   - [ ] **2.1. Application Entity:** Define an `Application` entity in `schema.prisma` linked to `User`. Include fields: `title`, `description`, `status` (Enum: PENDING, APPROVED, REJECTED), `submittedAt`, `userId`.
    *   - [ ] **2.2. Application Status Enum:** Create `ApplicationStatus` enum in `schema.prisma`.
    *   - [ ] **2.3. Run Migration:** Execute `wasp db migrate-dev`.
    *   - [ ] **2.4. Application Operations:** Implement queries and actions in `src/features/applications/operations.ts`:
        *   - [ ] `createApplication(data)` - Members can create applications
        *   - [ ] `getMyApplications()` - Members see only their applications
        *   - [ ] `getAllApplications()` - Officers and Admins see all applications
    *   - [ ] **2.5. Application Form UI:** Create component for Members to submit applications.
    *   - [ ] **2.6. Application List UI:** Create list view with role-based filtering (Members see own, Officers/Admins see all).

---

**Phase 3: Application Review & Approval (Officer/Admin)**

*   **Goal:** Enable Officers and Admins to review and approve/reject applications.
*   **Steps/User Stories:**
    *   - [ ] **3.1. Review Actions:** Implement actions in `operations.ts`:
        *   - [ ] `approveApplication(applicationId)` - Officer/Admin only
        *   - [ ] `rejectApplication(applicationId, reason)` - Officer/Admin only
        *   - [ ] Add authorization checks using `requireOfficerOrAdmin`
    *   - [ ] **3.2. Application Details Page:** Create detailed view showing:
        *   - [ ] Application information
        *   - [ ] Approve/Reject buttons (Officer/Admin only)
        *   - [ ] Status history
    *   - [ ] **3.3. Update Application Model:** Add `reviewedBy` (userId), `reviewedAt`, `rejectionReason` fields.
    *   - [ ] **3.4. Run Migration:** Execute `wasp db migrate-dev`.

---

**Phase 4: Document Management**

*   **Goal:** Allow Members to upload documents; Officers/Admins to review them.
*   **Steps/User Stories:**
    *   - [ ] **4.1. Document Entity:** Create `Document` entity in `schema.prisma` linked to `Application` and `User`. Fields: `filename`, `fileUrl`, `uploadedAt`, `applicationId`, `userId`.
    *   - [ ] **4.2. Run Migration:** Execute `wasp db migrate-dev`.
    *   - [ ] **4.3. File Upload Setup:** Configure file upload (consider using cloud storage or local storage).
    *   - [ ] **4.4. Document Operations:** Implement in `src/features/documents/operations.ts`:
        *   - [ ] `uploadDocument(applicationId, file)` - Members upload to their applications
        *   - [ ] `getDocuments(applicationId)` - Get documents for an application (with authorization)
    *   - [ ] **4.5. Document Upload UI:** Add upload interface to application detail page.
    *   - [ ] **4.6. Document List UI:** Display uploaded documents with download links.

---

**Phase 5: Audit Logging & Reporting**

*   **Goal:** Track all sensitive actions and provide reporting capabilities.
*   **Steps/User Stories:**
    *   - [ ] **5.1. AuditLog Entity:** Create `AuditLog` entity in `schema.prisma`. Fields: `action`, `userId`, `entityType`, `entityId`, `details`, `createdAt`.
    *   - [ ] **5.2. Run Migration:** Execute `wasp db migrate-dev`.
    *   - [ ] **5.3. Logging Helper:** Create `src/lib/audit.ts` with function to log actions:
        *   - [ ] `logAction(userId, action, entityType, entityId, details)`
    *   - [ ] **5.4. Add Logging:** Update all sensitive operations (approve, reject, role changes) to log actions.
    *   - [ ] **5.5. Audit Log Viewer (Admin Only):** Create page to view audit logs:
        *   - [ ] Filter by user, action, date range
        *   - [ ] Display in table format
    *   - [ ] **5.6. Reports (Admin/Officer):** Create basic reporting:
        *   - [ ] Applications by status
        *   - [ ] Applications by date range
        *   - [ ] User activity summary

---

**Phase 6: Security Review & Refinement**

*   **Goal:** Review the implemented features for potential security vulnerabilities and ensure proper role enforcement.
*   **Areas of Focus:**
    *   **Authorization Checks:**
        *   - [ ] Verify that *all* relevant Wasp Operations check `context.user.role` correctly
        *   - [ ] Ensure only Admin, Officer, and Member roles are used (no other roles)
        *   - [ ] Double-check that Members can only access their own data
        *   - [ ] Verify Officers/Admins can access appropriate data
        *   - [ ] Test edge cases: Can users manipulate IDs to access others' data?
    *   **Input Validation:**
        *   - [ ] Review server-side validation in all actions
        *   - [ ] Validate strings, numbers, IDs, file uploads
        *   - [ ] Use TypeScript types for compile-time safety
    *   **Data Exposure:**
        *   - [ ] Confirm queries return only necessary data
        *   - [ ] Ensure error messages don't leak sensitive information
        *   - [ ] Remove any unnecessary data from API responses
    *   **Role Enforcement:**
        *   - [ ] Verify UserRole enum has only ADMIN, OFFICER, MEMBER
        *   - [ ] Check all authorization helpers use correct roles
        *   - [ ] Ensure UI elements are hidden/shown based on roles
    *   **Authentication:**
        *   - [ ] Review Wasp auth configuration in `main.wasp`
        *   - [ ] Ensure environment variables are secure
        *   - [ ] Test authentication flows thoroughly
    *   **Dependency Review:**
        *   - [ ] Run `npm audit` to check for vulnerabilities
        *   - [ ] Update dependencies if needed

*   **Outcome:** Identify and address any security gaps found during the review.

---

**Key Reminders:**
- **Only use three roles**: Admin, Officer, Member
- Always implement authorization in server operations, not just UI
- Log all sensitive actions to AuditLog
- Test with each role after implementing features
- Document role requirements for each feature