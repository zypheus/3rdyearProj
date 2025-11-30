# LoanEase - Product Requirements Document (PRD)

**Version:** 1.0  
**Date:** November 30, 2025  
**System:** LoanEase - Loan Management System  
**Framework:** Laravel 12

---

## 1. Executive Summary

LoanEase is a comprehensive loan management system designed to streamline the loan application, verification, approval, and payment tracking processes. Built on Laravel 12 with a modern tech stack, the system implements role-based access control with exactly three user roles: Admin, Officer, and Member.

### 1.1 Project Goals
- Provide a secure, user-friendly platform for loan management
- Implement streamlined loan application workflow
- Enable document verification and approval processes
- Generate comprehensive reports for decision-making
- Ensure compliance through audit logging

### 1.2 Target Users
| Role | Description | Primary Activities |
|------|-------------|-------------------|
| **Admin** | System administrators | User management, reporting, system configuration |
| **Officer** | Loan officers/processors | Application review, document verification, approvals |
| **Member** | Loan applicants | Application submission, document upload, status tracking |

---

## 2. Technical Specifications

### 2.1 Technology Stack

| Component | Technology | Version |
|-----------|------------|---------|
| Backend Framework | Laravel | 12.x |
| Programming Language | PHP | 8.2+ |
| Database | MySQL | 8.0+ |
| ORM | Eloquent | - |
| Frontend Templates | Blade | - |
| CSS Framework | Tailwind CSS | 3.x |
| Build Tool | Vite | 5.x |
| Package Managers | Composer (PHP), Yarn (JS) | - |
| Authentication | Laravel Breeze | - |

### 2.2 Architecture Pattern

```
┌────────────────────────────────────────────────────────────────┐
│                     PRESENTATION LAYER                         │
│              (Blade Templates + Tailwind CSS)                  │
├────────────────────────────────────────────────────────────────┤
│                     APPLICATION LAYER                          │
│     ┌─────────────┐  ┌─────────────┐  ┌─────────────┐         │
│     │ Controllers │  │  Services   │  │ Middleware  │         │
│     └─────────────┘  └─────────────┘  └─────────────┘         │
│     ┌─────────────┐  ┌─────────────┐  ┌─────────────┐         │
│     │   Models    │  │  Policies   │  │  Requests   │         │
│     └─────────────┘  └─────────────┘  └─────────────┘         │
├────────────────────────────────────────────────────────────────┤
│                       DATABASE LAYER                           │
│                    (MySQL + Eloquent ORM)                      │
└────────────────────────────────────────────────────────────────┘
```

---

## 3. Role-Based Access Control (RBAC)

### 3.1 Role Definitions

> **CRITICAL: Only these three roles are allowed. No additional roles should ever be created.**

#### Admin Role
- **Database Value:** `admin`
- **Access Level:** Full system access
- **Permissions:**
  - ✅ Manage all users (create, read, update, delete)
  - ✅ Assign and modify user roles
  - ✅ View all loan applications
  - ✅ Access all system reports
  - ✅ Configure system settings
  - ✅ View audit logs
  - ✅ Manage loan types and policies

#### Officer Role
- **Database Value:** `officer`
- **Access Level:** Loan processing and verification
- **Permissions:**
  - ✅ View assigned loan applications
  - ✅ Review and verify documents
  - ✅ Approve or reject loan applications
  - ✅ Add comments and notes
  - ✅ View payment history
  - ✅ Generate processing reports
  - ❌ Cannot manage users
  - ❌ Cannot access system settings

#### Member Role
- **Database Value:** `member`
- **Access Level:** Self-service loan management
- **Permissions:**
  - ✅ Submit loan applications
  - ✅ Upload required documents
  - ✅ View own application status
  - ✅ View own payment history
  - ✅ Update own profile
  - ❌ Cannot view other members' data
  - ❌ Cannot approve/reject applications
  - ❌ Cannot access reports

---

## 4. Database Schema

### 4.1 Entity Relationship Diagram

```
┌─────────────┐       ┌─────────────┐       ┌─────────────┐
│    User     │───────│    Loan     │───────│   Payment   │
│             │ 1   N │             │ 1   N │             │
└─────────────┘       └─────────────┘       └─────────────┘
      │                     │
      │                     │
      │ 1                   │ 1
      │ N                   │ N
┌─────────────┐       ┌─────────────┐
│  AuditLog   │       │  Document   │
│             │       │             │
└─────────────┘       └─────────────┘
```

### 4.2 Core Tables

