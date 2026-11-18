# 🎟️ EventHub — Event Registration & Management System

A full-stack **College Event Registration & Management System** built with a clean separation between Frontend and Backend.

---

## 🛠️ Tech Stack

| Layer        | Technologies Used              |
|--------------|-------------------------------|
| **Frontend** | HTML5 &bull; CSS3 &bull; JavaScript (Vanilla) |
| **Backend**  | PHP 8.x                        |
| **Database** | MySQL                          |
| **Server**   | Apache (XAMPP / LAMP / WAMP)   |
| **Database** | MySQL                          |

---

## 📁 Project Structure

```
event-registration/
│
├── frontend/                    ← Pure HTML + CSS + JS (no PHP needed)
│   ├── index.html               ← Browse events with live search
│   ├── register.html            ← Registration form with JS validation
│   ├── success.html             ← Printable confirmation receipt
│   └── admin/
│       ├── login.html           ← Admin login (frontend demo)
│       └── dashboard.html       ← Admin dashboard with table, modals, filters
│
├── assets/                      ← Shared static assets
│   ├── css/style.css            ← Full custom CSS (no Bootstrap/Tailwind)
│   └── js/
│       ├── main.js              ← Public page JS (search, validation)
│       └── admin.js             ← Admin JS (table filter, modal, delete)
│
├── config/
│   └── db.php                   ← PDO database connection (PHP backend)
│
├── includes/
│   ├── header.php               ← Shared HTML header template
│   ├── footer.php               ← Shared HTML footer template
│   └── functions.php            ← Session, CSRF, flash, sanitize helpers
│
├── admin/                       ← PHP Admin backend pages
│   ├── login.php                ← Bcrypt auth + session
│   ├── logout.php               ← Session destroy
│   ├── index.php                ← Registrations management table
│   └── delete.php               ← CSRF-protected delete handler
│
├── index.php                    ← Public homepage (PHP + MySQL)
├── register.php                 ← Registration form processor (PHP)
├── success.php                  ← Confirmation page (PHP)
├── setup.php                    ← 1-click DB installer + event seeder
├── database.sql                 ← SQL schema + sample events
│
└── .gitignore
```

---

## 🌟 Features

### Public Frontend
- 🎴 **Event Cards** — Browse all events with category badges & dates
- 🔍 **Live Search** — Instant search by name, category, or venue (JS)
- ✍️ **Registration Form** — Dual validation (JS + PHP), department & year selection
- 🖨️ **Print Receipt** — Printable confirmation with print CSS media queries

### Admin Backend
- 🔐 **Secure Login** — Bcrypt password hashing, CSRF tokens, session fixation prevention
- 📊 **KPI Dashboard** — Total registrations, events, today's count
- 📋 **Registrations Table** — Search/filter, view modal, delete with confirmation
- 🛡️ **CSRF Protection** — All POST forms protected with CSRF tokens

---

## 🚀 Quick Start (XAMPP)

1. **Clone the repo:**
   ```bash
   git clone https://github.com/Theerdha-Raju/EventHub.git
   cd EventHub
   ```

2. **Copy to XAMPP htdocs:**
   ```
   C:\xampp\htdocs\event-registration\
   ```

3. **Run the setup installer:**
   Open in browser: `http://localhost/event-registration/setup.php`
   - Creates database, tables, and seeds 5 sample events automatically.

4. **Open the app:**
   - Public site: `http://localhost/event-registration/`
   - Admin login: `http://localhost/event-registration/admin/login.php`

---

## 🌐 Frontend Demo (No Server Needed)

Open `frontend/index.html` directly in your browser to view the full UI:
- Browse events, fill registration form, view success receipt
- Admin login → dashboard (demo credentials: `admin` / `admin123`)

---

## 🗄️ Database

**Database:** `event_management`

| Table           | Description                        |
|-----------------|------------------------------------|
| `events`        | 5 sample college events            |
| `registrations` | Student registration records       |
| `admins`        | Admin accounts (bcrypt passwords)  |

**Sample Events:**
| # | Event | Category |
|---|-------|----------|
| 1 | Annual Sports Day 2026 | Sports |
| 2 | College Music & Dance Night | Cultural |
| 3 | Web Design & Coding Contest | Coding |
| 4 | Smartphone Photography Workshop | Workshop |
| 5 | Blood Donation & Free Health Camp | Community |

---

## 👤 Author

**Theerdha Raju**  
📧 theerdharaju4@gmail.com  
🔗 [GitHub: Theerdha-Raju](https://github.com/Theerdha-Raju)

---

> Built as a college project demonstrating full-stack web development with PHP and MySQL.
