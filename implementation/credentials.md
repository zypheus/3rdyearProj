# LoanEase Mock Credentials

This document contains all mock credentials for testing the LoanEase application.

> ⚠️ **Warning**: These are development/testing credentials only. Do not use in production.

---

## Default Test Users

These users are created by the `DatabaseSeeder`:

| Role    | Email                      | Password   | Description                          |
|---------|----------------------------|------------|--------------------------------------|
| Admin   | admin@loanease.test        | password   | Full system access                   |
| Officer | officer@loanease.test      | password   | Loan processing and verification     |
| Member  | member@loanease.test       | password   | Loan application and tracking        |

---

## Active Loan Test User

Created by `ActiveLoanUserSeeder` for testing loan workflows with complete data:

| Role   | Email                       | Password   | Description                              |
|--------|----------------------------|------------|------------------------------------------|
| Member | juan.delacruz@example.com  | password   | Member with active loan and payment data |

---

## User Roles & Permissions

### Admin
- Full system access
- User management
- View all loans and payments
- Approve/reject loans
- Generate reports
- Access audit logs

### Officer
- View all loan applications
- Review and process loans
- Approve/reject loans
- Record disbursements
- Confirm payment schedules
- View member information

### Member
- Apply for loans
- View own loans
- Make payments
- View payment schedule
- Upload documents

---

## How to Seed the Database

```bash
# Run all seeders
php artisan db:seed

# Run specific seeder
php artisan db:seed --class=DatabaseSeeder
php artisan db:seed --class=ActiveLoanUserSeeder

# Fresh migration with seeding
php artisan migrate:fresh --seed
```

---

## Database Connection

Default development database configuration (from `.env`):

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=loanease
DB_USERNAME=root
DB_PASSWORD=
```

---

## API/Application URLs

| Environment | URL                          |
|-------------|------------------------------|
| Development | http://localhost:8000        |
| Vite Dev    | http://localhost:5173        |

---

## Notes

1. All seeded users have email verified (`email_verified_at` is set)
2. Additional 5 random member users are created by `DatabaseSeeder`
3. The `ActiveLoanUserSeeder` creates a member with:
   - An approved and active loan
   - Payment schedule with some paid entries
   - Sample documents
   - Disbursement record

---

*Last Updated: December 4, 2025*
