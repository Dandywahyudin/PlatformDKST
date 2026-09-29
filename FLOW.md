# Digital & AI Platform DKST

> MVP — Admin Portal

Digital & AI Platform DKST merupakan aplikasi web enterprise untuk mendukung pengelolaan aktivitas dan proses operasional Direktorat Kawasan Sains dan Teknologi (DKST) Institut Teknologi Bandung (ITB).

Project ini dikembangkan secara bertahap. Pada tahap MVP, fokus utama adalah membangun **Admin Portal** sebagai fondasi untuk pengembangan platform DKST pada tahap berikutnya.

---

## 📌 Project Overview

Platform DKST dirancang untuk menjadi satu platform terintegrasi yang nantinya dapat menangani:

- Program Management
- Approval Workflow
- Task Management
- Document Management
- User & Role Management
- Notification
- Audit Log
- Finance
- Asset Management
- Technology Transfer
- Business Development
- Integration dengan sistem ITB
- AI Assistant
- AI Knowledge / RAG
- Analytics & AI Insight

Namun, **fitur-fitur tersebut tidak semuanya dibuat pada MVP**.

MVP difokuskan terlebih dahulu pada proses inti:

```text
Authentication
      ↓
Admin Dashboard
      ↓
User & Role Management
      ↓
Program Management
      ↓
Document Management
      ↓
Approval Workflow
      ↓
Task Management
      ↓
Notification
      ↓
Audit Log
```

---

# 🎯 MVP Objective

Tujuan MVP adalah menghasilkan sebuah Admin Portal yang sudah dapat digunakan untuk mengelola proses dasar DKST secara end-to-end.

Admin harus dapat:

1. Login ke aplikasi
2. Melihat dashboard
3. Mengelola user
4. Mengelola role & permission
5. Membuat program
6. Mengubah program
7. Mengupload dokumen
8. Submit program
9. Melakukan approval
10. Membuat task
11. Menyelesaikan task
12. Menerima notification
13. Melihat audit log

---

# 🛠️ Technology Stack

## Backend

- Laravel 12+
- PHP 8.3+
- PostgreSQL
- Laravel Eloquent ORM
- Laravel Form Request
- Laravel Policies / Gates
- Laravel Notifications
- Laravel Storage

## Frontend

- Blade
- Tailwind CSS
- Alpine.js
- Vite

## Authentication

- Laravel Breeze / Fortify
- Session-based Authentication

## Development

- Git
- GitHub
- Composer
- NPM
- Laravel Artisan

---

# 🏗️ Architecture

MVP menggunakan arsitektur Laravel MVC dengan tambahan Service Layer untuk business logic.

```text
┌──────────────────────────────┐
│          Browser             │
│        Blade + Tailwind      │
└──────────────┬───────────────┘
               │
               ▼
┌──────────────────────────────┐
│          Routes              │
│       Web / Admin Routes     │
└──────────────┬───────────────┘
               │
               ▼
┌──────────────────────────────┐
│        Controllers           │
└──────────────┬───────────────┘
               │
               ▼
┌──────────────────────────────┐
│         Services             │
│       Business Logic         │
└──────────────┬───────────────┘
               │
               ▼
┌──────────────────────────────┐
│      Models / Eloquent       │
└──────────────┬───────────────┘
               │
               ▼
┌──────────────────────────────┐
│         PostgreSQL           │
└──────────────────────────────┘
```

---

# 👤 MVP Role

Pada MVP hanya terdapat satu role:

```text
ADMIN
```

Admin memiliki akses penuh terhadap seluruh fitur MVP.

Role berikut belum diimplementasikan:

```text
DIRECTOR
STAFF
REVIEWER
EXTERNAL_USER
```

Role tersebut akan ditambahkan pada tahap berikutnya.

---

# 📦 MVP Modules

## 1. Authentication

Fitur:

- Login
- Logout
- Remember Me
- Password hashing
- Session authentication
- Login validation

