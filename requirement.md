Hi Candidate,


Thank you for applying for the Senior Full Stack Developer position at Millennial Company.

We're pleased to inform you that after reviewing your profile, you have been shortlisted for the next round of our hiring process. 🎉

As the next step, we'd like you to complete a practical assignment. This will help us evaluate your technical approach, code quality, and problem-solving skills.

Please read the instructions carefully and submit before the deadline.

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

🗂️ ASSIGNMENT BRIEF

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

 

Objective:

Build a Role-Based Project & Task Management System that allows organizations to manage projects, assign tasks, track work logs, and receive automated deadline notifications.

 

The application should demonstrate clean architecture, scalability, security, and production-ready development practices.

 

──────────────────────────────

USER ROLES & PERMISSIONS

──────────────────────────────

 

Admin — Full system access
Create, edit, and delete projects, tasks, and users
Assign Project Managers to projects
Assign Employees to tasks
View all projects, tasks, activity logs, reports, and analytics
 

Project Manager — Manages only assigned projects
View and manage assigned projects
Create, update, and assign tasks within assigned projects
Review and reply to employee work logs
View project progress and project-level reports
Restrictions: Cannot access other managers' projects, cannot create/delete users, cannot access admin settings.

 

Employee — Works on assigned tasks
View and update assigned tasks only
Submit work logs with hours and optional attachments
View Project Manager comments on logs
Receive task deadline notifications
Restrictions: Cannot create projects or tasks, cannot assign tasks, cannot view other employees' tasks.

 

──────────────────────────────

CORE MODULES TO BUILD

──────────────────────────────

 

✅ Authentication

Login / Logout / Password Reset
JWT or Session-based authentication
Role-based authorization (RBAC)
 

✅ Role-Based Dashboards

Admin: Total Projects, Tasks, Active Employees, Overdue & Completed Tasks, Progress Overview

Project Manager: Managed Projects, Active Tasks, Upcoming Deadlines, Employee Productivity

Employee: Assigned Tasks, Tasks Due Soon, Completed Tasks, Recent Activity Logs

 

✅ Project Management

Fields: Project Name, Description, Start Date, End Date, Status (Planning / Active / Completed / Archived), Assigned Project Manager

Features: Create, Edit, Archive, View Details, Progress Tracking

 

✅ Task Management

Fields: Task Name, Description, Priority (Low / Medium / High / Critical), Status (To Do / In Progress / In Review / Completed / Blocked), Deadline, Assigned Employee, Assigned Project, Estimated Hours, Created By

Features: Create, Edit, Assign Employee, Change Status, View Timeline, History Tracking

 

✅ Work Log System

Employees submit progress logs with: Description, Hours Worked, Timestamp, Optional Attachment

Project Managers can reply to logs — full conversation history must be stored.

 

✅ Email Notification System

Employee reminders: 48 Hours / 24 Hours / 12 Hours / 1 Hour before deadline

Overdue alerts: Separate emails to Employee and Project Manager when deadline passes

Project Manager alerts: Notified of upcoming and overdue tasks for their assigned employees

 

✅ Activity Audit Log

Track: Login events, Project/Task creation & updates, Assignments, Status changes, Log submissions, Manager replies

Fields: User | Action | Entity | Timestamp | Previous Value | New Value

 

✅ Search & Filters

Projects: Status, Manager, Date Range

Tasks: Status, Priority, Employee, Deadline

Logs: Employee, Project, Date Range

 

✅ Reports (Admin & Project Manager)

Project Report: Completion %, Total / Completed / Pending Tasks

Employee Report: Assigned Tasks, Completed Tasks, Avg Completion Time, Total Hours Logged

 

──────────────────────────────

TECHNICAL REQUIREMENTS

──────────────────────────────

 

Backend: REST API or GraphQL, RBAC, Input Validation, Error Handling, Pagination, Audit Logging

 

Database (Normalized MySQL schema):

Users, Roles, Projects, Tasks, TaskAssignments, WorkLogs, LogReplies, Notifications, AuditLogs

 

Scheduler: Background jobs for deadline reminders and overdue alerts

(Cron Jobs / Queue Workers / Background Scheduler)

 

──────────────────────────────

BONUS FEATURES (Optional)

──────────────────────────────

Real-time notifications using WebSockets
Kanban Board View with Drag & Drop
File Attachments Storage
Multi-Tenant Architecture
Dark Mode
API Documentation (Swagger / Postman)
Dockerized Deployment
Unit & Integration Tests
CI/CD Pipeline
 

──────────────────────────────

EVALUATION CRITERIA

──────────────────────────────

You will be evaluated on:

Database Design & Normalization
RBAC Implementation
Code Quality & Cleanliness
Scalability & Architecture
Security Practices
API Design
UI/UX
Background Job Implementation
Email Notification Logic
Testing Coverage
Documentation
 

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

📤 SUBMISSION DETAILS

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

 

📅 Deadline: 7th June 2025, 8:00 PM

📩 Submit to: yash@millennialcompany.in

 

Please include:

✔ GitHub repository link (public or shared access)

✔ README with setup instructions, architecture decisions & assumptions

✔ Hosted demo link (optional but preferred)

✔ Your Last Drawn Salary

 

⚠️ Late submissions will not be considered.

 

For any questions, feel free to reply to this email.

 

We wish you all the best and look forward to reviewing your work!

 

Warm regards,

Millennial Company Hiring Team