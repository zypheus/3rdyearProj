# LoanEase: A Modern Loan Management System PRD

## Overview

Create **LoanEase**, a modern and streamlined loan management system using Laravel 12 and a Vite-powered frontend. The application will provide a comprehensive platform for members to apply for loans, officers to process applications, and administrators to manage the system. The focus is on building a secure, scalable, and user-friendly application suitable for managing the entire loan lifecycle.

## Core Features

1.  **Role-Based Access Control (RBAC):**
    *   **Admin:** Manages users, roles, system settings, and has full oversight.
    *   **Officer:** Processes loan applications, verifies documents, and communicates with members.
    *   **Member:** Submits loan applications, uploads documents, and tracks their application status.

2.  **Loan Application Submission:** Members can fill out and submit detailed loan applications through a multi-step form.

3.  **Application Processing & Management:** Officers can view, review, approve, or reject loan applications through a dedicated dashboard.

4.  **Document Management:** Members can upload necessary documents (e.g., proof of income, identification) securely. Officers can review and verify these documents.

5.  **Admin Dashboard:** A comprehensive dashboard for Admins to manage users, view system-wide statistics, and configure application settings.

6.  **Reporting:** Admins and Officers can generate reports on loan statuses, processing times, and other key metrics.

## Implementation Guidelines

*   **Backend:** Laravel 12 (PHP) for the core application logic and API.
*   **Database:** MySQL.
*   **Frontend:** Laravel Blade templates for server-rendered views, with Vite for compiling frontend assets (CSS, JS).
*   **Styling & Structure:** Utilize Tailwind CSS for styling, following a feature-based structure within the Laravel project.
*   **Development Approach:** Employ a vertical slice implementation strategy. Start with the most basic, end-to-end version of a feature (e.g., a simple loan application form) and incrementally add complexity and use this directory template.this approach is well-suited for LLM-assisted coding.
*   **Scope:** Focus on the core loan management lifecycle from application to decision.

## Tech Stack Summary

*   **Backend Framework:** Laravel 12 (PHP)
*   **Database:** MySQL
*   **Frontend Tooling:** Vite, managed via Yarn/npm.
*   **Asset Pipeline:** Dependencies managed in `package.json` (e.g., Tailwind CSS, Alpine.js, Iconify).
*   **Web Server:** Apache or Nginx, with configuration pointing to the `public` directory.
*   **Development Workflow:**
    *   `php artisan serve` to run the Laravel development server.
    *   `yarn dev` or `npm run dev` to run the Vite dev server for Hot Module Replacement (HMR).
    *   `php artisan migrate` to manage database schema migrations.