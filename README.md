# PHP Item Management System (Docker + MySQL)

A web-based Item / Inventory Management System built using PHP + MySQL, designed to demonstrate secure authentication, CRUD operations, and basic backend system architecture.

This project was developed as a technical assignment and also serves as a working example of backend and full-stack fundamentals.

## Tech Stack
- PHP 8.2 (Apache)
- MySQL 8
- phpMyAdmin
- Docker & Docker Compose

## Assignment Requirements Mapping

### 1. User Authentication
- Secure login using PHP sessions
- Passwords hashed using `password_hash()` (bcrypt)
- Login verification using `password_verify()`
- Logout functionality implemented

### 2. Item Management Dashboard

#### Item Listing
- Displays:
  - Item name
  - Description
  - Category
  - Quantity
  - Date added
- Data retrieved using PDO prepared statements

#### Add Item
- Form to add new inventory items
- Required fields:
  - Name
  - Description
  - Category
  - Quantity
- Input validation (quantity must be non-negative)

#### Edit Item
- Existing item data is pre-filled
- Users can update item details

#### Delete Item
- Items can be deleted
- Confirmation prompt included to prevent accidental deletion

### 3. Additional / Optional Features

#### User Management Module
- User listing page
- Create new users
- Edit existing users
- Delete users
- Secure password hashing for all users

This fulfills the User Management Module optional requirement.

#### Other Enhancements
- Category management module (CRUD)
- Filtering and pagination on item listing
- Dockerized setup for consistent environment
- Secure database access using PDO prepared statements

## Features Overview
- Authentication (Login / Logout)
- Items: Create / Read / Update / Delete
- Categories: Create / Read / Update / Delete
- Users: Create / Read / Update / Delete
- Secure database interaction (PDO)

## Security & Technical Decisions

### Password Hashing
- Passwords are never stored in plain text
- Uses bcrypt via `password_hash()`
- Login verification via `password_verify()`
- Hashes do not expire and only change if explicitly updated

### SQL Injection Prevention
- All database queries use PDO prepared statements

### Dockerized Environment
- Docker ensures consistent setup across different machines
- Avoids local environment conflicts
- Database uses Docker volume for persistence

## First-Time Docker Setup (Important)
1. Clone the repository:
   ```bash
   git clone <repo-url>
   cd php-item-management-system
   ```
2. Build and start containers:
   ```bash
   docker compose up -d --build
   ```
3. Initialize database schema and seed data:
   ```bash
   docker exec -i inventory_db mysql -u root -proot < sql/schema.sql
   docker exec -i inventory_db mysql -u root -proot < sql/seed.sql
   ```

   This step is required on first run to create tables and demo users.

4. Open the app in your browser:
   - App: http://localhost:8080
   - phpMyAdmin: http://localhost:8081

## Demo Login Credentials
- Email: admin@example.com
- Password: admin123

## Reimport / Reset Database (Optional)
If you need to reset the database completely:

```bash
docker exec -i inventory_db mysql -u root -proot -e "DROP DATABASE IF EXISTS inventory_system; CREATE DATABASE inventory_system;"
docker exec -i inventory_db mysql -u root -proot < sql/schema.sql
docker exec -i inventory_db mysql -u root -proot < sql/seed.sql
```

## Notes for Reviewers
- This system demonstrates backend fundamentals, secure authentication, and CRUD workflows
- Designed as a technical demonstration, not a production system
- GitHub repository serves as the primary documentation
- Code, README, and Docker setup are intended to be self-explanatory and reproducible

## Conclusion
This project fulfills all required functional criteria of the assignment and includes additional features such as user management and Docker deployment to demonstrate practical system development skills.
