# 🚀 Project & Task Management Tool (RBAC) - Roadmap & Progress
*A side-project to explore and master Laravel from Basic to Advanced concepts.*

---

## 🎯 Current Status Overview

### ✅ What is Already Done
- **Database Models & Migrations:** 
  - Defined core models: `User`, `Role`, `Permission`, `Project`, `Task`.
  - Created Pivot tables for many-to-many relationships: `role_user`, `permission_role`, `project_user`.
- **Authentication & Setup:** 
  - Standard User/Profile scaffolding is set up.
  - Livewire Auth components (`Login.php`, `Register.php`) integrated.
- **Authorization (RBAC):**
  - Integrated custom Policies: `ProjectPolicy` and `TaskPolicy` to control and limit user access based on role permissions.

### ⚠️ What Needs Immediate Attention
- The `ProjectList` UI and Livewire logic (referenced in routes) is either missing or incomplete. 
- The Admin UI for mapping Users to Roles/Permissions is not yet built.
- Seeders are needed to populate dummy data and foundational Roles (e.g., Admin, Manager, User) instantly on fresh installations.

---

## 🗺️ Development Roadmap (Phase by Phase)

### Phase 1: Foundation & RBAC Refinement (Beginner to Intermediate)
> *Focus: Solidifying Eloquent relationships, Database Seeding, and strict Authentication/Authorization.*
- [x] Create core database migrations (Users, Roles, Permissions, Projects, Tasks).
- [x] Configure Model Relationships (HasMany, BelongsToMany).
- [x] Define Laravel Policies for authorization gating.
- [ ] Write **Database Seeders & Factories** (Essential for quick testing without manual database entry).
- [ ] Flesh out UI for robust Registration and Login matching specific app layouts.

### Phase 2: Core Task & Project Management (Intermediate)
> *Focus: Exploring Livewire (v3) reactivity, Form Validations, and Component communication.*
- [ ] **Project Management CRUD:**
  - Create standard Views/Components to List, Create, Edit, and Delete projects.
  - Implement dynamic UI to Assign/Revoke users (`project_user` pivot) via Livewire.
- [ ] **Task Management CRUD:**
  - Build UI for tasks associated with individual projects.
  - Implement task status updates (To Do, In Progress, Review, Done).
- [ ] **Custom Form Requests:** Abstract backend validation rules into dedicated FormRequest classes.
- [ ] **Advanced Blade Directives:** Refine Blade layouts, utilizing sub-views, components, and slots heavily.

### Phase 3: Engagement & Tracking (Advanced)
> *Focus: Observers, Events, Listeners, and External Services.*
- [ ] **Activity Logging (Observers & Events):** Implement Model Observers to log system history (e.g., "John updated Task X to 'Done'").
- [ ] **File Storage:** Allow users to upload attachments (documents, images) to Tasks using Laravel's local or S3 filesystem disks.
- [ ] **Notifications & Mailables:** 
  - Send email notifications when a user is assigned a task.
  - In-app notification bell (Database notifications).

### Phase 4: Performance & Background Processing (Advanced to Master)
> *Focus: Queues, Jobs, Task Scheduling, and Optimization.*
- [ ] **Queued Jobs:** Move the email sending logic to background Queues to stop UI blocking.
- [ ] **Task Scheduling (Cron):** Daily automated background jobs to email users a "Pending Tasks Summary".
- [ ] **Caching:** Cache heavy database queries (e.g., Dashboard metrics showing total open tasks) using Redis or file cache.
- [ ] **Database Optimization:** Prevent N+1 query problems in the UI using strict Eager Loading config.

### Phase 5: Administration & External APIs (Master)
> *Focus: Middleware, API routing, Sanctum configuration, and Testing.*
- [ ] **Admin Dashboard:** A highly protected route (using custom Middleware) for system admins to create custom Roles & Permissions.
- [ ] **RESTful API:** Build an API namespace allowing external Frontends (React/Vue/Mobile) to consume Project and Task data. Secure with Laravel Sanctum.
- [ ] **Automated Testing:** Implement Feature and Unit tests using PHPUnit/Pest to ensure Policies and CRUD functionality do not break as the app scales.

---

*This roadmap assumes continuous refactoring of CSS/UI alongside backend development to ensure a premium look and feel.*