Route:

```text
/login
```

---

## 2. Dashboard

Dashboard memberikan gambaran kondisi aplikasi.

KPI:

```text
Total Users
Total Programs
Active Programs
Pending Approvals
Pending Tasks
Total Documents
```

Dashboard juga menampilkan:

- Program Status
- Program Progress
- Recent Activities
- Pending Approvals
- Recent Documents

Route:

```text
/admin/dashboard
```

---

## 3. User Management

Admin dapat:

- Melihat user
- Membuat user
- Mengubah user
- Menghapus user
- Mengaktifkan user
- Menonaktifkan user
- Assign role

Route:

```text
/admin/users
```

Data utama:

```text
Name
Email
Role
Status
Created At
Last Login
```

---

# 🔐 4. Role & Permission

Admin dapat mengelola:

- Role
- Permission
- Role Permission

Contoh permission:

```text
dashboard.view

users.view
users.create
users.update
users.delete

programs.view
programs.create
programs.update
programs.delete
programs.approve
programs.reject

documents.view
documents.upload
documents.download
documents.delete

tasks.view
tasks.create
tasks.update
tasks.delete

approvals.view
approvals.process

notifications.view

audit_logs.view

settings.manage
```

Authorization harus menggunakan:

- Middleware
- Policy
- Gate

Jangan melakukan authorization secara hardcode di setiap controller.

---

# ⭐ 5. Program Management

Program Management merupakan **core module MVP**.

Admin dapat:

- Membuat program
- Melihat program
- Mengubah program
- Menghapus program
- Submit program
- Approve program
- Reject program
- Memulai program
- Menyelesaikan program

Data program:

```text
ID
Code
Name
Description
PIC
Start Date
End Date
Budget
Progress
Status
Created By
Created At
Updated At
```

Program code dibuat otomatis.

Contoh:

```text
DKST-PRG-2026-0001
```

---

# 🔄 6. Program Workflow

Program menggunakan workflow:

```text
DRAFT
   │
   ▼
SUBMITTED
   │
   ▼
UNDER_REVIEW
   │
   ├──────────────┐
   │              │
   ▼              ▼
APPROVED       REJECTED
   │              │
   ▼              │
IN_PROGRESS ◄─────┘
   │
   ▼
COMPLETED
```

### Workflow Detail

```text
Create Program
      ↓
Save Draft
      ↓
Submit
      ↓
Under Review
      ↓
Approve / Reject
      ↓
Approved
      ↓
In Progress
      ↓
Completed
```

Setiap perubahan status harus dicatat ke audit log.

---

# 📄 7. Document Management

Admin dapat:

- Upload document
- View document
- Download document
- Delete document
- Search document
- Filter document

Document dapat dikaitkan dengan program.

Contoh:

```text
Program
└── Inkubasi Startup 2026

    ├── Proposal.pdf
    ├── TOR.pdf
    ├── Budget.xlsx
    └── Approval Letter.pdf
```

File disimpan menggunakan Laravel Storage.

Gunakan private storage:

```text
storage/app/private
```

File tidak boleh diakses secara langsung tanpa authorization.

---

# ✅ 8. Approval Management

Approval digunakan untuk memproses program yang telah disubmit.

Route:

```text
/admin/approvals
```

Status:

```text
PENDING
APPROVED
REJECTED
```

Saat melakukan rejection, Admin wajib memberikan alasan.

Contoh:

```text
Reason:
"Budget proposal perlu diperbaiki."
```

Approval harus menyimpan:

```text
Program
Requested By
Reviewer
Status
Reason
Created At
Processed At
```

---

# 📋 9. Task Management

Task digunakan untuk mengelola pekerjaan yang berkaitan dengan program atau proses lainnya.

Data:

```text
Title
Description
Assignee
Related Program
Priority
Due Date
Status
```

Priority:

