# TaskFlow — Role-Based Project & Task Management

TaskFlow is a comprehensive enterprise project and task management system built with Laravel 12. It features robust Role-Based Access Control (RBAC), JWT authentication, activity logging, automated deadline notifications, and a responsive dark mode Shadcn-inspired UI.

## 🚀 Features

- **JWT API Authentication** (`tymon/jwt-auth`)
- **Granular RBAC** (`spatie/laravel-permission`)
  - **Admin**: Full system access, employee management, overall reports.
  - **Project Manager**: Manage assigned projects, create tasks, assign employees, view project progress.
  - **Employee**: View assigned tasks, update status, submit work logs with file attachments.
- **Audit & Activity Logging** (`spatie/laravel-activitylog`) — tracks all entity changes.
- **Automated Notifications** (Queue & Scheduler)
  - Reminders sent to employees 48h, 24h, 12h, and 1h before a task deadline.
  - Overdue alerts sent to both the Employee and the Project Manager.
- **Work Logs** — Employees can log hours and attach files; PMs can reply to these logs.
- **Reporting** — Automated completion percentages and employee productivity metrics.
- **Modern UI** — Laravel Breeze Blade frontend with Tailwind CSS v4, dark mode, and Shadcn-inspired components.

## 🛠️ Tech Stack

- **Backend Framework**: Laravel 12.x
- **Frontend**: Blade, Alpine.js, Tailwind CSS v4
- **Database**: SQLite (default for dev), MySQL/PostgreSQL ready
- **Authentication**: `tymon/jwt-auth`
- **Authorization**: `spatie/laravel-permission`
- **Audit Trails**: `spatie/laravel-activitylog`

## 📦 Setup & Installation

1. **Clone the Repository**
   ```bash
   git clone https://github.com/abushahmaa/TaskFlow
   cd TaskFlow
   ```
   *Requires PHP 8.2+ and Laravel 12*

2. **Install Dependencies**
   ```bash
   composer install
   npm install
   ```

2. **Environment Setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   php artisan jwt:secret
   ```

3. **Database & Seeding**
   ```bash
   touch database/database.sqlite
   php artisan migrate:fresh --seed
   ```
   *The seeders will create the roles, default users, 3 sample projects, and tasks.*

4. **Compile Assets**
   ```bash
   npm run build
   ```

5. **Run the Application**
   ```bash
   # Terminal 1: Web server
   php artisan serve

   # Terminal 2: Vite dev server (optional for frontend dev)
   npm run dev

   # Terminal 3: Queue worker (for emails/notifications)
   php artisan queue:work --tries=3

   # Terminal 4: Scheduler (for deadline reminders)
   php artisan schedule:work
   ```

## 🔑 Test Credentials

All seeded accounts share the password: `password`

| Role | Email |
|------|-------|
| **Admin** | `admin@taskflow.com` |
| **Project Manager** | `alice.pm@taskflow.com` |
| **Project Manager** | `bob.pm@taskflow.com` |
| **Employee** | `charlie@taskflow.com` |
| **Employee** | `diana@taskflow.com` |

## 📚 Documentation

- [API Documentation](API_DOCS.md) — Comprehensive guide to all 50 REST API endpoints.
- [Database & Folder Structure](laravel_rbac_folder_structure.md) — Details on the models, schema, and architectural layout.

## ✨ Bonus Features Status

As per the original project requirements, here is the implementation status of the requested bonus features:

- ✅ **File Attachments Storage** (Implemented — employees can attach files to work logs)
- ✅ **Dark Mode** (Implemented — responsive dark mode UI using Tailwind CSS)
- ✅ **API Documentation** (Implemented — comprehensive API_DOCS.md provided)
- ✅ **Dockerized Deployment** (Implemented — via Laravel Sail with MySQL/Redis)
- ✅ **CI/CD Pipeline** (Implemented — automated testing workflow via GitHub Actions)
- ❌ **Real-time notifications using WebSockets** (Not implemented)
- ❌ **Kanban Board View with Drag & Drop** (Not implemented)
- ❌ **Multi-Tenant Architecture** (Not implemented)
- ❌ **Unit & Integration Tests** (Removed per user request)

## 🕰️ Scheduled Commands

To test the automated notification systems without waiting for the cron schedule, you can run them manually:
- `php artisan reminders:send` — Dispatches reminders for tasks due soon.
- `php artisan tasks:mark-overdue` — Scans for missed deadlines, updates status, and alerts users.
