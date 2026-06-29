# Deployment Guide (VPS / Server)

This guide explains the steps to deploy the FotoApp application to a VPS (such as DigitalOcean, Linode, AWS EC2, etc.) or a hosting provider that supports Docker. This application utilizes Laravel (PHP), Vue.js (Inertia), Nginx, and MySQL.

## Server Prerequisites
Ensure your server has the following installed:
- **Git**
- **Docker**
- **Docker Compose**

## Deployment Steps

### 1. Clone the Repository
SSH into your VPS server, then clone this repository.
```bash
git clone https://github.com/radityaputranto/photo-review-laravel-vue.git
cd photo-review-laravel-vue
```

### 2. Setup Environment Variables
Copy the `.env.example` file to `.env` and configure it for production.
```bash
cp .env.example .env
```

Open the `.env` file using a text editor (like `nano` or `vim`) and adjust the values:
```env
APP_NAME=FotoApp
APP_ENV=production
APP_KEY= # We will fill this in the next step
APP_DEBUG=false
APP_URL=https://your-domain.com

# Database Configuration (Match this with docker-compose.prod.yml)
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=fotoapp
DB_USERNAME=root
DB_PASSWORD=secret # Change this to a secure password
```
*Note: Ensure the `DB_PASSWORD` in the `.env` file matches the one you set for the database.*

### 3. Build & Run Containers
Use `docker-compose.prod.yml` to build the images and run the containers in the background (detached mode).
```bash
docker-compose -f docker-compose.prod.yml up -d --build
```
*The initial build process will take some time as it downloads images, installs PHP dependencies (Composer), and builds the Vue/Vite assets.*

### 4. Setup Laravel
Once the containers are running, you need to perform the initial Laravel setup (generate APP_KEY, run migrations, and seed the database).
Execute these commands inside the `app` container:

```bash
# Generate APP_KEY
docker-compose -f docker-compose.prod.yml exec app php artisan key:generate

# Run Database Migrations & Seeding (For default users)
docker-compose -f docker-compose.prod.yml exec app php artisan migrate --seed

# Optimize configuration for production
docker-compose -f docker-compose.prod.yml exec app php artisan optimize
docker-compose -f docker-compose.prod.yml exec app php artisan view:cache
```

### 5. Link Storage (Optional)
This application stores photos and documents in the `storage/app/public` folder. You need to create a symbolic link so these files are publicly accessible:
```bash
docker-compose -f docker-compose.prod.yml exec app php artisan storage:link
```

### 6. Finished!
The FotoApp application is now running on your server and can be accessed via your Server IP Address or Domain (Port 80/443).
Please log in using the default credentials found in `docs/installation.md`.

---

## Service Management (Docker Compose)
Here are some useful commands for managing the application on your server:

**View Container Status:**
```bash
docker-compose -f docker-compose.prod.yml ps
```

**View Application Logs:**
```bash
docker-compose -f docker-compose.prod.yml logs -f
```

**Restart the Application:**
```bash
docker-compose -f docker-compose.prod.yml restart
```

**Stop the Application:**
```bash
docker-compose -f docker-compose.prod.yml down
```

## Updating the Application (New Release)
If there are code updates from the GitHub repository, here is how to update it on the server:
```bash
# 1. Pull the latest code
git pull origin main

# 2. Rebuild the image and run the containers
docker-compose -f docker-compose.prod.yml up -d --build

# 3. Run migrations if there are database changes
docker-compose -f docker-compose.prod.yml exec app php artisan migrate

# 4. Clear Cache
docker-compose -f docker-compose.prod.yml exec app php artisan optimize:clear
docker-compose -f docker-compose.prod.yml exec app php artisan optimize
```
