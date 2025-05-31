# Pet Management System

## Overview
The Pet Management System is a web application designed to manage pet records, appointments, veterinarians, and billing. It allows veterinary clinics to manage their operations efficiently.

## Project Structure
```
pet-management-system
├── assets
│   ├── css
│   │   └── style.css
│   └── js
│       └── script.js
├── includes
│   ├── db_conn.php
│   ├── header.php
│   └── footer.php
├── add.php
├── edit.php
├── delete.php
├── index.php
└── README.md
```

## Features
- **User Authentication**: Secure login for pet owners and staff
- **Pet Management**: Add, edit, and view pet records
- **Owner Management**: Track pet owner information
- **Veterinarian Profiles**: Manage veterinary staff details
- **Appointment Scheduling**: Book and manage pet appointments
- **Billing and Payments**: Track services and payments

## Database Structure
- **owner**: Pet owners' personal information and authentication details
- **pet**: Pet records linked to their owners
- **veterinarian**: Information about veterinary staff
- **appointment**: Scheduled appointments with composite primary key
- **billingpayment**: Payment records for veterinary services

## Setup Instructions
1. Clone the repository: `git clone https://github.com/AquinoLuisMartin/pet-management-system.git`
2. Navigate to the project directory
3. Set up your local server environment (e.g., XAMPP, WAMP)
4. Create a database named `Pet`
5. Import the database schema:

