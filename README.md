# RB Fitness - Gym Management System

![RB Fitness](https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg)

RB Fitness is a comprehensive Gym Management System built with **Laravel 13**, designed to streamline gym operations, member management, and payment tracking. This system provides a robust administrative dashboard to manage membership plans, trainers, facilities, and automated communication.

## 🚀 Features

- **Admin Dashboard**: Real-time overview of gym statistics, including active members and upcoming renewals.
- **Member Management**: Complete CRUD operations for gym members with automated expiry tracking.
- **Membership Plans**: Dynamic creation and management of membership plans (Monthly, Quarterly, Yearly, etc.).
- **Trainer Management**: Track gym trainers, their specializations, and assignments.
- **Payment & Accounting**: Track payments, pending dues, and generate financial reports.
- **Facility Showcase**: Manage and display gym facilities (Cardio Zone, Free Weights, etc.) on the frontend.
- **Automated Communication**: Integrated with **Twilio** for OTP verification and membership notifications.
- **Cloud Storage**: Seamless image management via **Cloudinary**.
- **Modern UI**: Clean and responsive administrative interface built with modern Blade components and Vite.

## 🛠️ Tech Stack

- **Backend**: Laravel 13, PHP 8.3
- **Frontend**: Blade Templates, Vanilla CSS, Vite
- **Database**: PostgreSQL (configured in Docker) / MySQL
- **Communications**: Twilio SDK
- **Media**: Cloudinary PHP SDK
- **Deployment**: Dockerized, ready for platforms like Render

## ⚙️ Installation

To get started with RB Fitness locally, follow these steps:

1. **Clone the repository:**
   ```bash
   git clone <repository-url>
   cd rbfitness
   ```

2. **Install PHP dependencies:**
   ```bash
   composer install
   ```

3. **Install Node.js dependencies:**
   ```bash
   npm install && npm run build
   ```

4. **Environment Setup:**
   Copy the example environment file and configure your database and third-party services:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   *Required variables: `CLOUDINARY_URL`, `TWILIO_SID`, `TWILIO_AUTH_TOKEN`, `TWILIO_FROM`.*

5. **Run Migrations and Seeders:**
   ```bash
   php artisan migrate --seed
   ```

6. **Start the Development Server:**
   ```bash
   php artisan serve
   ```

## 🔐 Default Access

For testing purposes, you can use the following default admin credentials (if seeded):

- **URL**: `/admin/login`
- **Email**: `admin@rbfitness.com`
- **Password**: `password123`

## 📂 Project Structure

- `app/Http/Controllers/Admin`: Core logic for administrative tasks.
- `app/Models`: Database models (Member, Plan, Trainer, etc.).
- `resources/views/admin`: Administrative dashboard templates.
- `resources/views/frontend`: Public-facing website templates.
- `database/seeders`: Initial data setup for plans and admin accounts.

---

Built with ❤️ for RB Fitness.
