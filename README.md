# php-item-management-system
Item Management System (PHP + MySQL) with secure login and CRUD dashboard.

# PHP Item Management System (Docker + MySQL)
A simple Inventory / Item Management System built with **PHP + MySQL** featuring:
- Secure login (sessions + `password_verify`)
- Items dashboard (CRUD)
- Category management (CRUD)
- Dockerized setup (consistent environment)

## Tech Stack
- PHP 8.2 (Apache)
- MySQL 8
- phpMyAdmin
- Docker Compose

## Features
- Authentication (Login/Logout)
- Items: Create / Read / Update / Delete
- Categories: Create / Read / Update / Delete
- Prepared statements (PDO)

## First-Time Docker Setup (Step by Step)
1. Clone the repo:
   ```bash
   git clone <repo-url>
   ```
2. Go into the project folder:
   ```bash
   cd php-item-management-system
   ```
3. Build and start containers:
   ```bash
   docker compose up -d --build
   ```
4. Import database schema and seed data:
   ```bash
   docker exec -i inventory_db mysql -u root -proot < sql/schema.sql
   docker exec -i inventory_db mysql -u root -proot < sql/seed.sql
   ```
5. Open the app:
   - App: http://localhost:8080
   - phpMyAdmin: http://localhost:8081
6. Login with:
   - Email: admin@example.com
   - Password: admin123

## Run with Docker
1. Start containers:
   ```bash
   docker compose up -d --build
   ```

## First-Time Setup
If you just cloned the repo, the database will be empty and no users exist. Run the SQL scripts to create the schema and seed data:
```bash
docker exec -i inventory_db mysql -u root -proot < sql/schema.sql
docker exec -i inventory_db mysql -u root -proot < sql/seed.sql
```

## Reimport Database (Docker)
Use these commands to reset and reimport the schema and seed data:
```bash
docker exec -i inventory_db mysql -u root -proot -e "DROP DATABASE IF EXISTS inventory_system; CREATE DATABASE inventory_system;"
docker exec -i inventory_db mysql -u root -proot < sql/schema.sql
docker exec -i inventory_db mysql -u root -proot < sql/seed.sql
```