```text
LOW
MEDIUM
HIGH
URGENT
```

Status:

```text
TODO
IN_PROGRESS
COMPLETED
CANCELLED
```

Contoh:

```text
Task:
Review Proposal

Program:
Inkubasi Startup 2026

Assignee:
Admin

Priority:
HIGH

Due Date:
30 September 2026

Status:
TODO
```

---

# 🔔 10. Notification

Notification digunakan untuk memberi informasi kepada user.

Contoh:

```text
Program submitted
Program approved
Program rejected
Task assigned
Task deadline
Document uploaded
```

Admin dapat:

- Melihat notification
- Mark as read
- Mark all as read

Gunakan Laravel Notification.

---

# 📝 11. Audit Log

Audit Log digunakan untuk mencatat aktivitas penting dalam sistem.

Contoh:

```text
User created
User updated
User deleted

Program created
Program updated
Program submitted
Program approved
Program rejected

Document uploaded
Document deleted

Task created
Task completed
```

Data:

```text
User
Action
Module
Entity
Description
IP Address
Timestamp
```

Audit log tidak dapat diedit melalui aplikasi.

---

# ⚙️ 12. Settings

Admin dapat mengatur konfigurasi aplikasi.

Contoh:

```text
Application Name
Application Logo
Timezone
Date Format
Maximum Upload Size
```

Route:

```text
/admin/settings
```

---

# 🗄️ Database

Database menggunakan PostgreSQL.

## Core Tables

```text
users

roles
permissions
role_user
permission_role

programs
program_members

documents

tasks

approvals
approval_actions

notifications

audit_logs

settings
```

---

# 🔗 Database Relationship

Gambaran relasi:

```text
users
  │
  ├──────── roles
  │
  ├──────── programs
  │
  ├──────── tasks
  │
  ├──────── documents
  │
  ├──────── notifications
  │
  └──────── audit_logs


programs
  │
  ├──────── documents
  │
  ├──────── tasks
  │
  ├──────── approvals
  │
  └──────── program_members


approvals
  │
  └──────── approval_actions
```

---

# 🛣️ Route Structure

Gunakan prefix:

```text
/admin
```

Contoh:

```text
/admin/dashboard

/admin/users
/admin/users/create
/admin/users/{id}/edit

/admin/roles
/admin/roles/create
/admin/roles/{id}/edit

/admin/programs
/admin/programs/create
/admin/programs/{id}
/admin/programs/{id}/edit

/admin/approvals
/admin/approvals/{id}

/admin/tasks
/admin/tasks/create
/admin/tasks/{id}/edit

/admin/documents

/admin/notifications

/admin/audit-logs

/admin/settings
```

---

# 📁 Project Structure

Struktur Laravel yang digunakan:

```text
app/
├── Http/
│   ├── Controllers/
│   │   └── Admin/
│   │       ├── DashboardController.php
│   │       ├── UserController.php
│   │       ├── RoleController.php
│   │       ├── ProgramController.php
│   │       ├── DocumentController.php
│   │       ├── TaskController.php
│   │       ├── ApprovalController.php
│   │       ├── NotificationController.php
│   │       ├── AuditLogController.php
│   │       └── SettingController.php
│   │
│   └── Requests/
│       ├── StoreUserRequest.php
│       ├── UpdateUserRequest.php
│       ├── StoreProgramRequest.php
│       ├── UpdateProgramRequest.php
│       ├── StoreTaskRequest.php
│       └── UpdateTaskRequest.php
│
├── Models/
│   ├── User.php
│   ├── Role.php
│   ├── Permission.php
│   ├── Program.php
│   ├── ProgramMember.php
│   ├── Document.php
│   ├── Task.php
│   ├── Approval.php
│   ├── ApprovalAction.php
│   ├── AuditLog.php
│   └── Setting.php
│
├── Policies/
│   ├── UserPolicy.php
│   ├── RolePolicy.php
│   ├── ProgramPolicy.php
│   ├── DocumentPolicy.php
│   ├── TaskPolicy.php
│   └── ApprovalPolicy.php
│
└── Services/
    ├── ProgramService.php
    ├── ApprovalService.php
    ├── DocumentService.php
    └── AuditLogService.php
```

