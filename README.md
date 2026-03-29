<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

# 🧠 Canteen Backend API

A scalable backend system built with Laravel, designed to power a digital food ordering platform and eliminate long queues in university cafeterias.

---

## 🚀 Overview

This backend provides a robust REST API for managing:

* authentication (email & Google OAuth)
* food menu and categories
* order processing and tracking
* admin dashboard operations
* real-time statistics

It is designed with performance, scalability, and clean architecture principles in mind.

---

## 🏗️ Architecture

The backend follows a modular monolithic architecture, designed for future migration to microservices.

### 🔧 Core Components

* **Framework:** Laravel (REST API)
* **Database:** PostgreSQL
* **Cache Layer:** Redis
* **Storage:** Cloudflare R2 (S3-compatible)
* **Authentication:** Laravel Sanctum + Google OAuth (Socialite)
* **Containerization:** Docker
* **Reverse Proxy:** NGINX

---

## ⚙️ System Architecture

* Stateless API secured with token-based authentication
* Redis used as primary cache store
* PostgreSQL for relational data integrity
* Cloud storage for scalable image delivery
* Dockerized services for portability and consistency
* NGINX as entry point and reverse proxy

---

## 📁 Project Structure

```
app/
 ├── Http/
 ├── Models/
 └── Providers/

routes/
 ├── api.php
 ├── web.php

database/
 ├── migrations/
 ├── seeders/
 └── factories/

config/
```

---

## 🔐 Authentication

### Supported Methods

* Email & password authentication
* Google OAuth (via Laravel Socialite)

### Implementation

* Uses **Laravel Sanctum** for token-based authentication
* Tokens are issued on login/register and used for protected routes

---

## 📡 API Endpoints

### 🔓 Public

* `POST /register`
* `POST /login`
* `GET /meniu`

---

### 🔒 Authenticated (Sanctum)

#### 👤 User

* `GET /user`
* `GET /food-items`
* `GET /categories`
* `POST /orders`
* `GET /orders`
* `GET /orders/{id}`
* `GET /user/orders`

---

#### 🧑‍💼 Admin

* `GET /admin/orders`

* `GET /admin/orders/pending`

* `PUT /admin/orders/{order}/status`

* `GET /admin/stats`

* `GET /admin/users`

* `POST /admin/users`

* `PUT /admin/users/{id}`

* `DELETE /admin/users/{id}`

* `GET /admin/produse`

* `POST /admin/produse`

* `PUT /admin/produse/{id}`

* `DELETE /admin/produse/{id}`

---

## 🧠 Core Models

* **User**
* **FoodItem**
* **Category**
* **Order**
* **OrderItem**
* **OrderStatus**
* **PaymentMethod**

### Relationships

* User → Orders
* Order → OrderItems
* OrderItem → FoodItem
* FoodItem → Category

---

## ⚡ Performance & Caching

### Redis Integration

* Food items are cached using Redis:

  * cache key: `food_items_all`
  * TTL: 10 minutes

* Cache invalidation:

  * triggered on create/update/delete operations

### Benefits

* reduces database load
* improves response time
* scalable under high traffic

---

## 🖼️ Image Storage (Cloudflare R2)

* Images are stored using S3-compatible Cloudflare R2
* Backend generates **temporary signed URLs**
* URLs expire after a short period (security + performance)

---

## 🛒 Order Processing Flow

1. User authenticates (Sanctum / Google OAuth)
2. Frontend fetches food items (cached via Redis)
3. User selects products and sends order request
4. Backend validates:

   * items
   * quantities
   * pickup time (11:30 – 15:00 constraint)
5. Order is created inside a database transaction:

   * order record
   * order items
6. Admin accesses orders via admin endpoints
7. Admin updates order status
8. User can track order history and status

---

## 📊 Admin Dashboard Features

* total orders count
* total users
* revenue (daily)
* top ordered items
* order status distribution

---

## 🐳 Docker Setup

### Services

* `backend` (Laravel API)
* `db` (PostgreSQL)
* `redis`
* `nginx`

### Run the system

```bash
docker-compose up --build
```

---

## 🔐 Security

* Token-based authentication (Sanctum)
* Role-based access control (user / admin)
* Input validation on all endpoints
* Secure file access via signed URLs

---

## 📈 Scalability & Design Decisions

* Redis caching layer reduces DB pressure
* Stateless API enables horizontal scaling
* Docker ensures environment consistency
* Modular architecture allows microservices transition
* Prepared for event-driven architecture (Kafka planned)

---

## 🚀 Future Improvements

* Kafka integration for async processing
* WebSockets for real-time updates
* payment gateway integration
* rate limiting & advanced security
* CI/CD pipeline

---

## 👨‍💻 Author

Developed as a Bachelor's Thesis project with a strong focus on:

* backend architecture
* scalability
* performance optimization
* real-world usability

---
