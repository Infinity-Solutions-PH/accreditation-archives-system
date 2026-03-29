# Technical Stack Implementation Plan: VILT

The **VILT Stack** (Vue.js, Inertia.js, Laravel, Tailwind CSS) is perfectly suited for the IDO Archives System. It allows you to build a robust, single-page application (SPA) experience while leveraging the powerful, server-side capabilities of PHP for complex file routing and role-based permissions.

Here is how the system outline maps to the VILT architecture:

---

## I. Core Technologies

*   **Frontend (UI/Reactivity):** Vue.js 3 (Composition API)
*   **Routing & Data Bridging:** Inertia.js (Eliminates the need for a separate REST API)
*   **Backend & Core Logic:** Laravel 11/12 (PHP)
*   **Styling:** Tailwind CSS
*   **Database:** MySQL or PostgreSQL
*   **Storage:** Laravel Storage Facade (Local for testing, Cloudflare R2 for production)
*   **Authentication Starter Kit:** Laravel Breeze (Inertia/Vue stack) or Laravel Jetstream.

---

## II. Technical Implementation by Module

### 1. Authentication & Authorization (Laravel & Vue)
*   **Standard Login:** Utilize Laravel Breeze/Inertia scaffolding. It provides pre-built Vue components and Laravel controllers for secure session-based authentication.
*   **Accreditor Login (Temporary):** Use Laravel's `URL::temporarySignedRoute()` to generate secure, time-sensitive email links for Accreditors.
*   **OAuth:** Implement Laravel Socialite for Google Sign-in, restricting access to domains/emails present in the system's invitation database.
*   **RBAC (Role-Based Access Control):** 
    *   *Backend:* Create custom Laravel Middleware (e.g., `EnsureUserHasRole`) to protect specific routes.
    *   *Frontend:* Pass the authenticated user's role via Inertia's shared data (`HandleInertiaRequests` middleware) to conditionally render UI elements in Vue (e.g., hiding the "Admin Dashboard" link).

### 2. User Profile & Management
*   **Directory & Queues:** Build Vue components using Tailwind data tables to list users. Use Inertia links to send approval/rejection actions to Laravel controllers.
*   **State Management:** Since Inertia handles server-state, you generally won't need Pinia/Vuex unless managing complex, isolated frontend states (like an active drag-and-drop file queue).

### 3. File Storage & Drive Management
*   **File Handling:** Use the Laravel `Storage` facade.
    *   *My Drive & General Drive:* Store files in a private disk (`storage/app/private/colleges/{college_id}/...`).
*   **File Upload UI:** Build a Vue component using a drag-and-drop library (like Uppy or Vue-Dropzone) styled with Tailwind.
*   **Serving Secure Files:** Never expose file paths directly. Route file requests through a Laravel controller that checks user permissions before returning the file via `return response()->file($path);` or `Storage::download()`.

### 4. Accreditation Event Module (Virtual Drives)
*   **Event Creation:** A Vue form where IDO Staff inputs the event details. 
*   **Dynamic Folders:** When the event is saved in Laravel, automatically create the corresponding directory structures via the `Storage::makeDirectory()` method.
*   **"Share" Logic:** Instead of duplicating large files when "Sharing to Virtual Drive", store a database record mapping the original `file_id` to the `virtual_drive_id`.
*   **Expiration Tracking:** Use Laravel's Task Scheduling (`routes/console.php` or scheduled commands) to run a daily check that automatically updates the status of expired Accreditation Drives to "Archived" or "Locked".

### 5. Audit & Logging Module
*   **Activity Logs:** Use Eloquent Model Observers (e.g., `FileObserver`, `UserObserver`) to automatically record an entry into an `activity_logs` table whenever a file is uploaded, shared, or a user is approved.
*   **System Logs:** Rely on Laravel's built-in logging channel (`storage/logs/laravel.log`) or integrate a package like Telescope or LogViewer for Admin access directly within the Vue dashboard.

---

## III. Phased Development Plan (VILT Specific)

**Phase 1: Foundation & Authentication**
1. Initialize Laravel project and install Inertia/Vue via Laravel Breeze.
2. Set up the database migrations for `Users`, `Roles`, `Colleges`, and `Accreditation_Events`.
3. Create Laravel Middleware for the 5 distinct roles.
4. Set up Inertia shared data to pass auth state and roles to the Vue frontend.

**Phase 2: Core UI & User Management**
1. Design the main application layout in Vue using Tailwind CSS.
2. Build CRUD operations and Vue pages for Admin/IDO Staff to manage users.
3. Implement the CAO approval queue using Inertia forms.

**Phase 3: File System Integration**
1. Configure Laravel filesystems (local and preparation for cloud).
2. Build the "My Drive" and "General Drive" interfaces in Vue.
3. Implement secure file uploading, downloading, and deletion endpoints in Laravel.

**Phase 4: Accreditation & Virtual Drives**
1. Build the Vue interface for creating Accreditation Events.
2. Implement the database logic for linking existing files to the new Virtual Drive (Sharing).
3. Create the Accreditor view (read-only Vue components) and secure temporary route access.

**Phase 5: Polish & Auditing**
1. Implement Model Observers to populate the Activity Log.
2. Build the Activity Log data tables in Vue.
3. Refine UI, add loading states to Inertia requests, and optimize Tailwind bundles.