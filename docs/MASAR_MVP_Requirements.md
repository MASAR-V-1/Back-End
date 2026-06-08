# MASAR (مَسَار) — MVP Requirements Specification (Revised)

> **Status:** Draft v1.1 (engineering-ready)
> **Owner:** Product / Engineering
> **Scope:** MVP across IT & Admin, Field Operations, HR & Volunteer, and M&E
> **Last updated:** 2026-06-08

This document supersedes the initial "Sorted MVP Requirements". It keeps the
original four-module structure but fixes structural gaps found during review so
that engineering can start without ambiguity.

---

## 1. Overview & System Architecture

MASAR is a humanitarian/NGO operations platform composed of:

| Component | Tech | Repository |
|---|---|---|
| Core API & business logic | Laravel (PHP) | `MASAR-V-1/Back-End` |
| Web dashboard (admin/coordinator) | Vue or React SPA | `MASAR-V-1/Front-End-` |
| Field mobile app (officers) | Flutter | `MASAR-V-1/Flutter` |
| AI microservice (dedup + reporting) | Python FastAPI | `MASAR-V-1/AI-Service` *(to be created)* |
| Database | PostgreSQL (recommended) | — |
| Cache / rate-limit / queue | Redis | — |

**Key architectural decisions**

- **PostgreSQL over MySQL** is recommended because the `pg_trgm` extension can do
  Arabic fuzzy matching natively and may remove the need for a separate dedup
  service round-trip.
- The **AI-Service does not connect to the application database directly.** Laravel
  pre-selects candidate records and sends them to the AI-Service. This keeps the
  services loosely coupled.
- All inter-service traffic is authenticated with an **internal API key** (and is
  network-restricted where possible).

---

## 2. Cross-Cutting Requirements (apply to all modules)

These were missing or inconsistent in the first draft and must be resolved first.

### 2.1 Unified RBAC model
The original draft mixed numeric "levels" (Level 0/1) with named roles
(Volunteer Coordinator, Staff Member). Replace numeric levels with a
**roles + permissions** model (recommended: `spatie/laravel-permission`).

| Role (slug) | Description |
|---|---|
| `system_admin` | Full access, user management, org settings, AI reports |
| `field_officer` | Beneficiary registry, distributions, own tasks |
| `volunteer_coordinator` | Volunteer directory, shifts, attendance |
| `me_officer` | Tasks/Kanban, dashboard, report generation |

Middleware checks **permissions**, not a numeric level. Roles map to permission
sets and can be adjusted without code changes.

### 2.2 Audit logging
Every create/update/delete on beneficiaries, distributions, users and reports
must be logged (recommended: `spatie/laravel-activitylog`): actor, action,
entity, before/after, timestamp.

### 2.3 PII & security baseline
- Encrypt sensitive fields at rest (e.g., National ID).
- Enforce HTTPS for all traffic.
- Restrict who can read full National IDs; mask in lists.
- All list endpoints are paginated and scoped by role.

### 2.4 Web vs Mobile scope
- **Flutter (field):** beneficiary registration & search, distribution capture,
  attendance — must support **offline capture + sync** with idempotency keys.
- **Web (office):** user/org admin, volunteer & shift management, Kanban,
  dashboard, AI reports.

---

## 3. Module 1 — IT & Admin (قسم تقنية المعلومات)

**Focus:** Authentication, Authorization, Org settings.

### User Stories

| ID | Story |
|---|---|
| Auth-01 | As a user, I want to securely log in with email + password. |
| Auth-02 | As a System Admin, I want to create users and assign them a role. |
| Auth-03 | As a System Admin, I want to deactivate accounts so access is revoked immediately. |
| Auth-04 | As a Field Officer, I want to be blocked from admin pages I shouldn't see. |
| Auth-05 *(new)* | As a user, I want a "forgot password" flow to reset via email. |
| Auth-06 *(new)* | As an admin-created user, I want to set my password on first login (and verify email). |
| Auth-07 *(new)* | As a user, I want to log out and have my token invalidated. |
| Admin-01 | As a System Admin, I want to edit org profile (Name, Logo). |

### Technical Requirements

- **Tech-Auth-1:** Authentication via **Laravel Sanctum** (first-party SPA + mobile)
  *or* JWT (`tymon/jwt-auth`). If JWT is chosen, document token expiry + refresh +
  blacklist so Auth-03/Auth-07 actually revoke access.
