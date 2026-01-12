# PHP Item Management System (Docker + MySQL)

A web-based Item / Inventory Management System built using **PHP + MySQL**, designed to demonstrate secure authentication, CRUD operations, and basic system architecture.

This project was developed as a technical assignment and working example of backend and full-stack fundamentals.

## Tech Stack
- PHP 8.2 (Apache)
- MySQL 8
- phpMyAdmin
- Docker & Docker Compose

## Assignment Requirements Mapping

### 1. User Authentication
- Secure login system using PHP sessions
- Passwords are hashed using `password_hash()` (bcrypt)
- Login verification uses `password_verify()`
- Logout functionality included

### 2. Item Management Dashboard

#### Item Listing
- Displays item name, description, category, quantity, and date added
- Data retrieved using prepared SQL statements

#### Add Item
- Form to create new inventory items
- Required fields: name, description, category, quantity
- Quantity validation (must be non-negative)

#### Edit Item
- Existing item data is pre-filled
- Users can update item details

#### Delete Item
- Items can be deleted
- Confirmation prompt included to prevent accidental deletion

### 3. Additional Features
- Category management module (CRUD)
- User management module
- Filtering and pagination on item listing
- Dockerized environment for consistent setup
- Prepared statements (PDO) to prevent SQL injection

## Features Overview
- Authentication (Login / Logout)
- Items: Create / Read / Update / Delete
- Categories: Create / Read / Update / Delete
- Users management
- Secure database interaction (PDO)

## Security & Technical Decisions
- **Password Hashing**: Passwords are never stored in plain text. Bcrypt hashing ensures secure credential storage.
- **Prepared Statements**: All database queries use PDO prepared statements to prevent SQL injection.
- **Dockerized Setup**: Docker ensures the system runs consistently across different machines and avoids environment issues.

## First-Time Docker Setup (Step by Step)
1. Clone the repository:
   ```bash
   git clone <repo-url>
   ```
2. Navigate to the project directory:
   ```bash
   cd php-item-management-system
   ```
3. Build and start containers:
   ```bash
   docker compose up -d --build
   ```
4. Initialize database schema and seed data:
   ```bash
   docker exec -i inventory_db mysql -u root -proot < sql/schema.sql
   docker exec -i inventory_db mysql -u root -proot < sql/seed.sql
   ```
5. Open the application:
   - App: http://localhost:8080
   - phpMyAdmin: http://localhost:8081
6. Demo login:
   - Email: admin@example.com
   - Password: admin123

## Reimport Database (Optional)
To reset and reimport the database:
```bash
docker exec -i inventory_db mysql -u root -proot -e "DROP DATABASE IF EXISTS inventory_system; CREATE DATABASE inventory_system;"
docker exec -i inventory_db mysql -u root -proot < sql/schema.sql
docker exec -i inventory_db mysql -u root -proot < sql/seed.sql
```
