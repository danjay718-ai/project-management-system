# 🚀 Project & Task Management Tool (RBAC)

A comprehensive side-project built to explore and master Laravel from foundational mechanisms to advanced concepts. This system implements a robust Role-Based Access Control (RBAC) architecture wrapped in a modern, highly reactive frontend powered by Livewire 4 and Tailwind CSS v4.

---

## 🎯 Project Goals & Identity

Unlike typical tutorial projects, this repository is designed to mirror real-world application architecture. It intentionally focuses on:
- **Strict Authorization:** Eagerly enforcing limits using Laravel Policies and Gates at both the system and UI layers.
- **Modern Full-Stack Patterns:** Moving away from traditional Blade/Controller rendering in favor of Livewire 4 Multi-File Components (MFC) and Alpine.js.
- **Progressive Complexity:** Building a strong foundation (RBAC and Auth) before layering on business domains (Projects, Tasks, Activity Logs).
- **Premium Aesthetics:** Utilizing Tailwind CSS v4 to build dynamic, responsive, and glassmorphic UI components from scratch, avoiding reliance on heavy third-party CSS frameworks.

---

## 🛠️ Tech Stack & Architecture

- **Backend framework**: Laravel 10+ (PHP)
- **Frontend framework**: Livewire 4 (Volt Single-File and Multi-File Component patterns)
- **Interactivity**: Alpine.js (managed natively by Livewire)
- **Styling**: Tailwind CSS v4 (Compiled via Vite)
- **Database**: MySQL / MariaDB (managed via Eloquent ORM)

---

## ✨ Key Features & Implementation Highlights

### 1. Robust RBAC System
- Custom `User`, `Role`, and `Permission` models linked via Many-to-Many pivots.
- Helper methods like `$user->hasRole('Admin')` aggressively cached/lazy-loaded to prevent N+1 queries.
- Foundational requirement for all subsequent feature modules.

### 2. Modern Project Management
- Full CRUD interface built as a Livewire **Multi-File Component (MFC)**.
- Slide-over architectural patterns for Forms (replacing clunky full-page redirects).
- Debounced real-time search (`wire:model.live.debounce`) and reactive status filtering.
- Policy-enforced layout: UI actively hides buttons/actions if the user fails `ProjectPolicy` checks, backed by secondary server-side `Gate::authorize()` guards to prevent forged API requests.

### 3. State Management
- Seamless integration between Livewire 4 properties and Alpine.js reactive states using `$wire.entangle()`.
- Component scoped caching via `#[Computed]` properties to minimize database hits while maintaining reactive UI states.

---

## 📚 Developer Guides & Documentation

To understand the specific methodologies used in this project, refer to the detailed guides located in the `documentation/` directory:

1. [**Livewire 4 Dashboard Guide**](documentation/livewire-dashboard-guide.md) - Understanding the shift from standard Controllers to Livewire route binding and data fetching.
2. [**Mastering Tailwind & Livewire**](documentation/livewire-tailwind-guide.md) - A primer on transitioning from Bootstrap to utility-first styling and Livewire's `wire:model` lifecycle.
3. [**Project List CRUD Architecture**](documentation/livewire-project-list-guide.md) - Deep dive into how the Projects module was built, including the Volt SFC to MFC conversion and policy integration.

---

## 🗺️ Current Roadmap Status

Currently transitioning between Phase 1 (Foundation) and Phase 2 (Core Business Logic).

✅ **Phase 1: Foundation & RBAC Refinement**
- Core database migrations, Eloquent relationships, and Livewire Auth flows.

🔄 **Phase 2: Core Task & Project Management**
- *Projects CRUD completed (Livewire MFC + Policy Guards).*
- *Pending:* Task Management CRUD, dynamic assignment logic, and abstracting validations into Custom Form Requests.

*(See [ROADMAP.md](documentation/ROADMAP.md) for the full phase breakdown, spanning up to Background Queues, Caching, and RESTful APIs).*