---

# 🚀 Development Flow

Project dikerjakan secara bertahap.

Jangan mengerjakan semua module sekaligus.

---

## Phase 1 — Project Setup

### Objective

Mempersiapkan project Laravel.

### Tasks

- [ ] Create Laravel project
- [ ] Configure `.env`
- [ ] Configure PostgreSQL
- [ ] Configure Vite
- [ ] Install Tailwind CSS
- [ ] Configure application timezone
- [ ] Configure storage
- [ ] Setup Git repository
- [ ] Create initial README

### Expected Result

Laravel berhasil dijalankan:

```bash
php artisan serve
```

dan aplikasi dapat dibuka melalui:

```text
http://127.0.0.1:8000
```

---

# Phase 2 — Authentication

### Objective

Membuat sistem login Admin.

### Tasks

- [ ] Install Laravel Breeze / Fortify
- [ ] Create login page
- [ ] Implement login
- [ ] Implement logout
- [ ] Password hashing
- [ ] Authentication middleware
- [ ] Login validation
- [ ] Rate limiting

### Expected Flow

```text
Login
  ↓
Validate Credentials
  ↓
Authentication
  ↓
Admin Dashboard
```

---

# Phase 3 — Database & RBAC

### Objective

Membangun fondasi database dan authorization.

### Tasks

- [ ] Create users migration
- [ ] Create roles migration
- [ ] Create permissions migration
- [ ] Create role_user migration
- [ ] Create permission_role migration
- [ ] Create models
- [ ] Create relationships
- [ ] Create Admin seeder
- [ ] Create permission seeder
- [ ] Implement middleware
- [ ] Implement Policies

### Expected Result

Admin dapat login dan memiliki akses berdasarkan permission.

---

# Phase 4 — Admin Dashboard

### Objective

Menyediakan overview sistem.

### Tasks

- [ ] Create dashboard layout
- [ ] Create sidebar
- [ ] Create topbar
- [ ] Create KPI cards
- [ ] Create program statistics
- [ ] Create recent activity
- [ ] Create pending approvals
- [ ] Create recent documents
- [ ] Connect dashboard to database

### Expected Result

Dashboard menampilkan data aktual dari database.

---

# Phase 5 — User Management

### Objective

Admin dapat mengelola pengguna.

### Tasks

- [ ] User list
- [ ] User detail
- [ ] Create user
- [ ] Edit user
- [ ] Delete user
- [ ] Activate user
- [ ] Deactivate user
- [ ] Assign role
- [ ] Search
- [ ] Filter
- [ ] Validation

---

# Phase 6 — Role & Permission Management

### Objective

Membuat authorization yang scalable.

### Tasks

- [ ] Role list
- [ ] Create role
- [ ] Edit role
- [ ] Delete role
- [ ] Permission list
- [ ] Assign permissions
- [ ] Policy implementation
- [ ] Middleware implementation

### Expected Result

Role baru dapat ditambahkan tanpa mengubah business logic utama.

---

# Phase 7 — Program Management

### Objective

Membangun core business module.

### Tasks

- [ ] Program migration
- [ ] Program model
- [ ] Program relationship
- [ ] Program list
- [ ] Program detail
- [ ] Create program
- [ ] Edit program
- [ ] Delete program
- [ ] Generate program code
- [ ] Search
- [ ] Filter
- [ ] Pagination
- [ ] Validation

### Expected Result

Admin dapat mengelola program secara penuh.

---

# Phase 8 — Program Workflow

### Objective

Mengimplementasikan business workflow.

### Workflow

