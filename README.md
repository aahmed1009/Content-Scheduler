# Laravel Content Scheduler API

A backend API built with **Laravel** for scheduling and managing social media posts across platforms like Twitter, Instagram, and LinkedIn. This challenge project includes authentication, post scheduling, platform management, character validation, rate limiting, job queues, and activity logging.

---

##Features

* ✅ Laravel Sanctum authentication (login/register)
* ✅ CRUD for posts (create, read, update, delete)
* ✅ Schedule posts with `scheduled_time`
* ✅ Per-platform character limit validation
* ✅ Platform activation per user
* ✅ Max 10 scheduled posts per user per day
* ✅ Laravel Job for automatic post publishing
* ✅ Activity logging: post\_created, post\_updated, post\_deleted
* ✅ Performance optimizations (eager loading, pagination, caching)
* ✅ Full test coverage with PHPUnit

---

##  System Requirements

* PHP 
* Composer
* Laravel 10+
* PostgreSQL 


---

##  Installation Guide

```bash
# Clone the repo
git clone https://github.com/aahmed1009/content-scheduler.git
cd content-scheduler

# Install dependencies
composer install


# Generate app key
php artisan key:generate
```

---

### 🔧 Configure Database

Update `.env`:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=content_scheduler
DB_USERNAME=username
DB_PASSWORD=password
```

---

###  Migrate & Seed

```bash
php artisan migrate --seed
```

---

###  Run the App

```bash
php artisan serve
```

App will be available at:
 `http://127.0.0.1:8000`

---

##  Authentication (via Laravel Sanctum)

### Register

```http
POST /api/register
Content-Type: application/json

{
  "name": "Alaa",
  "email": "alaa@example.com",
  "password": "password",
  "password_confirmation": "password"
}
```

### Login

```http
POST /api/login
Content-Type: application/json

{
  "email": "alaa@example.com",
  "password": "password"
}
```

Use the returned `token` as:

```
Authorization: Bearer <token>
```

---

## 📬 API Endpoints

### 🔸 Posts

* `GET /api/posts` – List posts (with filters)
* `POST /api/posts` – Create post
* `PUT /api/posts/{id}` – Update post
* `DELETE /api/posts/{id}` – Delete post

### 🔸 Platforms

* `GET /api/platforms` – List all platforms
* `POST /api/platforms/toggle` – Activate/deactivate platform for user

### 🔸 Logs

* `GET /api/logs` – View authenticated user's activity logs

---

## ⏱️ Scheduled Job for Publishing Posts

Posts scheduled with `scheduled_time <= now()` will be published by a Laravel Job.

### Setup Cron (Ubuntu)

```bash
crontab -e
```

Add:

```bash
* * * * * cd /path/to/content-scheduler && php artisan schedule:run >> /dev/null 2>&1
```

---

## 🧺s Testing

Run the full test suite:

```bash
php artisan test
```

Or target a specific class:

```bash
php artisan test --filter=PostFeatureTest
```

✅ Tests include:

* Post creation
* Rate limiting
* Character limit validation

---

## ⚡ Performance Optimizations

* Eager loading relationships: `.with('platforms')`

* Caching: `Cache::remember('platforms', ...)`
* Background jobs: Laravel queue for scheduled publishing

---

