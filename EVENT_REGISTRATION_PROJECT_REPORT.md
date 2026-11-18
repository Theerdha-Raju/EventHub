# PROJECT REPORT

## EventHub: Modern Event Registration & Management System
**Course / Domain:** Full-Stack Web Development & Database Management Systems  
**Tech Stack:** PHP 8.0+ (Backend), MySQL / MariaDB (Database), HTML5, CSS3, Vanilla JavaScript (Frontend)  
**Academic Year:** 2026 – 2027  
**Document Status:** Complete Production & Academic Submission Report  

---

### Submitted To:
**Faculty In-Charge / Project Evaluation Committee**  
Department of Computer Science & Engineering / Computer Applications  

### Submitted By:
**Student Name:** [Your Name]  
**Roll No / Register No:** [Your Roll No]  
**Class / Semester:** MCA / B.Tech / BCA  
**Academic Institution:** [Your College / University Name]  

---

## Table of Contents
1. [Introduction](#1-introduction)
2. [Project Title, Domain & Problem Statement](#2-project-title-domain--problem-statement)
3. [System Analysis & Requirement Specifications](#3-system-analysis--requirement-specifications)
4. [System Architecture & Workflow Design](#4-system-architecture--workflow-design)
5. [Database Design & Data Dictionary](#5-database-design--data-dictionary)
6. [Functional Modules & Implementation Walkthrough](#6-functional-modules--implementation-walkthrough)
7. [Security Architecture & Threat Mitigation](#7-security-architecture--threat-mitigation)
8. [Testing & Quality Assurance Matrix](#8-testing--quality-assurance-matrix)
9. [User Manual & Step-by-Step Deployment Guide](#9-user-manual--step-by-step-deployment-guide)
10. [Sample Simple Events Catalog](#10-sample-simple-events-catalog)
11. [Conclusion & Future Enhancements](#11-conclusion--future-enhancements)

---

## 1. Introduction

In modern educational institutions, corporate organizations, and community groups, events serve as critical hubs for skill development, cultural exchange, athletic competitions, and collaborative learning. Traditional approaches to event coordination rely on manual sign-up sheets, paper slips, or unstructured spreadsheets. These outdated mechanisms suffer from severe limitations:
- **High Friction:** Prospective attendees must physically locate coordinators or fill out repetitive forms.
- **Accidental Duplication:** Lack of validation permits participants to register multiple times with the same email.
- **Data Entry Errors:** Illegible handwriting or malformed email addresses result in lost notifications.
- **Lack of Confirmation:** Attendees leave without verifiable tickets or confirmation receipts.
- **Inefficient Administrative Control:** Organizers lack an instant, centralized dashboard to monitor registration counts, inspect attendee details, and safely purge test or cancelled records.

The **EventHub** web application was engineered to address these challenges through a lightweight, robust, and dependency-free full-stack software architecture:
- **Backend:** Native PHP 8 utilizing PHP Data Objects (PDO) with parameterized prepared statements, secure session handling, and Bcrypt cryptographic hashing.
- **Frontend:** Semantic HTML5, modern custom CSS3 (featuring responsive card grids and print-optimized stylesheets), and vanilla JavaScript delivering real-time validation and instant live search filtering.
- **Database:** Relational MySQL / MariaDB database with foreign-key constraints enforcing referential integrity.

---

## 2. Project Title, Domain & Problem Statement

### 2.1 Project Title
**EventHub: Modern Event Registration & Management System**

### 2.2 Domain
**Full-Stack Web Engineering, Information Systems & Relational Database Management**

### 2.3 Problem Statement
To design and build a responsive, user-friendly, and secure web application where users can seamlessly browse upcoming events, register with instantaneous validation, and receive an immediate printable confirmation summary, while providing event administrators with a password-protected portal to review, search, inspect, and manage all attendee records.

### 2.4 Project Objectives
1. **Dynamic Catalog:** Display upcoming events (title, formatted date, location, category, seats, and description) fetched dynamically from a MySQL database.
2. **Context-Aware Registration:** Auto-fill and lock the chosen event when navigating from the event catalog, while allowing dropdown selection when accessed directly.
3. **Double-Layer Validation:** Implement client-side JavaScript validation for instant visual cues (preventing empty inputs and invalid email patterns) backed by strict server-side PHP validation (checking email syntax, sanitizing inputs, and blocking duplicate registrations).
4. **Instant Printable Receipts:** Deliver a confirmation page featuring a unique reference code (`#EVT-000001`), attendee credentials, full event details, and print styling that eliminates web headers and buttons.
5. **Administrative Back-Office:** Provide secure admin authentication (`password_verify`), live dashboard statistics, search-filterable table with the exact columns: `ID`, `Name`, `Email`, `Event`, `Date Registered`, `Action (View, delete)`, interactive modal view, and CSRF-guarded deletion.
6. **Zero-Friction Deployment:** Supply an automated 1-click database setup and migration utility (`setup.php`) requiring no manual SQL imports.

---

## 3. System Analysis & Requirement Specifications

### 3.1 Functional Requirements
- **FR-01 (Events Discovery):** The system shall fetch and display all active upcoming events ordered by date.
- **FR-02 (Live Search):** The homepage shall provide an instantaneous client-side filter updating the card grid as the user types.
- **FR-03 (Direct Registration Link):** Each event card shall feature a "Register Now" button linking to `register.php?event_id=X`.
- **FR-04 (Event Auto-Fill):** The registration form shall detect `event_id` in the URL, fetch event metadata, and display the event title in an auto-filled readonly view.
- **FR-05 (Client Validation):** The frontend shall validate that Name is &ge; 2 characters and Email matches standard RFC regex before submitting.
- **FR-06 (Server Validation & Sanitization):** The PHP backend shall trim inputs, validate email via `filter_var()`, check for duplicate registrations, and sanitize outputs against XSS.
- **FR-07 (Data Persistence):** Valid registrations shall be inserted into the MySQL `registrations` table using PDO prepared statements.
- **FR-08 (Confirmation Summary):** The system shall redirect to `success.php`, presenting the registration reference, attendee information, event date/venue, and printable ticket layout.
- **FR-09 (Admin Authentication):** The admin portal shall require username and password verification against Bcrypt hashes in the `admins` table.
- **FR-10 (Admin Dashboard & Registrations Table):** The admin page shall render KPI metrics and a table containing: `ID`, `Name`, `Email`, `Event`, `Date Registered`, and `Action (View, delete)`.
- **FR-11 (Attendee Inspection Modal):** Clicking "View" shall display attendee credentials and event schedule in a modal dialog.
- **FR-12 (Safe Record Deletion):** Clicking "Delete" shall require user confirmation and validate a session CSRF token before removing the database row.

### 3.2 Non-Functional Requirements
- **Security:** Complete protection against SQL Injection (parameterized queries), XSS (`htmlspecialchars`), CSRF tokens, and session fixation defense (`session_regenerate_id`).
- **Performance:** Lightweight execution with zero external CSS/JS framework overhead. Pages render in &lt; 50ms locally.
- **Usability & Responsiveness:** Clean modern typography, intuitive color-coded badges, and mobile-friendly layouts (adapting from 320px mobile screens to desktop monitors).
- **Maintainability:** Modular architecture separating database configuration (`config/db.php`), helper functions (`includes/functions.php`), presentation templates, and administrative scripts.

### 3.3 Hardware & Software Requirements
- **Operating System:** Windows 10 / 11, Linux (Ubuntu/Debian), or macOS
- **Web Server:** Apache 2.4+ (XAMPP / WAMP / LAMP) or PHP 8 CLI Built-in Server
- **Server-Side Engine:** PHP 8.0 or higher (with `pdo`, `pdo_mysql`, `mysqli`, `session`, `mbstring` enabled)
- **Database Server:** MySQL 5.7+ or MariaDB 10.4+ (InnoDB engine)
- **Client Web Browser:** Google Chrome, Mozilla Firefox, Microsoft Edge, or Safari

---

## 4. System Architecture & Workflow Design

### 4.1 High-Level Architecture
The application is structured into four interconnected tiers:
1. **Client Tier (Presentation):** HTML5 semantic markup, CSS3 stylesheets (with print media queries), and vanilla JavaScript handling client-side DOM manipulation and validation.
2. **Security & Routing Tier:** CSRF token verification, session authentication guards (`require_admin()`), and input sanitization filters.
3. **Application Logic Tier (PHP Backend):** Controller logic handling requests, verifying data integrity, calculating date formats, and generating tokens.
4. **Persistence Tier (MySQL):** Relational tables storing events, attendee registrations, and admin credentials.

### 4.2 System Workflows

#### Attendee Registration Workflow:
```
[User on Homepage] 
       │
       ▼
[Browse Events / Live Search] ──► [Click "Register Now"]
                                           │
                                           ▼
                                 [register.php opens]
                              (Event Auto-Filled & Locked)
                                           │
                                           ▼
                                 [Fill Name & Email]
                                           │
                                           ▼
                                 [Client-side Validation]
                                    ├─ Invalid ──► [Show Red Inline Error]
                                    └─ Valid
                                           │
                                           ▼
                                 [POST to PHP Backend]
                                           │
                                           ▼
                                 [Server-side Validation]
                                    ├─ Duplicate / Invalid ──► [Return Error Banner]
                                    └─ Valid
                                           │
                                           ▼
                                 [INSERT into registrations]
                                           │
                                           ▼
                                 [Redirect to success.php]
                                           │
                                           ▼
                                 [View / Print Confirmation]
```

#### Administrator Management Workflow:
```
[Admin visits admin/login.php]
       │
       ▼
[Submit Username & Password]
       │
       ▼
[Verify Bcrypt Hash in DB]
  ├─ Invalid ──► [Display Error Alert]
  └─ Valid
       │
       ▼
[Regenerate Session ID & Set Auth State]
       │
       ▼
[Redirect to admin/index.php]
       │
       ├─► [View KPI Stat Cards: Total Reg, Events, Unique Attendees]
       ├─► [Live Filter Registrations Table by Name/Email/Event]
       ├─► [Click "View" ──► Open Attendee Modal Summary]
       ├─► [Click "Delete" ──► Confirm Prompt ──► CSRF Check ──► DB Row Deleted]
       └─► [Click "Sign Out" ──► Destroy Session ──► Redirect to Login]
```

---

## 5. Database Design & Data Dictionary

The database `event_management` consists of three normalized tables in 3rd Normal Form (3NF).

### 5.1 Entity-Relationship (ER) Representation
- An **Event** can have **zero or many** registrations (1 : N).
- A **Registration** belongs to **exactly one** event.
- If an event is deleted, all associated registrations are automatically deleted via `ON DELETE CASCADE`.

### 5.2 Table: `events`
Stores details of scheduled upcoming events.

| Field Name | Data Type | Key / Constraint | Default Value | Description |
|---|---|---|---|---|
| `id` | `INT` | PRIMARY KEY, AUTO_INCREMENT | None | Unique event numeric ID |
| `title` | `VARCHAR(150)` | NOT NULL | None | Headline / title of the event |
| `description` | `TEXT` | NOT NULL | None | Comprehensive agenda and details |
| `event_date` | `DATETIME` | NOT NULL | None | Scheduled date and start time |
| `location` | `VARCHAR(150)` | NOT NULL | None | Campus venue, ground, or hall |
| `category` | `VARCHAR(50)` | NOT NULL | `'General'` | Badge category (Sports, Cultural, etc.) |
| `max_seats` | `INT` | NOT NULL | `100` | Attendee seat capacity |
| `created_at` | `TIMESTAMP` | NOT NULL | `CURRENT_TIMESTAMP` | Record creation timestamp |

### 5.3 Table: `registrations`
Stores individual attendee registrations linked to events.

| Field Name | Data Type | Key / Constraint | Default Value | Description |
|---|---|---|---|---|
| `id` | `INT` | PRIMARY KEY, AUTO_INCREMENT | None | Registration reference code |
| `event_id` | `INT` | FOREIGN KEY &rarr; `events.id` (CASCADE) | None | ID of the registered event |
| `name` | `VARCHAR(100)` | NOT NULL | None | Full name of the attendee |
| `email` | `VARCHAR(150)` | NOT NULL | None | Validated email address |
| `registration_date` | `TIMESTAMP` | NOT NULL | `CURRENT_TIMESTAMP` | Date and time of registration |

### 5.4 Table: `admins`
Stores authorized administrative login accounts.

| Field Name | Data Type | Key / Constraint | Default Value | Description |
|---|---|---|---|---|
| `id` | `INT` | PRIMARY KEY, AUTO_INCREMENT | None | Unique admin identifier |
| `username` | `VARCHAR(50)` | UNIQUE, NOT NULL | None | Admin username (e.g. `admin`) |
| `password_hash` | `VARCHAR(255)` | NOT NULL | None | Native Bcrypt hashed password |
| `full_name` | `VARCHAR(100)` | NOT NULL | `'Administrator'` | Admin display name |
| `created_at` | `TIMESTAMP` | NOT NULL | `CURRENT_TIMESTAMP` | Account creation timestamp |

---

## 6. Functional Modules & Implementation Walkthrough

### 6.1 Database Connection (`config/db.php`)
Establishes a centralized PDO database connection instance. Configures `PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION` and `PDO::ATTR_EMULATE_PREPARES => false`. Reads credentials with seamless fallback for both local XAMPP (`localhost`, `root`, blank password) and cloud platforms via environment variables (`DB_HOST`, `DB_USER`, `DB_PASS`, `DB_NAME`, `DATABASE_URL`). Automatically redirects to `setup.php` if the database does not exist.

### 6.2 Common Helpers (`includes/functions.php`)
Provides reusable modular functions across the application:
- `init_session()`: Starts sessions securely with `HttpOnly` and `SameSite` flags.
- `sanitize($data)`: Wraps `htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8')`.
- `csrf_token()` & `verify_csrf_token($token)`: Generates and validates cryptographic session tokens.
- `is_admin_logged_in()` & `require_admin()`: Authentication guards protecting the admin portal.
- `format_event_date($datetimeStr)`: Converts SQL datetimes into formatted strings (e.g. *Thu, Dec 10, 2026 at 9:00 AM*).
- `get_upcoming_events($pdo)`, `get_all_registrations($pdo)`, `delete_registration($pdo, $id)`: Database query abstractions.

### 6.3 Homepage (`index.php`)
Renders the upcoming events grid. Queries `events` ordered by `event_date ASC`. Each card displays category, date, venue, capacity, description, and the "Register Now" button. Houses the `#eventSearch` input hooked to `assets/js/main.js` for instant live filtering without page reloads.

### 6.4 Registration Form (`register.php`)
Features the required fields: **Name**, **Email**, and **Event (Auto-Filled)**.
- If invoked via an event card, the event title is displayed in a clean readonly input with an overview banner.
- If accessed directly, renders a dropdown selector.
- Enforces dual validation: client-side JavaScript alerts on typing, and server-side PHP validation checks non-empty values, valid email syntax, and duplicate submissions.
- On success, inserts the record into MySQL and redirects to `success.php`.

### 6.5 Confirmation Summary (`success.php`)
Displays the confirmation card with checkmark badge, reference ID (e.g. `#EVT-000001`), attendee name, email, event title, date, venue, and registration timestamp. Includes a "Print / Save Confirmation" button utilizing `@media print` CSS to generate clean paper or PDF receipts without web navigation headers.

### 6.6 Admin Authentication (`admin/login.php` & `admin/logout.php`)
Provides the login screen. Validates input credentials using `password_verify()`. Regenerates session tokens upon login (`session_regenerate_id(true)`) to defeat session fixation attacks.

### 6.7 Admin Registrations Dashboard (`admin/index.php` & `admin/delete.php`)
Guarded by `require_admin()`. Features top KPI statistic cards (Total Registrations, Active Events, Unique Attendees), an instant search bar (`#adminTableSearch`), and the complete table with the required columns:
- **ID**
- **Name**
- **Email**
- **Event**
- **Date Registered**
- **Action (View, delete)**:
  - *View:* Triggers an interactive modal dialog presenting attendee and event details.
  - *Delete:* CSRF-protected form with a JavaScript confirmation dialog.

### 6.8 1-Click Database Setup & Seeder (`setup.php`)
Automates database creation, builds all tables, and seeds the simple events and admin account in one click.

---

## 7. Security Architecture & Threat Mitigation

| Vulnerability | Threat Description | Mitigation in EventHub |
|---|---|---|
| **SQL Injection (SQLi)** | Attacker injects SQL syntax to alter queries or extract data | 100% of database interactions use PDO Prepared Statements with bound parameters. Zero dynamic SQL concatenation. |
| **Cross-Site Scripting (XSS)** | Attacker inputs malicious JavaScript to execute in other users' browsers | All dynamic user and database outputs are escaped via `htmlspecialchars(..., ENT_QUOTES, 'UTF-8')`. |
| **CSRF Attacks** | Unauthorized commands transmitted from a trusted user | 64-character cryptographic tokens generated in session and verified on all state-altering POST requests. |
| **Credential Theft** | Password database compromised via leaks or rainbow tables | Passwords are never stored in plaintext; hashed using native Bcrypt (`password_hash()`) and verified via `password_verify()`. |
| **Session Hijacking / Fixation** | Attacker intercepts or fixes session identifier | Session cookies enforce `HttpOnly` and `SameSite=Lax`. Session ID is automatically regenerated upon login. |
| **Broken Access Control** | Unauthorized visitor directly accesses administrative pages | Centralized `require_admin()` guard redirects unauthorized requests immediately to `admin/login.php`. |

---

## 8. Testing & Quality Assurance Matrix

| Test ID | Module | Scenario & Inputs | Expected Result | Actual Result | Status |
|---|---|---|---|---|---|
| **TC-01** | Setup | Open `setup.php` and click Initialize | Database, tables created; events and admin seeded | All 3 tables and 5 events created | **PASS** |
| **TC-02** | Home | Open `index.php` with populated database | Display 5 event cards with date, venue, category & button | All cards rendered with correct badges and links | **PASS** |
| **TC-03** | Home | Type "Sports" or "Music" into `#eventSearch` | Instant client-side filtering of event cards | Real-time hiding of non-matching cards | **PASS** |
| **TC-04** | Register | Click "Register Now" on Event #1 (Sports) | `register.php` auto-fills and locks Event #1 | Event #1 pre-filled and locked in readonly mode | **PASS** |
| **TC-05** | Register | Submit form with empty Name and Email | JavaScript prevents submission; highlights inputs in red | Inputs highlighted red; error text displayed | **PASS** |
| **TC-06** | Register | Submit malformed email format (e.g. `user@bad`) | Server rejects submission; displays validation alert | Form reloads with validation alert; no DB insert | **PASS** |
| **TC-07** | Register | Submit same email and event twice | Duplicate check detects existing row; displays warning | Friendly alert: "You are already registered" | **PASS** |
| **TC-08** | Register | Submit valid name and email | Record saved to MySQL; redirected to `success.php` | Row saved in `registrations`; redirected | **PASS** |
| **TC-09** | Success | View confirmation page and click Print | Summary rendered; print view strips navigation | Ticket displayed; print preview clean | **PASS** |
| **TC-10** | Admin | Direct URL access to `admin/index.php` | Unauthorized visitor redirected to `admin/login.php` | Automatically redirected to login page | **PASS** |
| **TC-11** | Admin | Submit credentials `admin` / `admin123` | Validates hash, creates session, opens dashboard | Dashboard rendered with admin welcome banner | **PASS** |
| **TC-12** | Admin | Inspect registrations table columns | Contains `ID`, `Name`, `Email`, `Event`, `Date`, `Action` | All 6 required columns present and formatted | **PASS** |
| **TC-13** | Admin | Click "View" button on registration row | Interactive modal dialog opens with attendee details | Modal opens with full attendee & event info | **PASS** |
| **TC-14** | Admin | Click "Delete" button and confirm prompt | CSRF checked; record purged; success banner shown | Row removed from MySQL; table updated | **PASS** |

---

## 9. User Manual & Step-by-Step Deployment Guide

### 9.1 Local XAMPP Setup (Recommended)
1. **Start Services:** Open the **XAMPP Control Panel** and click **Start** for **Apache** and **MySQL**.
2. **Initialize Database:** Open your browser to:
   ```
   http://localhost/event-registration/setup.php
   ```
   Click **"🚀 Initialize Database & Load Events"**.
3. **Browse Upcoming Events:**
   ```
   http://localhost/event-registration/index.php
   ```
4. **Register for an Event:** Click any event's **"Register Now"** button or visit:
   ```
   http://localhost/event-registration/register.php
   ```
5. **Admin Portal:**
   ```
   http://localhost/event-registration/admin/login.php
   ```
   - **Username:** `admin`
   - **Password:** `admin123`

---

## 10. Sample Simple Events Catalog

The application comes pre-loaded with simple, relatable events:

| # | Event Title | Category | Venue / Location | Scheduled Date | Max Seats | Description |
|---|---|---|---|---|---|---|
| **1** | 🏆 **Annual Sports Day 2026** | **Sports** | College Sports Ground | Dec 10, 2026 &bull; 9:00 AM | 150 | Athletic races, cricket matches, football tournament, and indoor games with medals and certificates. |
| **2** | 🎵 **College Music & Dance Night** | **Cultural** | Main Campus Auditorium | Dec 15, 2026 &bull; 5:30 PM | 300 | Live student acoustic bands, solo singing, dance performances, and food stalls. |
| **3** | 💻 **Web Design & Coding Contest** | **Coding** | Computer Lab 2 | Dec 22, 2026 &bull; 10:30 AM | 50 | A beginner-friendly 2-hour coding challenge. Build a creative webpage using HTML, CSS, and JavaScript. |
| **4** | 📸 **Smartphone Photography Workshop** | **Workshop** | Seminar Hall B | Jan 08, 2027 &bull; 2:00 PM | 60 | Learn tips and tricks to take high-quality photos, portraits, and edit short video reels using your mobile phone. |
| **5** | ❤️ **Blood Donation & Free Health Camp** | **Community** | Student Activity Center | Jan 15, 2027 &bull; 9:30 AM | 200 | Free basic health checkup, blood pressure check, and voluntary blood donation drive with the Red Cross. |

---

## 11. Conclusion & Future Enhancements

The **EventHub** Event Registration and Management System successfully demonstrates a complete full-stack web application adhering to all mandatory technical constraints and functional deliverables. The codebase provides a production-grade demonstration of:
- Safe, parameterized database operations in PHP via PDO.
- Modern responsive layout design with CSS Grid, Flexbox, and print media queries.
- Instant client-side validation and search filtering using vanilla JavaScript.
- Robust security architecture mitigating common web application vulnerabilities (SQLi, XSS, CSRF, session fixation).

### Potential Future Enhancements:
1. **Automated Email Confirmations:** Integrating PHPMailer or SendGrid to dispatch transactional confirmation emails with calendar `.ics` invites.
2. **QR Code Attendance Verification:** Generating dynamic QR codes on confirmation receipts to allow one-scan ticket check-in at event entrances.
3. **Event Creation Portal:** Allowing administrators to create and edit events directly from the admin panel interface.
4. **Payment Gateway Integration:** Supporting paid ticket tiers via Razorpay, Stripe, or PayPal for paid conferences.