```text
DRAFT
 ↓
SUBMITTED
 ↓
UNDER_REVIEW
 ↓
APPROVED
 ↓
IN_PROGRESS
 ↓
COMPLETED
```

Alternative:

```text
UNDER_REVIEW
 ↓
REJECTED
 ↓
DRAFT
```

### Tasks

- [ ] Submit program
- [ ] Approve program
- [ ] Reject program
- [ ] Start program
- [ ] Complete program
- [ ] Validate status transition
- [ ] Database transaction
- [ ] Record workflow history

---

# Phase 9 — Document Management

### Objective

Menghubungkan dokumen dengan program.

### Tasks

- [ ] Document migration
- [ ] Document model
- [ ] Upload document
- [ ] Download document
- [ ] Delete document
- [ ] Document preview
- [ ] File validation
- [ ] Private storage
- [ ] Authorization
- [ ] Document-program relationship

---

# Phase 10 — Approval Management

### Objective

Membuat approval workflow.

### Tasks

- [ ] Approval migration
- [ ] Approval model
- [ ] Approval list
- [ ] Approval detail
- [ ] Approve
- [ ] Reject
- [ ] Rejection reason
- [ ] Approval history
- [ ] Authorization

---

# Phase 11 — Task Management

### Objective

Membuat task yang berhubungan dengan program.

### Tasks

- [ ] Task migration
- [ ] Task model
- [ ] Create task
- [ ] Edit task
- [ ] Delete task
- [ ] Assign task
- [ ] Update status
- [ ] Complete task
- [ ] Priority
- [ ] Due date
- [ ] Related program

---

# Phase 12 — Notification

### Objective

Memberikan informasi kepada user.

### Tasks

- [ ] Notification implementation
- [ ] Notification dropdown
- [ ] Unread counter
- [ ] Mark as read
- [ ] Mark all as read
- [ ] Program notification
- [ ] Task notification
- [ ] Document notification

---

# Phase 13 — Audit Log

### Objective

Mencatat aktivitas penting.

### Tasks

- [ ] Audit log migration
- [ ] AuditLog model
- [ ] AuditLog service
- [ ] Record CRUD activity
- [ ] Record workflow activity
- [ ] Record authentication activity
- [ ] Audit log page
- [ ] Audit log filter

---

# Phase 14 — Settings & Profile

### Objective

Menyediakan pengaturan dasar aplikasi.

### Tasks

- [ ] Profile page
- [ ] Update profile
- [ ] Change password
- [ ] Application settings
- [ ] Timezone
- [ ] Date format
- [ ] File upload limit

---

# Phase 15 — Testing

Sebelum MVP dianggap selesai, seluruh core flow harus diuji.

## Authentication Test

- [ ] Login berhasil
- [ ] Login gagal
- [ ] Logout
- [ ] Unauthorized user ditolak

## User Test

- [ ] Create user
- [ ] Update user
- [ ] Delete user
- [ ] Assign role

## Program Test

- [ ] Create program
- [ ] Update program
- [ ] Submit program
- [ ] Approve program
- [ ] Reject program
- [ ] Complete program

## Document Test

- [ ] Upload
- [ ] Download
- [ ] Delete
- [ ] Invalid file rejected

## Approval Test

- [ ] Approve
- [ ] Reject
- [ ] Rejection reason required

## Task Test

- [ ] Create
- [ ] Assign
- [ ] Update
- [ ] Complete

## Audit Test

- [ ] Create activity logged
- [ ] Update activity logged
- [ ] Delete activity logged
- [ ] Approval activity logged

---

# 🧪 MVP End-to-End Test

MVP dianggap berhasil apabila flow berikut dapat berjalan tanpa error:

