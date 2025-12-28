# Society Security Solution (SSS) 🏢🔒

A comprehensive **Database Management System (DBMS)** web application designed to digitize and automate security, visitor tracking, and administration for residential societies. This project replaces manual entry logs with a secure, role-based digital platform.

## 📖 Project Overview

**Society Security Solution (SSS)** connects Security Supervisors, Residents, and Administrators on a single platform to ensure data integrity and real-time communication.

* **Real-time Approval:** Residents verify visitors instantly via their dashboard.
* **Digital Attendance:** Automated check-in/out for daily help (Maids, Drivers) using unique Security Codes.
* **Financial Management:** Tracks monthly maintenance dues and payment status.

## ✨ Key Features

### 🛠️ Admin Module
* **Master Data Management:** Add Buildings and Flats.
* **User Management:** Create accounts for Residents and Supervisors.
* **Maintenance:** One-click generation of monthly dues for all flats.
* **Payment Tracking:** Update payment status (Cash/Online) for maintenance records.

### 👮 Supervisor (Security) Module
* **Visitor Entry:** Register casual visitors (Delivery, Guests) and trigger approval requests.
* **Regular Visitor Tracking:** Fast-track entry for daily staff using 6-digit **Security Codes**.
* **Staff Management:** Add new society staff and manage their active/inactive status.
* **Live Logs:** View real-time status of who is inside the premises.

### 🏠 Resident Module
* **Live Notifications:** View pending visitor requests on the dashboard.
* **Action:** **Approve** or **Reject** visitors (updates security instantly).
* **History:** View attendance logs of personal daily help (e.g., Maid's entry/exit times).
* **Dues:** View maintenance history and payment status.

## 🛠️ Tech Stack

* **Frontend:** HTML5, CSS3, JavaScript (Fetch API for AJAX).
* **Backend:** PHP (Native).
* **Database:** MySQL.
* **Server:** Apache (via XAMPP/WAMP).

## 📂 Project Structure

/society-security-system
│
├── /api                
│   ├── approve_visitor.php
│   ├── reject_visitor.php
│   └── get_pending_requests.php
│
├── /css                 # Stylesheets
│   └── style.css
│
├── db_connect.php       # Database configuration
├── index.php            # Login Page
├── admin_dashboard.php  # Admin Controller
├── resident_dashboard.php # Resident Controller
├── supervisor_dashboard.php # Supervisor Controller
├── logout.php           # Session destroyer
└── [All other process files like add_visitor_process.php]

Database Schema
The project uses a relational database named Society with the following key tables:

users: Stores login credentials and roles (Admin/Resident/Supervisor).

buildings & flats: Hierarchical structure of the society.

residents: Links users to specific flats.

visitors: Stores entry/exit logs and approval status.

regular_visitors: Stores daily help data and unique Security Codes.

attendance_log: Tracks check-in/out times for regular visitors.

maintenance: Tracks monthly dues and payment history.

staff: Stores society employee details.
