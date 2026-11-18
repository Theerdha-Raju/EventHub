# 🎟️ EventHub - Modern Event Registration & Management System

A full-stack, responsive PHP and MySQL web application designed for browsing upcoming events, registering attendees with real-time validation, generating instant confirmation summaries, and providing a password-protected administrative back-office to manage registrations.

---

## 🛠️ Mandatory Tech Stack
- **Backend:** PHP (PHP 7.4 / 8.0+ compatible, PDO, Prepared Statements, Secure Sessions, CSRF protection, Bcrypt hashing)
- **Frontend:** HTML5, Modern Responsive CSS3 (Glassmorphism & Card Design, Custom Theme, Print Stylesheet), Vanilla JavaScript (No heavy frameworks required)
- **Database:** MySQL / MariaDB (UTF-8 `utf8mb4_unicode_ci`, Foreign Keys with Cascade)

---

## 🌟 Core Functionalities & Features

### 1. 🏠 Homepage (`index.php`)
- **Upcoming Events List:** Displays all active upcoming events fetched directly from MySQL.
- **Rich Event Cards:** Features event title, category badge, formatted date & time (e.g. *Sat, Dec 5, 2026 at 9:30 AM*), venue/location, seat availability, and descriptive summary.
- **Live Search & Filter:** Instant real-time filtering by keyword, topic, or venue via JavaScript.
- **Direct Registration:** Each card includes a prominent **"Register Now &rarr;"** button linking directly to the registration form with the event pre-selected and auto-filled.

### 2. ✍️ Registration Form (`register.php`)
- **Required Fields:**
  - **Full Name:** Text field, min 2 characters, trimmed and sanitized.
  - **Email Address:** RFC-compliant email field with regex and `filter_var` validation.
  - **Event (Auto-Filled):** Automatically populates and locks the event title when clicked from the homepage. Also supports an intuitive dropdown selector when accessed directly.
- **Double-Layer Validation:**
  - **Client-Side (JS):** Real-time feedback on input and blur events, preventing empty inputs or invalid email structures before network transfer.
  - **Server-Side (PHP):** Strict verification, prevents duplicate submissions (same attendee email registering twice for the same event), XSS sanitization, and CSRF token verification.
- **Database Persistence:** Inserts valid registrations into the `registrations` table and securely redirects to the confirmation page.

### 3. 🎉 Success Page (`success.php`)
- **Confirmation Message:** Celebratory badge and personalized greeting.
- **Registration Reference:** Formatted reference code (e.g., `#EVT-000001`).
- **Complete Details:** Attendee Full Name, Email, Event Name, Event Date & Time, Venue/Location, and Timestamp.
- **Print / Save Ticket:** One-click print button formatted with dedicated `@media print` CSS for generating receipts or PDFs.

### 4. 🛡️ Admin Portal (`admin/`)
- **Admin Authentication (`admin/login.php`):**
  - Secure session-based authentication with `password_verify()` against bcrypt hashes.
  - Session regeneration on login to prevent session fixation.
  - **Default Credentials:**
    - **Username:** `admin`
    - **Password:** `admin123`
- **Registrations Table (`admin/index.php`):**
  - **Key Metrics:** Stat cards displaying total registrations, active events, and unique attendees.
  - **Instant Search:** Live table search filtering across registrant names, emails, and event titles.
  - **Required Columns:**
    1. **ID:** Registration unique identifier.
    2. **Name:** Attendee's full name.
    3. **Email:** Clickable email address.
    4. **Event:** Styled badge displaying the event title.
    5. **Date Registered:** Formatted date and time.
    6. **Action (View, delete):**
       - **👁️ View:** Opens an interactive modal dialog displaying complete registration and event breakdown.
       - **🗑️ Delete:** Form-based deletion guarded with CSRF protection and a JavaScript confirmation dialog.
- **Admin Actions:**
  - `admin/delete.php`: Deletes the record safely and redirects back with a flash alert.
  - `admin/logout.php`: Destroys admin session safely.

### 5. ⚙️ Automated Database Setup & Seeder (`setup.php`)
- **1-Click Web Installer:** Automatically connects to MySQL, creates the `event_management` database, executes the table schemas, seeds 5 rich sample events, and creates the admin account.

---

## 📁 Project Directory Structure

```
event-registration/
│
├── config/
│   └── db.php                  # PDO database connection, base URL handler & error fallback
│
├── includes/
│   ├── header.php              # Global navigation bar & flash alert handler
│   ├── footer.php              # Global footer & scripts
│   └── functions.php           # Helper functions (CSRF, sanitization, dates, queries)
│
├── assets/
│   ├── css/
│   │   └── style.css           # Complete modern CSS design with print styles
│   └── js/
│       ├── main.js             # Client-side validation & live event search
│       └── admin.js            # Admin table filter, modal view & delete confirmation
│
├── admin/
│   ├── login.php               # Admin login screen
│   ├── logout.php              # Admin logout handler
│   ├── index.php               # Admin registrations table (ID, Name, Email, Event, Date, Action)
│   └── delete.php              # CSRF-protected registration delete action
│
├── index.php                   # Homepage with list of upcoming events
├── register.php                # Registration form with auto-filled event
├── success.php                 # Registration confirmation & summary receipt
├── setup.php                   # 1-Click database setup & seeder
├── database.sql                # Raw SQL schema & seed script
└── README.md                   # Complete documentation
```

---

## 🚀 Quickstart & How to Run

### Method 1: Using XAMPP (Recommended)
1. **Start XAMPP:**
   - Open **XAMPP Control Panel**.
   - Click **Start** for **Apache** and **MySQL**.
2. **Access in Browser:**
   - The project is already placed in your XAMPP web root:
     `C:\xampp\htdocs\event-registration\`
   - Open your browser to:
     ```
     http://localhost/event-registration/setup.php
     ```
   - Click **"🚀 Initialize Database Now"** (creates database, tables, and seed data automatically).
3. **Explore the Website:**
   - **Public Homepage:** `http://localhost/event-registration/index.php`
   - **Register an Attendee:** Click any event's "Register Now" button or visit `http://localhost/event-registration/register.php`
   - **Admin Portal:** `http://localhost/event-registration/admin/login.php`
     - **Username:** `admin`
     - **Password:** `admin123`

---

### Method 2: Manual Database Import via phpMyAdmin
If you prefer importing the SQL manually:
1. Open **phpMyAdmin** (`http://localhost/phpmyadmin`).
2. Click the **Import** tab.
3. Select `database.sql` from the `event-registration` folder.
4. Click **Go** / **Import**.

---

### Method 3: Using PHP Built-in Server
If running standalone:
```bash
cd "C:\xampp\htdocs\event-registration"
"C:\xampp\php\php.exe" -S localhost:8000
```
Then visit `http://localhost:8000/setup.php` in your browser.

---

## 🔒 Security Highlights
- **Prepared Statements (PDO):** Zero raw SQL interpolation protects against SQL injection.
- **XSS Prevention:** All user inputs and database outputs are escaped using `htmlspecialchars()`.
- **CSRF Protection:** State-changing requests (Registration submission and Admin Deletion) require session CSRF tokens.
- **Bcrypt Password Storage:** Admin passwords hashed using PHP's native `password_hash()` and verified via `password_verify()`.