#### Users Table
```sql
CREATE TABLE users (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'officer', 'member') DEFAULT 'member',
    phone VARCHAR(20) NULL,
    address TEXT NULL,
    profile_photo VARCHAR(255) NULL,
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

#### Loans Table
```sql
CREATE TABLE loans (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    loan_type VARCHAR(100) NOT NULL,
    amount DECIMAL(15,2) NOT NULL,
    term_months INT NOT NULL,
    interest_rate DECIMAL(5,2) NOT NULL,
    purpose TEXT NULL,
    status ENUM('pending', 'under_review', 'approved', 'rejected', 'active', 'completed', 'defaulted') DEFAULT 'pending',
    reviewed_by BIGINT UNSIGNED NULL,
    reviewed_at TIMESTAMP NULL,
    rejection_reason TEXT NULL,
    approved_amount DECIMAL(15,2) NULL,
    disbursement_date DATE NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (reviewed_by) REFERENCES users(id) ON DELETE SET NULL
);
```

#### Documents Table
```sql
CREATE TABLE documents (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    loan_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    document_type VARCHAR(100) NOT NULL,
    filename VARCHAR(255) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    file_size INT NOT NULL,
    mime_type VARCHAR(100) NOT NULL,
    is_verified BOOLEAN DEFAULT FALSE,
    verified_by BIGINT UNSIGNED NULL,
    verified_at TIMESTAMP NULL,
    notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (loan_id) REFERENCES loans(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (verified_by) REFERENCES users(id) ON DELETE SET NULL
);
```

#### Payments Table
```sql
CREATE TABLE payments (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    loan_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    amount DECIMAL(15,2) NOT NULL,
    principal DECIMAL(15,2) NOT NULL,
    interest DECIMAL(15,2) NOT NULL,
    payment_date DATE NOT NULL,
    due_date DATE NOT NULL,
    status ENUM('pending', 'paid', 'overdue', 'partial') DEFAULT 'pending',
    payment_method VARCHAR(50) NULL,
    reference_number VARCHAR(100) NULL,
    notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (loan_id) REFERENCES loans(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

#### Audit Logs Table
```sql
CREATE TABLE audit_logs (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NULL,
    action VARCHAR(100) NOT NULL,
    entity_type VARCHAR(100) NOT NULL,
    entity_id BIGINT UNSIGNED NULL,
    old_values JSON NULL,
    new_values JSON NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);
```

---

## 5. Feature Specifications

### 5.1 Authentication System

#### 5.1.1 Registration
- New users register with: name, email, password
- Default role assigned: `member`
- Email verification required (optional)
- Password requirements: min 8 characters, mixed case, numbers

#### 5.1.2 Login
- Email and password authentication
- "Remember me" functionality
- Session management with Laravel
- Rate limiting on failed attempts

#### 5.1.3 Password Reset
- Email-based password reset
- Token expiration (60 minutes)
- One-time use tokens

### 5.2 Dashboard Features

#### 5.2.1 Admin Dashboard
| Widget | Description |
|--------|-------------|
| User Statistics | Total users by role |
| Loan Overview | Applications by status |
| Recent Activity | Latest audit log entries |
| Quick Actions | Links to common tasks |
| Financial Summary | Total disbursed, collected |

#### 5.2.2 Officer Dashboard
| Widget | Description |
|--------|-------------|
| Pending Reviews | Count of applications awaiting review |
| Assigned Applications | List of assigned loans |
| Recent Decisions | Recent approvals/rejections |
| Document Verification | Pending document reviews |

#### 5.2.3 Member Dashboard
| Widget | Description |
|--------|-------------|
| Application Status | Current application status |
| Payment Schedule | Upcoming payments |
| Quick Apply | New application button |
| Document Status | Status of uploaded documents |

### 5.3 Loan Application Module

#### 5.3.1 Application Form (Multi-Step)
**Step 1: Personal Information**
- Full name (auto-filled from profile)
- Contact information
- Address details
- Employment information

**Step 2: Loan Details**
- Loan type selection
- Requested amount
- Preferred term (months)
- Purpose of loan

**Step 3: Document Upload**
- Valid ID
- Proof of income
- Additional supporting documents

**Step 4: Review & Submit**
- Summary of all entered information
- Terms and conditions acceptance
- Submit button

#### 5.3.2 Loan Status Workflow
```
[pending] → [under_review] → [approved] → [active] → [completed]
                          ↘ [rejected]           ↘ [defaulted]
```

### 5.4 Document Management

#### 5.4.1 Supported Document Types
- PDF files
- Images (JPEG, PNG)
- Maximum file size: 10MB per file

#### 5.4.2 Document Categories
- Valid ID (government-issued)
- Proof of Income
- Employment Certificate
- Bank Statements
- Collateral Documents
- Other Supporting Documents

#### 5.4.3 Verification Workflow
1. Member uploads document
2. Officer reviews document
3. Officer marks as verified/rejected
4. Notes added for rejected documents
5. Member notified of status

### 5.5 Payment Tracking

#### 5.5.1 Payment Schedule Generation
- Automatic schedule creation upon loan approval
- Monthly payment calculation with interest
- Amortization schedule display

#### 5.5.2 Payment Recording
- Officer/Admin records payments
- Payment methods: Cash, Bank Transfer, Check
- Reference number tracking
- Partial payment support

#### 5.5.3 Payment Status
- **Pending**: Due date approaching
- **Paid**: Payment received
- **Overdue**: Past due date
- **Partial**: Incomplete payment

### 5.6 Reporting Module

#### 5.6.1 Available Reports

| Report | Access | Description |
|--------|--------|-------------|
| Loan Summary | Admin, Officer | Overview of all loans by status |
| Payment Collection | Admin | Payments received by date range |
| Delinquency Report | Admin, Officer | Overdue payments list |
| User Activity | Admin | User login and action history |
| Application Processing | Admin | Average processing times |
| Monthly Summary | Admin | Monthly loan and payment summary |

#### 5.6.2 Export Formats
- PDF (for printing)
- Excel/CSV (for analysis)
- On-screen display

---

## 6. User Interface Specifications

### 6.1 Design Principles
- Clean, professional appearance
- Responsive design (mobile-first)
- Consistent navigation patterns
- Clear feedback for user actions
- Accessible (WCAG 2.1 AA compliance)

### 6.2 Color Scheme
```
Primary:    #3B82F6 (Blue)
Secondary:  #10B981 (Green)
Warning:    #F59E0B (Yellow)
Danger:     #EF4444 (Red)
Neutral:    #6B7280 (Gray)
Background: #F3F4F6 (Light Gray)
```

### 6.3 Layout Structure
```
┌────────────────────────────────────────────────────────┐
│                       Header                           │
│  [Logo] [Navigation]              [User Menu] [Logout] │
├─────────────┬──────────────────────────────────────────┤
│             │                                          │
│   Sidebar   │              Main Content                │
│             │                                          │
│  - Dashboard│                                          │
│  - Loans    │                                          │
│  - Payments │                                          │
│  - Reports  │                                          │
│  - Users    │                                          │
│  - Settings │                                          │
│             │                                          │
├─────────────┴──────────────────────────────────────────┤
│                       Footer                           │
└────────────────────────────────────────────────────────┘
```

### 6.4 Key Pages

| Page | Route | Access |
|------|-------|--------|
| Login | `/login` | Public |
| Register | `/register` | Public |
| Dashboard | `/dashboard` | All authenticated |
| Loans List | `/loans` | All authenticated |
| Loan Details | `/loans/{id}` | Owner/Officer/Admin |
| New Application | `/loans/create` | Member |
| Users Management | `/users` | Admin |
| Reports | `/reports` | Admin/Officer |
| Settings | `/settings` | Admin |
| Profile | `/profile` | All authenticated |

---

## 7. Security Requirements

### 7.1 Authentication Security
- [x] Password hashing using bcrypt
- [x] CSRF protection on all forms
- [x] Session security (secure cookies, HTTP-only)
- [x] Rate limiting on login attempts
- [x] Password reset token expiration

### 7.2 Authorization Security
- [x] Server-side role verification (never trust client)
- [x] Middleware-based route protection
- [x] Policy-based resource authorization
- [x] Data scoping based on user role

### 7.3 Data Security
- [x] Input validation on all forms
- [x] SQL injection prevention (Eloquent ORM)
- [x] XSS prevention (Blade escaping)
- [x] File upload validation
- [x] Sensitive data encryption

### 7.4 Audit & Compliance
- [x] All sensitive actions logged
- [x] User activity tracking
- [x] IP address logging
- [x] Data retention policies

---

## 8. Non-Functional Requirements

### 8.1 Performance
- Page load time: < 3 seconds
- API response time: < 500ms
- Database query optimization
- Asset caching and minification

### 8.2 Scalability
- Horizontal scaling support
- Database connection pooling
- Session management (database/Redis)
- File storage (local/cloud)

### 8.3 Reliability
- 99.5% uptime target
- Daily database backups
- Error logging and monitoring
- Graceful error handling

### 8.4 Maintainability
- Clean code architecture
- Comprehensive documentation
- Consistent coding standards
- Version control (Git)

---

## 9. Success Metrics

### 9.1 Key Performance Indicators (KPIs)

| Metric | Target | Measurement |
|--------|--------|-------------|
| Application Processing Time | < 48 hours | Average time from submission to decision |
| User Satisfaction | > 4.0/5.0 | User feedback ratings |
| System Uptime | > 99.5% | Monitoring logs |
| Document Verification Time | < 24 hours | Average verification time |
| Payment Collection Rate | > 95% | On-time payments / Total due |

### 9.2 Acceptance Criteria
- [ ] All three roles can perform their designated functions
- [ ] Loan application workflow completes end-to-end
- [ ] Document upload and verification works correctly
- [ ] Payment tracking accurately records transactions
- [ ] Reports generate correct data
- [ ] Audit logs capture all sensitive actions
- [ ] System passes security review

---

## 10. Appendix

### 10.1 Glossary

| Term | Definition |
|------|------------|
| RBAC | Role-Based Access Control |
| ORM | Object-Relational Mapping |
| CSRF | Cross-Site Request Forgery |
| XSS | Cross-Site Scripting |
| HMR | Hot Module Replacement |

### 10.2 References
- Laravel Documentation: https://laravel.com/docs
- Tailwind CSS: https://tailwindcss.com/docs
- Vite: https://vitejs.dev/guide/

### 10.3 Document History

| Version | Date | Author | Changes |
|---------|------|--------|---------|
| 1.0 | 2025-11-30 | Development Team | Initial PRD |

---

**Document Status:** ✅ Approved for Development

**Next Steps:**
1. Review and approve PRD
2. Set up development environment
3. Begin Phase 1 implementation
4. Conduct regular sprint reviews
