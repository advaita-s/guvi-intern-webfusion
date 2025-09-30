# GUVI Intern Web App

# A web application built with a flow of Register → Login → Profile.

  1. User credentials are securely stored in MySQL.
  2. Profile details such as age, DOB, and contact are managed in MongoDB.
  3. Sessions are handled using Redis tokens with browser localStorage.
  4. The frontend is styled with Bootstrap and uses jQuery AJAX for smooth interaction (no form submissions).

# Features
  1. User Registration with secure password hashing
  2. User Login with prepared statements
  3. Profile management (update age, DOB, contact)
  4. MySQL for registration data
  5. MongoDB for user profile storage
  6. Redis for session management
  7. LocalStorage for browser session handling
  8. Bootstrap UI for clean design
  9. AJAX-based form handling (no page reloads)

# 📂 Project Structure

guvi-intern/
│── assets/
│── css/
│    └── styles.css
│── js/
│    ├── register.js
│    ├── login.js
│    └── profile.js
│── php/
│    ├── register.php
│    ├── login.php
│    └── profile.php
│── index.html
│── register.html
│── login.html
│── profile.html

# ⚙️ Tech Stack

  1. Frontend: HTML, CSS, Bootstrap, jQuery
  2. Backend: PHP
  3. Databases: MySQL & MongoDB
  4. Session Management: Redis + LocalStorage

# 🚀 How to Run Locally

  1. Install XAMPP, MongoDB, and Redis (via WSL).
  2. Place the project in htdocs folder (C:\xampp\htdocs\guvi-intern).
  3. Start Apache & MySQL from XAMPP Control Panel.
  4. Import MySQL schema in phpMyAdmin.
  5. Start MongoDB and Redis servers.
  6. Open in browser: *http://localhost/guvi-intern/register.html*





