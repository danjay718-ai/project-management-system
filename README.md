# ProcureFlow

## Internal Procurement & Operations Approval System

---

## Table of Contents

1. Overview
2. Problem Statement
3. Solution Overview
4. System Scope & Target Users
5. High-Level Architecture
6. Tech Stack & Laravel Concepts
7. Module Breakdown

   * Module 1: Role-Based Access Control (RBAC)
   * Module 2: Purchase Request Management (Planned)
   * Module 3: Approval Policies & Workflows (Planned)
   * Module 4: Audit Logs & Activity Tracking (Planned)
   * Module 5: Reports & Insights (Planned)
8. Design Principles
9. System Limitations & Future Enhancements
10. Why This System Exists (Portfolio Context)
11. Current Status & Roadmap

---

## 1. Overview

**ProcureFlow** is a medium-sized internal system designed to manage purchase requests, approval workflows, and access control within an organization. It is built incrementally using modular design principles, starting from a strong authorization foundation and expanding into real business domains.

The system mirrors how real-world internal tools are developed: foundational infrastructure first, followed by business-specific modules that consume and rely on that foundation.

---

## 2. Problem Statement

Many organizations still rely on:

* Email or chat-based purchase approvals
* Spreadsheets for tracking requests
* Manual access control
* No audit trail or accountability

This leads to:

* Poor visibility of request status
* Unclear approval responsibility
* Security risks due to improper access
* Difficulty scaling internal processes

---

## 3. Solution Overview

ProcureFlow provides:

* Centralized role-based access control
* Structured purchase request workflows
* Policy-based authorization enforcement
* Auditability and traceability
* A scalable modular architecture

The system is intentionally designed to grow over time, with each module introducing new concepts without rewriting existing logic.

---

## 4. System Scope & Target Users

### Target Users

* **Staff** – creates and tracks purchase requests
* **Managers** – reviews and approves requests
* **Super Admins** – manages system configuration, users, and oversight

### Out of Scope (for now)

* External vendors
* Payment processing
* Accounting system integration

---

## 5. High-Level Architecture

The system follows a modular, domain-driven structure:

* A **core RBAC module** provides authorization primitives
* Business modules (e.g., Purchase Requests) consume RBAC
* Cross-cutting concerns (logs, reports) observe system behavior

Each module is isolated by responsibility but integrated through shared contracts (models, policies, permissions).

---

## 6. Tech Stack & Laravel Concepts

### Tech Stack

* **Backend:** Laravel 10 (PHP)
* **Database:** MySQL / MariaDB
* **Frontend:** Blade Templates / optional Vue.js for interactive components
* **Version Control:** Git / GitHub

### Laravel Concepts Used

* Eloquent ORM for models and relationships
* Many-to-many relationships via pivot tables
* Seeders for default data population
* Policies for domain-level authorization
* Helper methods (e.g., hasPermission) for authorization logic
* Model observers/events for future audit logging
* Modular design with service classes and organized namespaces

---

## 7. Module Breakdown

### 🟩 Module 1: Role-Based Access Control (RBAC)

**Status:** Completed (Foundation Module)

#### Purpose

Provides a reusable authorization foundation for the entire system.

#### Concepts Implemented

* Users, Roles, Permissions data models
* Many-to-many relationships via pivot tables
* Seeders for default roles and permissions
* Authorization helper methods (e.g., permission checks)

#### What This Module Does

* Defines *who* can do *what* at a system level
* Enables consistent authorization checks across all modules

#### What This Module Does NOT Do

* Contains no business logic
* Has no knowledge of purchase requests or approvals

---

### 🟦 Module 2: Purchase Request Management (Planned)

#### Purpose

Introduces the first real business domain that consumes RBAC.

#### Responsibilities

* Creation and tracking of purchase requests
* Ownership rules (who created what)
* Request lifecycle management (e.g., pending, approved, rejected)

#### Concepts to be Implemented

* Domain models
* Ownership-based access rules
* State-driven logic

---

### 🟨 Module 3: Approval Policies & Workflows (Planned)

#### Purpose

Enforces business rules on top of RBAC permissions.

#### Responsibilities

* Approval and rejection logic
* Role-aware and context-aware authorization

#### Concepts to be Implemented

* Laravel Policies
* Action-level authorization
* Separation of permissions vs business constraints

---

### 🟧 Module 4: Audit Logs & Activity Tracking (Planned)

#### Purpose

Ensures accountability and traceability.

#### Responsibilities

* Record critical system actions
* Provide immutable history for reviews and reports

#### Concepts to be Implemented

* Model observers or events
* Read-only access patterns

---

### 🟪 Module 5: Reports & Insights (Planned)

#### Purpose

Provides management-level visibility into system usage.

#### Responsibilities

* Aggregated views of requests and approvals
* Read-heavy operations

#### Concepts to be Implemented

* Query optimization
* Indexing considerations
* Permission-based reporting access

---

## 8. Design Principles

* **Separation of concerns** – each module has a single responsibility
* **Data-driven authorization** – roles and permissions drive behavior
* **Policy-based enforcement** – business rules are centralized
* **Incremental complexity** – features are added without refactoring foundations

---

## 9. System Limitations & Future Enhancements

### Limitations

* Notifications module not yet implemented
* Reports are read-heavy but do not support real-time analytics yet
* No external system integration (accounting, vendors)
* Frontend interactivity is minimal (mostly Blade templates)

### Future Enhancements

* Add notifications (email, in-app) for approvals and rejections
* Advanced reporting with charts and downloadable exports
* Multi-tenancy support for SaaS deployment
* API endpoints for integration with external systems
* Frontend SPA for improved UX (Vue.js / React)

---

## 10. Why This System Exists (Portfolio Context)

ProcureFlow is intentionally designed as a single evolving system rather than multiple isolated demo projects. This demonstrates:

* Real-world system design thinking
* Proper use of Laravel authorization features
* Ability to scale features without rewriting core logic
* Understanding of how business domains consume infrastructure modules

---

## 11. Current Status & Roadmap

### Completed

* Module 1: Role-Based Access Control (RBAC)

### In Progress / Planned

* Module 2: Purchase Request Management
* Module 3: Approval Policies
* Module 4: Audit Logs
* Module 5: Reports & Insights

The system will continue to evolve module by module, mirroring real production development practices.