```text
ADMIN LOGIN
     ↓
DASHBOARD
     ↓
CREATE PROGRAM
     ↓
SAVE DRAFT
     ↓
UPLOAD DOCUMENT
     ↓
SUBMIT PROGRAM
     ↓
OPEN APPROVAL
     ↓
APPROVE / REJECT
     ↓
UPDATE PROGRAM STATUS
     ↓
CREATE TASK
     ↓
COMPLETE TASK
     ↓
VIEW NOTIFICATION
     ↓
VIEW AUDIT LOG
```

Semua data harus tersimpan di PostgreSQL.

---

# 📊 MVP Definition of Done

MVP dinyatakan selesai jika:

- [ ] Authentication berjalan
- [ ] Admin role berjalan
- [ ] RBAC berjalan
- [ ] Dashboard menggunakan data database
- [ ] User management berjalan
- [ ] Role management berjalan
- [ ] Program management berjalan
- [ ] Program workflow berjalan
- [ ] Document management berjalan
- [ ] Approval berjalan
- [ ] Task management berjalan
- [ ] Notification berjalan
- [ ] Audit log berjalan
- [ ] Settings berjalan
- [ ] Validation berjalan
- [ ] Authorization berjalan
- [ ] Error handling berjalan
- [ ] Database transaction diterapkan pada proses penting
- [ ] Test utama berhasil
- [ ] Tidak ada hardcoded business data

---

# 🚫 Out of Scope MVP

Fitur berikut **belum dikerjakan pada MVP**:

```text
AI Assistant
AI RAG
Document AI
AI Agent
AI Analytics

Finance
Procurement
Asset Management

Technology Transfer
Intellectual Property

Startup Management
Incubation
Business Development

IT Service Management

ITB SSO

SIRENDU Integration
Oracle Fusion Integration
E-Office Integration
HRIS Integration
SATUDATA Integration

API Gateway
Event-driven Architecture
Microservices
```

Fitur tersebut akan dikembangkan pada phase berikutnya.

---

# 🛣️ Future Roadmap

## Phase 2 — Business Expansion

```text
MVP
 │
 ├── Staff Role
 ├── Reviewer Role
 ├── Director Role
 │
 ├── Finance
 ├── Asset Management
 ├── Business Development
 ├── Startup / Incubation
 └── Technology Transfer
```

---

## Phase 3 — Integration

```text
DKST Platform
      │
      ▼
Integration Layer
      │
 ┌────┼────────┬────────┐
 ▼    ▼        ▼        ▼
SIRENDU
Oracle Fusion
E-Office
HRIS
SATUDATA
```

---

## Phase 4 — DKST AI Core

```text
              DKST AI CORE
                    │
       ┌────────────┼────────────┐
       ▼            ▼            ▼
 AI Assistant      RAG      Document AI
       │            │            │
       └────────────┼────────────┘
                    ▼
                Analytics
                    │
                    ▼
              AI Executive
                 Insight
```

---

# 🔒 Security Principles

Security harus diperhatikan sejak MVP.

Implementasi:

- Authentication
- Authorization
- Role-based access
- Laravel Policies
- CSRF Protection
- XSS Protection
- SQL Injection Protection
- Form Request Validation
- Password Hashing
- Rate Limiting
- Secure File Upload
- Private File Storage
- Audit Logging
- Mass Assignment Protection

Jangan mengandalkan frontend untuk authorization.

Authorization harus selalu dilakukan di backend.

---

# 🌱 Git Workflow

Gunakan branch berdasarkan feature.

Contoh:

```text
main
 │
 ├── develop
 │
 ├── feature/authentication
 │
 ├── feature/rbac
 │
 ├── feature/dashboard
 │
 ├── feature/program-management
 │
 ├── feature/program-workflow
 │
 ├── feature/document-management
 │
 ├── feature/approval
 │
 ├── feature/task-management
 │
 ├── feature/notification
 │
 └── feature/audit-log
```

Format commit:

```text
feat: add admin authentication
feat: add program management
feat: add program approval workflow

fix: fix program status transition
fix: fix document authorization

refactor: improve program service

test: add program workflow test

docs: update README
```

