# Installation and Setup Guide

Follow these steps to set up the Photo Reviewer App from scratch.

## Prerequisites
Before you begin, ensure you have the following installed on your machine:
- **PHP** (>= 8.2)
- **Composer** (PHP dependency manager)
- **Node.js and npm** (for frontend assets)
- **Git**

## Step 1: Clone the Repository
Clone the project repository to your local machine:
```bash
git clone <repository-url>
cd photo-reviewer-app
```

## Step 2: Install Dependencies
Install the PHP dependencies using Composer and frontend dependencies using npm:
```bash
composer install
npm install
```

## Step 3: Environment Setup
Copy the example environment file to create your local `.env` configuration:
```bash
cp .env.example .env
```
Generate the application key:
```bash
php artisan key:generate
```

## Step 4: Database Configuration
By default, the application uses **SQLite**. If you wish to use a different database (e.g., MySQL or PostgreSQL), update the `DB_CONNECTION` and related credentials in your `.env` file.

Run the database migrations and seed the initial data:
```bash
php artisan migrate:fresh --seed
```
*Note: The seeder will automatically create default user accounts (superadmin, admin, photographer, and a dummy customer). The default password for all seeded accounts is `password`.*

## Step 5: Google Drive Setup
This application integrates with Google Drive to sync photos for sessions.
Please refer to the [Google Drive Setup Guide](google-drive.md) for detailed instructions on configuring the Google API credentials and environment variables.

## Step 6: Compiling Frontend Assets
To compile the Vue components and Tailwind CSS styles, run:
```bash
npm run build
```
For local development with Hot Module Replacement (HMR), use:
```bash
npm run dev
```

## Step 7: Running the Application

### Option A: Without Docker (Local PHP & Node)
You can serve the application locally using Laravel's built-in development server:
```bash
php artisan serve
```
The application will be accessible at `http://localhost:8000`.

### Option B: With Docker (No local PHP/Node required)
If you prefer to run the application using Docker on your local PC (similar to the production environment):

1. Make sure Docker and Docker Compose are running.
2. Build and start the containers:
   ```bash
   docker-compose -f docker-compose.prod.yml up -d --build
   ```
3. Run the initial setup inside the Docker container:
   ```bash
   docker-compose -f docker-compose.prod.yml exec app php artisan key:generate
   docker-compose -f docker-compose.prod.yml exec app php artisan migrate:fresh --seed
   ```
The application will be accessible at `http://localhost:80`. To stop the containers, run `docker-compose -f docker-compose.prod.yml down`.

## Default Login Credentials
After seeding the database, you can log in with any of the following accounts:
- **Super Admin**: `superadmin@fotoapp.com` (Password: `password`)
- **Admin**: `admin@fotoapp.com` (Password: `password`)
- **Photographer**: `photographer@fotoapp.com` (Password: `password`)
- **Customer**: `andi@example.com` (Password: `password`)
