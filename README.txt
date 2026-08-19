http://localhost/php/library-management/public/index.php

http://localhost/php/library-management/admin/dashboard.php



# Library Management System

A simple web-based library management system for managing books, students, and issued books.

---------------------------------------
📁 Folder Structure:

library-management/
├── admin/              → Admin dashboard and management pages
├── public/             → Public-facing pages for students
├── includes/           → Shared PHP files (DB connection, header/footer, sessions)
├── assets/             → Static files (CSS, JS, Images)
├── database/           → Database schema (.sql file)
├── README.txt          → Project info and setup guide

---------------------------------------
⚙️ Features:

✔ **Admin Panel**:
  - Login/logout
  - Manage books (add, edit, delete)
  - Manage students
  - Issue & return books
  - Generate reports

✔ **Student Panel**:
  - Register & login
  - View available books
  - Track issued books
  - Contact library

---------------------------------------
🛠️ Setup Instructions:

1. **Import the SQL**:
   - Open phpMyAdmin.
   - Create a database: `library_management`.
   - Import the SQL file located at `database/schema.sql`.

2. **Configure Database Connection**:
   - Open `includes/db_connect.php`.
   - Set your MySQL username, password, and host (if different from default).

3. **Run the Application**:
   - Place the project folder inside `htdocs/` (for XAMPP) or `www/` (for WAMP).
   - Start Apache and MySQL services.
   - Visit: [http://localhost/library-management/public/](http://localhost/library-management/public/).

---------------------------------------
👤 Default Admin Credentials (for demo purposes):

- **Username**: `admin`  
- **Password**: `admin123`  
  *(Password is encrypted in the database — set manually if needed.)*

---------------------------------------
📦 Author & License:

- **Developed by**: [Your Name]  
- **License**: Free for educational use.

---------------------------------------
📚 Additional Notes:

- Ensure that the `assets/` folder contains all required images and CSS/JS files.
- For production use, update the database credentials and secure the application.
