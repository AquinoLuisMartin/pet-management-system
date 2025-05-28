# Pet Management System

## Overview
The Pet Management System is a web application designed to manage pet records. It allows users to add, edit, delete, and view pet information in a user-friendly interface.

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
- **Add New Pet**: Users can add new pet records through a form.
- **Edit Pet Information**: Users can edit existing pet records.
- **Delete Pet Records**: Users can delete pet records from the database.
- **View Pet List**: The main page displays a list of all pets in the system.

## Setup Instructions
1. Clone the repository to your local machine.
2. Navigate to the project directory.
3. Ensure you have a local server environment (e.g., XAMPP, WAMP) set up.
4. Create a database named `Pet` and import the necessary SQL schema (if provided).
5. Update the `db_conn.php` file with your database credentials.
6. Access the application through your web browser at `http://localhost/pet-management-system/index.php`.

## Usage
- To add a new pet, click on the "Add New Pet" button on the main page.
- To edit a pet, click the edit icon next to the pet's record.
- To delete a pet, click the delete icon next to the pet's record.
- The main page will always display the current list of pets in the system.