- **Tech-Auth-2:** Migrations for `users`, `roles`, `permissions`, `role_user`.
- **Tech-Auth-3:** Permission-based middleware on all API routes (see §2.1).
- **Tech-Auth-4:** Account lockout after **5 consecutive failed logins** with a
  **15-minute cooldown**, implemented via Laravel `RateLimiter` backed by Redis
  (works across multiple app servers).
- **Tech-Auth-5:** Password reset tokens table + email notification.
- **Tech-Auth-6:** Soft-deactivate flag (`is_active`) checked on every request.

---

## 4. Module 2 — Field Operations (قسم العمليات الميدانية)

**Focus:** Beneficiary registry, AI deduplication, **and aid distribution** (added).

### User Stories

| ID | Story |
|---|---|
| Ben-01 | As a Field Officer, I want to register a beneficiary (full profile). |
| Ben-02 | As a Field Officer, I want to search by National ID or Phone. |
| Ben-03 | As a Field Officer, I want a warning when a highly similar Arabic name exists (e.g., "أحمد" vs "احمد"). |
| Ben-04 | As a Field Officer, when warned, I want to **Merge** or **Override** and create anyway. |
| Ben-05 *(new)* | As a Field Officer, National ID/Phone must be a hard uniqueness check (not just a soft warning). |
| Dist-01 *(new)* | As a Field Officer, I want to record an aid distribution (beneficiary, item/amount, date). |
| Dist-02 *(new)* | As an Admin, I want distributions aggregated for the weekly dashboard. |

### Beneficiary data model (expanded)
`full_name`, `normalized_name`, `national_id` (encrypted, unique), `phone` (unique),
`gender`, `date_of_birth`, `family_size`, `governorate/location`, `vulnerability_category`,
`status`, `registered_by`, `photo/docs`, timestamps, soft-delete.

### Technical Requirements

- **Tech-Ben-1:** Migrations + Eloquent models for `beneficiaries`.
- **Tech-Ben-2:** CRUD REST APIs with strict `FormRequest` validation (National ID
  length/format, phone format, required fields).
- **Tech-Ben-3 (AI dedup — reworked):**
  1. On write, compute and store a **`normalized_name`** (strip tatweel/diacritics;
     unify alef variants أ/إ/آ → ا, hamza, and taa marbuta ة → ه).
  2. Laravel **pre-filters candidates** (trigram block / phone / ID prefix) — never
     scan the whole table per request.
  3. Send candidate set + new name to the AI-Service, which returns similarity
     scores combining **normalization + token-set matching (name parts in any order)
     + a phonetic pass**, not raw Levenshtein alone.
  4. Threshold returns a "possible duplicate" list to the officer.
  - *Alternative:* use PostgreSQL `pg_trgm` similarity directly in Laravel and skip
    the network hop for dedup.
- **Tech-Ben-4:** AI-Service requires an **internal API key** for all requests.
- **Tech-Ben-5 (Merge):** Define merge semantics — choose a primary record, re-point
  all distributions/history to it, keep a merge audit trail, mark the merged record.
- **Tech-Dist-1:** Migrations for `distributions` (beneficiary_id, item/type, quantity,
  value, distributed_at, officer_id).

---

## 5. Module 3 — HR & Volunteer (قسم الموارد البشرية)

**Focus:** Volunteer directory, shift scheduling, attendance, contributed hours.

### User Stories

| ID | Story |
|---|---|
| Vol-01 | As a Coordinator, I want to add a volunteer (Name, Phone, Skills). |
| Vol-02 | As a Coordinator, I want to create a shift (Title, Date, Time, Required Capacity). |
| Vol-03 | As a Coordinator, I want to assign volunteers to shifts (respecting capacity). |
| Vol-04 | As a Coordinator, I want to mark attendance so hours are logged for present volunteers. |
| Vol-05 *(new)* | As a Coordinator, I want to see remaining capacity (X/Y filled) and be blocked from over-assigning. |
| Vol-06 *(new)* | As a Coordinator, I want to edit/cancel a shift and remove a volunteer. |
| Vol-07 *(new)* | As a Coordinator, I want to filter volunteers by skill when assigning. |

### Technical Requirements

- **Tech-Vol-1:** Migrations for `volunteers`, `shifts`, pivot `shift_volunteer`
  (many-to-many). Clarify whether a volunteer is also a `user` (login) or directory-only.
