<p align="center">AI Job Board — Laravel Graduation Project</p>

A Job Board System built with **Laravel** where candidates create profiles, browse and apply for jobs, and where an **AI chatbot** recommends suitable jobs based on the candidate's profile skills.

## Features

### Candidate (Employee)
- Register a new account, login and logout
- Edit their profile (full name, age, job title, description, email, phone, skills, profile image, resume/CV)
- Browse available jobs and view job details
- Apply for jobs (with optional cover letter)
- Cancel a job application
- Personal dashboard with recommended jobs and application status

### Admin
- Login with a dedicated admin account
- Add, edit and delete jobs
- View all candidates
- View all job applications and update their status (pending / accepted / rejected)

### AI Chatbot (Jobot)
- Recommends jobs from the database by matching the candidate's skills against each job's required skills
- Answers questions about applying, cancelling, deadlines, and platform statistics
- Chat history is stored in the database

## Entities (MySQL Tables)

| Table | Attributes |
|-------|------------|
| `users` | name, email (unique), password, role (candidate/admin), age, job_title, description, phone, skills, image, resume |
| `job_listings` | title, description, required_skills, category, location, work_type (remote/on-site/hybrid), salary, deadline |
| `applications` | user_id (FK), job_listing_id (FK), status, cover_letter — unique per (user, job) |
| `chat_messages` | user_id (FK), message, response |

Relationships: a user has many applications; a job listing has many applications; an application belongs to a user and a job listing.

## Requirements

- PHP 8.2+ (XAMPP recommended)
- Composer
- MySQL (MariaDB via XAMPP works)

## Setup

```bash
# 1. Start MySQL (via XAMPP) and create the database
#    CREATE DATABASE job_board CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# 2. Configure .env (already configured for XAMPP defaults)
#    DB_CONNECTION=mysql
#    DB_HOST=127.0.0.1
#    DB_PORT=3306
#    DB_DATABASE=job_board
#    DB_USERNAME=root
#    DB_PASSWORD=

# 3. Install dependencies
composer install

# 4. Migrate and seed
php artisan migrate:fresh --seed

# 5. Link storage (so uploaded profile images / CVs are served)
php artisan storage:link

# 6. Run the app
php artisan serve
# open http://127.0.0.1:8000
```

## Demo Accounts (seeded)

| Role | Email | Password |
|------|-------|----------|
| Admin | `admin@jobboard.com` | `password123` |
| Candidate | `ahmed@example.com` | `password123` |

## Project Structure

- `app/Models` — `User`, `Job` (job_listings), `Application`, `ChatMessage`
- `app/Http/Controllers` — Auth, Dashboard, Profile, Job, Application, Chatbot
- `app/Http/Controllers/Admin` — admin Dashboard, Job CRUD, Candidates, Applications
- `app/Services/ChatbotService.php` — the AI recommendation/answering engine
- `app/Http/Middleware` — `EnsureAdmin`, `EnsureCandidate` role guards
- `database/migrations` — MySQL schema
- `database/seeders` — demo data