---

# 📝 Development Rules

1. Jangan mengerjakan fitur di luar scope phase yang sedang berjalan.
2. Setiap feature harus mempunyai migration jika membutuhkan perubahan database.
3. Business logic tidak boleh seluruhnya diletakkan di Controller.
4. Gunakan Service untuk business logic yang kompleks.
5. Gunakan Form Request untuk validation.
6. Gunakan Policy untuk authorization.
7. Gunakan database transaction untuk proses yang memerlukan beberapa perubahan data.
8. Setiap perubahan status penting harus dicatat.
9. Jangan menyimpan file sensitif di public storage.
10. Jangan menggunakan hardcoded business data.
11. Jangan mengimplementasikan AI sebelum core data dan workflow stabil.
12. Jangan menggunakan microservices pada MVP.
13. Prioritaskan functionality sebelum visual enhancement.
14. Setiap feature harus diuji sebelum masuk ke branch utama.

---

# 📌 Development Priority

Urutan prioritas:

```text
P0 — Critical
├── Authentication
├── RBAC
├── Database
├── Program Management
└── Program Workflow

P1 — High
├── Approval
├── Document Management
├── Task Management
└── Dashboard

P2 — Medium
├── Notification
├── Audit Log
└── Settings

P3 — Future
├── AI
├── Integration
├── Finance
├── Asset
├── Technology Transfer
└── Business Development
```

---

# 🎯 Core Business Flow

Flow utama MVP:

```text
Admin
  │
  ▼
Login
  │
  ▼
Dashboard
  │
  ▼
Create Program
  │
  ▼
Draft
  │
  ▼
Submit
  │
  ▼
Under Review
  │
  ├───────────────┐
  ▼               ▼
Approve          Reject
  │               │
  ▼               ▼
Approved         Draft
  │
  ▼
In Progress
  │
  ▼
Completed
```

Program dapat memiliki:

```text
Program
 ├── Documents
 ├── Tasks
 ├── Approvals
 └── Activity History
```

---

# 📈 Long-Term Vision

MVP ini merupakan fondasi dari Digital & AI Platform DKST.

Target akhirnya:

```text
                    DKST PLATFORM
                          │
        ┌─────────────────┼─────────────────┐
        ▼                 ▼                 ▼
   Business            Operations          AI Core
   Modules             Modules
        │                 │                 │
        ▼                 ▼                 ▼
 Programs              Finance          AI Assistant
 Technology             Assets           RAG
 Startup                HRIS             Document AI
 Partnership            IT               Analytics
        │                 │                 │
        └─────────────────┼─────────────────┘
                          ▼
                  Integration Layer
                          │
          ┌───────────────┼───────────────┐
          ▼               ▼               ▼
       SIRENDU       Oracle Fusion      E-Office
                          │
                     HRIS / SATUDATA
```

---

# 👨‍💻 Development Philosophy

Project dikembangkan secara bertahap:

```text
Build Small
    ↓
Test
    ↓
Validate
    ↓
Deploy
    ↓
Collect Feedback
    ↓
Improve
    ↓
Expand
```

Prioritas utama bukan membuat semua fitur sekaligus, tetapi memastikan setiap fitur yang dibuat memiliki:

- Business value
- Clear workflow
- Valid data
- Proper authorization
- Auditability
- Maintainable code
- Scalability

---

# 🚀 Final Goal of MVP

MVP harus menghasilkan aplikasi yang mampu membuktikan bahwa DKST dapat mengelola proses:

```text
USER
 ↓
PROGRAM
 ↓
DOCUMENT
 ↓
APPROVAL
 ↓
TASK
 ↓
NOTIFICATION
 ↓
AUDIT LOG
```

secara terintegrasi dalam satu aplikasi Laravel.

Setelah flow tersebut stabil, platform dapat dikembangkan secara bertahap menjadi **Digital & AI Platform DKST** yang lebih luas.
