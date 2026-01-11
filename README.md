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

## Run with Docker
1. Start containers:
   ```bash
   docker compose up -d --build
