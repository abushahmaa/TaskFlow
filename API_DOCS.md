# TaskFlow — API Documentation

TaskFlow provides a RESTful API secured by JWT authentication. All requests (except login/password reset) require the `Authorization: Bearer {token}` header.

---

## 🔐 Authentication

### POST `/api/auth/login`
Authenticates a user and returns a JWT token.
- **Body:** `{ "email": "admin@taskflow.com", "password": "password" }`
- **Response:** `{ "access_token": "eyJ...", "token_type": "bearer", "expires_in": 3600, "user": {...} }`

### POST `/api/auth/logout`
Invalidates the current JWT token.
- **Headers:** `Authorization: Bearer {token}`
- **Response:** `{ "message": "Logged out successfully." }`

### GET `/api/auth/me`
Returns the current authenticated user's profile.

### POST `/api/auth/refresh`
Refreshes and returns a new JWT token.

---

## 👑 Admin Routes (Requires `admin` role)

### Users Management
- **GET `/api/admin/users`** — List all users (Query: `?role=employee&search=John&is_active=1`)
- **POST `/api/admin/users`** — Create a user. Body: `name`, `email`, `password`, `phone`, `role`
- **GET `/api/admin/users/{id}`** — Show specific user
- **PUT `/api/admin/users/{id}`** — Update a user
- **DELETE `/api/admin/users/{id}`** — Delete a user

### Projects Management
- **GET `/api/admin/projects`** — List projects (Query: `?status=active&manager_id=2`)
- **POST `/api/admin/projects`** — Create project. Body: `name`, `description`, `start_date`, `end_date`, `status`, `manager_id`
- **GET `/api/admin/projects/{id}`** — Show project details
- **PUT `/api/admin/projects/{id}`** — Update project
- **DELETE `/api/admin/projects/{id}`** — Soft delete project
- **POST `/api/admin/projects/{id}/restore`** — Restore soft-deleted project

### Tasks Management
- **GET `/api/admin/tasks`** — List all tasks (Query: `?project_id=1&status=to_do&priority=high`)
- **POST `/api/admin/tasks`** — Create a task. Body: `name`, `description`, `priority`, `deadline`, `estimated_hours`, `project_id`, `assigned_to`
- **GET `/api/admin/tasks/{id}`** — Show task
- **PUT `/api/admin/tasks/{id}`** — Update task
- **DELETE `/api/admin/tasks/{id}`** — Delete task
- **POST `/api/admin/tasks/{id}/assign`** — Assign task. Body: `user_id`

### Reports & Audits
- **GET `/api/admin/reports/projects`** — Project completion reports
- **GET `/api/admin/reports/employees`** — Employee productivity (total hours, avg completion time)
- **GET `/api/admin/reports/projects/{id}`** — Detailed single project report
- **GET `/api/admin/audit-logs`** — View system activity logs (Query: `?event=created&log_name=default`)

---

## 👔 Project Manager Routes (Requires `project-manager` role)

*Note: PMs can only access projects they are assigned to manage, and tasks within those projects.*

### Dashboards & Reports
- **GET `/api/pm/dashboard`** — PM KPIs, upcoming deadlines
- **GET `/api/pm/reports`** — Progress reports for managed projects
- **GET `/api/pm/reports/{project_id}`** — Detailed report for a specific project

### Projects & Tasks
- **GET `/api/pm/projects`** — List managed projects
- **GET `/api/pm/projects/{id}`** — Show managed project
- **PUT `/api/pm/projects/{id}`** — Update project (description, status only)
- **GET `/api/pm/projects/{project_id}/tasks`** — List tasks for a project
- **POST `/api/pm/projects/{project_id}/tasks`** — Create task within project
- **PUT `/api/pm/projects/{project_id}/tasks/{task_id}`** — Update task
- **DELETE `/api/pm/projects/{project_id}/tasks/{task_id}`** — Delete task
- **POST `/api/pm/projects/{project_id}/tasks/{task_id}/assign`** — Assign employee

### Work Logs
- **GET `/api/pm/work-logs/{log_id}`** — View employee work log details
- **POST `/api/pm/work-logs/{log_id}/reply`** — Reply to a work log. Body: `message`

---

## 👷 Employee Routes (Requires `employee` role)

*Note: Employees can only view/interact with tasks assigned to them.*

### Dashboards
- **GET `/api/employee/dashboard`** — My active tasks, recent logs, KPIs

### Tasks
- **GET `/api/employee/tasks`** — List my assigned tasks
- **GET `/api/employee/tasks/{id}`** — Show task details
- **PATCH `/api/employee/tasks/{id}/status`** — Update task status. Body: `status` (in_progress, in_review, completed)

### Work Logs
- **GET `/api/employee/tasks/{task_id}/logs`** — List work logs for a task
- **POST `/api/employee/tasks/{task_id}/logs`** — Submit a work log. Body: `hours_worked`, `description`, `attachment` (optional file)
- **GET `/api/employee/logs/{log_id}`** — View specific work log + replies
- **GET `/api/employee/logs/{log_id}/download`** — Download attached file from work log