- **Tech-Vol-2:** APIs for the volunteer directory; **skills as structured tags**
  (many-to-many), not free text.
- **Tech-Vol-3:** APIs to assign volunteers and update attendance
  (enum: `pending`, `present`, `absent`). Enforce **Required Capacity** and prevent
  **overlapping-shift** assignment for the same volunteer.
- **Tech-Vol-4:** Observer/helper to tally `total_hours` when a volunteer is marked
  `present`. Define the hours source explicitly (shift end − start, or check-in/out).
- **Tech-Vol-5:** Shift `status` enum (`scheduled`, `completed`, `cancelled`).

---

## 6. Module 4 — M&E / Workflow & Reporting (قسم المتابعة والتقييم)

**Focus:** Task workflow (Kanban), live dashboard, AI-generated Arabic reporting.
*(Note: this module is workflow + reporting; true KPI/indicator M&E can be a fast-follow.)*

### User Stories

| ID | Story |
|---|---|
| Task-01 | As a Staff Member, I want to see my tasks on a Kanban board. |
| Task-02 | As a Staff Member, I want to drag tasks between To-Do / In Progress / Done. |
| Dash-01 | As an Admin, I want a live dashboard: total beneficiaries, active volunteers, total aid distributed this week. |
| AI-Report-01 | As an Admin, I want a button to generate a formal weekly Arabic narrative from dashboard stats. |
| AI-Report-02 *(new)* | As an Admin, I want to edit/regenerate the report before finalizing, then export to PDF/Word. |
| AI-Report-03 *(new)* | As an Admin, I want generated reports stored and auditable. |

### Technical Requirements

- **Tech-Task-1:** Migrations for `tasks` (title, description, status enum,
  assignee_id, **due_date, priority, created_by**) + **status-change history**
  (timestamps → enables cycle-time measurement).
- **Tech-Task-2:** Frontend state management for smooth drag-and-drop, syncing
  with the Laravel API.
- **Tech-Dash-1:** Optimized aggregation queries for weekly stats (define
  "active volunteer"; "live" = polling is acceptable for MVP). Depends on the
  `distributions` table (§4).
- **Tech-AI-1 (reworked):** AI-Service endpoint takes aggregated JSON from Laravel,
  builds a prompt **constrained to only the supplied figures (no invented data)**,
  calls the Claude API (or similar), returns Arabic text. Include **timeout handling,
  retries, and rate-limiting/caching** for cost control.
- **Tech-AI-2:** `reports` table (period, generated_by, content, status,
  created_at) so reports persist and can be exported.

---

## 7. Data Model Summary (MVP tables)

```
users, roles, permissions, role_user, permission_role
password_resets
organization_settings
beneficiaries            (normalized_name, encrypted national_id)
beneficiary_merges       (audit of merges)
distributions
volunteers, skills, skill_volunteer
shifts, shift_volunteer  (attendance enum, hours)
tasks, task_status_history
reports
activity_log             (audit)
```

---

## 8. Repository Structure (action required)

- [x] `MASAR-V-1/Back-End` — Laravel core API
- [x] `MASAR-V-1/Front-End-` — web SPA
- [x] `MASAR-V-1/Flutter` — field mobile app
- [ ] **`MASAR-V-1/AI-Service`** — Python FastAPI (dedup + reporting) — **create this**

---

## 9. Open Decisions (need a product call)

1. **Auth:** Sanctum vs JWT (impacts Auth-03/07 revocation strategy).
2. **DB:** PostgreSQL (`pg_trgm`) vs MySQL + Python dedup.
3. **Dedup location:** in-DB trigram vs AI-Service network call.
4. **Volunteers:** login users or directory-only records?
5. **Hours source:** shift duration vs check-in/check-out.
6. **M&E depth:** ship workflow-only now, add KPI/indicator + beneficiary feedback (CFM) later?
7. **Offline sync conflict policy** for the Flutter app.

---

## Appendix A — Arabic Name Normalization Rules (for dedup)

Apply on both storage (`normalized_name`) and at compare time:

- Remove tatweel (ـ) and diacritics (harakat).
- Unify alef: `أ`, `إ`, `آ` → `ا`.
- Unify taa marbuta: `ة` → `ه`.
- Unify yaa: `ى` → `ي`.
- Trim and collapse whitespace; lower internal casing for any Latin parts.
- Tokenize and compare as a set (name parts may appear in different order).
