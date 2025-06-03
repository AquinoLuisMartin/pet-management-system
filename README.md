# Pet Management System

## Overview
The Pet Management System is a web application designed to manage pet records, appointments, veterinarians, and billing. It allows veterinary clinics to manage their operations efficiently.

## Project Structure
```
pet-management-system/
├── assets/
│   ├── css/
│   │   └── style.css
│   └── js/
│       └── script.js
├── includes/
│   ├── db_conn.php
│   ├── header.php
│   └── footer.php
├── modules/
│   ├── owners/
│   │   ├── owners.php
│   │   ├── owner_view.php
│   │   ├── owner_edit.php
│   │   ├── owner_update.php
│   │   └── owner_delete.php
│   ├── pets/
│   │   ├── pets.php
│   │   ├── pet_view.php
│   │   ├── pet_edit.php
│   │   ├── pet_update.php
│   │   └── pet_delete.php
│   ├── veterinarians/
│   │   ├── veterinarians.php
│   │   ├── veterinarian_view.php
│   │   ├── veterinarian_edit.php
│   │   ├── veterinarian_update.php
│   │   └── veterinarian_delete.php
│   ├── appointments/
│   │   ├── appointments.php
│   │   ├── appointment_view.php
│   │   ├── appointment_edit.php
│   │   ├── appointment_update.php
│   │   └── appointment_delete.php
│   └── billing/
│       ├── billing.php
│       ├── billing_view.php
│       ├── billing_edit.php
│       ├── billing_update.php
│       └── billing_delete.php
├── index.php
├── login.php
├── dashboard.php
└── README.md
```

## Features
- **User Authentication:** Secure login for pet owners and staff
- **Pet Management:** Add, edit, and view pet records
- **Owner Management:** Track pet owner information
- **Veterinarian Profiles:** Manage veterinary staff details
- **Appointment Scheduling:** Book and manage pet appointments
- **Billing and Payments:** Track services and payments
- **Database Integration:** Utilizes stored procedures for efficient and secure database operations

## Database Structure
- **owner:** Pet owners' personal information and authentication details
- **pet:** Pet records linked to their owners
- **veterinarian:** Information about veterinary staff
- **appointment:** Scheduled appointments with composite primary key
- **billingpayment:** Payment records for veterinary services

## Stored Procedures
The system utilizes stored procedures for all database operations, including:

- Owner management (CreateOwner, UpdateOwner, DeleteOwner, etc.)
- Pet management (CreatePet, UpdatePet, DeletePet, etc.)
- Appointment scheduling (CreateAppointment, UpdateAppointment, etc.)
- Billing and payment processing (CreatePayment, GetPendingPayments, etc.)
- Various reporting procedures (GetDashboardStats, GetRecentActivities, etc.)

## Setup Instructions
1. Clone the repository: `git clone https://github.com/AquinoLuisMartin/pet-management-system.git`
2. Navigate to the project directory
3. Set up your local server environment (e.g., XAMPP, WAMP)
4. Create a database named Pet
5. Import the database schema and stored procedures from the SQL files provided
6. Configure database connection in db_conn.php
7. Access the application through your local server

## Technologies Used
- PHP 8.x
- MySQL/MariaDB
- HTML5, CSS3, JavaScript
- Bootstrap 5
- Font Awesome for icons


