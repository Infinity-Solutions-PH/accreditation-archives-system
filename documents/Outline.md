# IDO Archives System

## I. System Outline

The system is logically divided into five core modules to handle requirements effectively.

### 1. Authentication & Authorization Module
* **Standard Login:** Email and password authentication for Admin, IDO Staff, College Accreditation Officer (CAO), and Taskforce Members.
* **Accreditor Login:** Temporary secure link via email or Google OAuth (restricted to registered/invited emails only).
* **Role-Based Access Control (RBAC):** Middleware to enforce strict permissions boundaries across the five distinct roles.

### 2. User Profile & Management Module
* **Directory:** A hub to view and manage users.
* **Approval Queue:** A dedicated section for the CAO to approve or reject pending Taskforce sign-ups for their specific college.
* **Profile Management:** Basic profile settings, password resets, and college affiliation tags.

### 3. File Storage & Drive Management Module
* **My Drive:** Isolated personal storage for Taskforce Members to prepare and store rough drafts.
* **General Drive:** College-specific collaborative storage for Taskforce Members and the CAO.
* **Virtual Accreditation Drive:** A time-sensitive, read-only (for Accreditors) drive containing specific "Area Folders" dynamically generated per accreditation visit.
* **File Actions:** Upload (documents, videos), create folders, rename, delete, and "Share to Virtual Drive".

### 4. Accreditation Event Module
* **Event Creation:** Form for IDO Staff/Admin to create an accreditation visit, select the target program, define the "Area Folders," and set the expiration date.
* **Notification Engine:** Automated alerts triggered upon event creation to notify the relevant CAO and Taskforce Members.
* **Access Provisioning:** Interface to generate and manage Accreditor access credentials and extend drive expiration dates if needed.

### 5. Audit & Logging Module
* **Activity Logs:** Tracks file uploads, user approvals, and file sharing (accessible to IDO Staff and Admin).
* **System Logs:** Tracks technical errors, login failures, and deep system changes (Admin only).

---

## II. Access & Permissions Matrix

| Feature | Admin | IDO Staff | CAO | Taskforce | Accreditor |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **Manage System Settings** | Yes | No | No | No | No |
| **View System Logs** | Yes | No | No | No | No |
| **View Activity Logs** | Yes | Yes | No | No | No |
| **Manage Users** | All | All (except Admin) | College Only | No | No |
| **Assign CAO Role** | Yes | Yes | No | No | No |
| **Create Accreditation Drive**| Yes | Yes | No | No | No |
| **Upload to General/My Drive**| Yes | Yes | Yes | Yes | No |
| **Share to Virtual Drive** | Yes | Yes | Yes | Yes | No |
| **View Virtual Drive Files** | Yes | Yes | Yes | Yes | Yes (Until Expiry) |

---

## III. Core System Workflows

### Workflow 1: User Onboarding (Taskforce)
1. Taskforce Member registers on the platform, selecting their specific College.
2. The account is placed in a "Pending Verification" state.
3. System notifies the CAO of that specific college.
4. CAO logs in, reviews the request, and clicks "Approve."
5. Taskforce Member receives an approval email and gains access to their "My Drive" and the college's "General Drive."

### Workflow 2: Accreditation Preparation Phase
1. IDO Staff creates a new "Accreditation Record" for a specific college program, sets the required Accreditation Areas (folders), and sets an expiry date.
2. System automatically generates the Virtual Accreditation Drive.
3. System sends an automated notification to the CAO and Taskforce Members of that college.
4. Taskforce Members upload their preparation documents to their "My Drive" or the "General Drive."
5. Taskforce Members select finalized files and use the "Share to Virtual Drive" action, mapping them to the specific Area Folders created by the IDO Staff.

### Workflow 3: Accreditor Review Phase
1. IDO Staff generates an access invitation for the Accreditor assigned to the specific Virtual Drive.
2. Accreditor receives a secure link via email.
3. Accreditor clicks the link and authenticates via temporary token or Google Auth.
4. Accreditor views the populated Area Folders within the Virtual Accreditation Drive.
5. Upon the predefined expiration date, the system automatically revokes the Accreditor's access.
6. If an extension is needed, IDO Staff manually extends the drive's expiration date, restoring access.

---

## IV. Development Plan

### Phase 1: Architecture & Auth Foundation
* Set up the database schema (Users, Colleges, Drives, Files, Logs).
* Implement Google OAuth and secure temporary link generation.
* Develop the Role-Based Access Control (RBAC) middleware to enforce role boundaries.

### Phase 2: User Management & Workflows
* Build the Admin and IDO Staff dashboards.
* Develop the user registration flow and the CAO approval queue.
* Implement the automated email notification system for account status updates.

### Phase 3: File System Implementation
* Integrate cloud storage (e.g., AWS S3, Google Cloud Storage) for handling documents and large video files.
* Build the UI for "My Drive" and "General Drive" (folder creation, file uploading, renaming).
* Ensure users are properly restricted to their designated college drives.

### Phase 4: Virtual Accreditation Drives
* Build the Accreditation Event creation tool for IDO Staff (Area folder generation, expiration timers).
* Implement the "Share to Drive" functionality for Taskforce members to bridge files from General/Personal to Virtual.
* Build the read-only, time-restricted view for the Accreditors.

### Phase 5: Auditing, Polish, and Deployment
* Implement comprehensive system and activity logging.
* Conduct end-to-end testing with all five roles simulating a real accreditation event.
* Finalize UI/UX design and deploy.