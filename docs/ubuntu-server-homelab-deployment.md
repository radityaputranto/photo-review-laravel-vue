# Ubuntu Server & Homelab Docker Deployment Guide

This guide provides a comprehensive, step-by-step tutorial for deploying **FotoApp** on **Ubuntu Server** (such as a local Homelab server, Mini PC, Proxmox VM/LXC, or Cloud VPS) using **Docker** and **Docker Compose**.

---

## 1. Overview & Key Differences

### Is the deployment process the same as on Windows?
**Yes, the core Docker container execution is identical.** Because the application runtime (PHP 8.4-FPM, Nginx, MySQL 8.0) is packaged inside Docker, it runs identically on both Windows and Linux.

### What is different on Ubuntu Server / Homelab?
1. **Docker Engine Installation**: You install Docker natively via `apt` instead of Docker Desktop.
2. **User Permissions**: You run Docker commands using a non-root Linux user without needing `sudo` every time.
3. **Firewall (UFW)**: You must ensure ports (80 / 443) are open in Ubuntu's firewall.
4. **Networking**: You can access it locally via LAN IP (e.g., `http://192.168.1.100`), via reverse proxies (e.g., Nginx Proxy Manager, Caddy, Traefik), or over the internet using **Cloudflare Tunnels** or **Tailscale**.

---

## 2. Server Preparation (Ubuntu Server)

### Step 2.1: Update System Packages
SSH into your Ubuntu Server and update packages:
```bash
sudo apt update && sudo apt upgrade -y
sudo apt install -y curl git ufw ca-certificates gnupg lsb-release
```

### Step 2.2: Install Official Docker Engine & Docker Compose
Install Docker using official Docker repository:

```bash
# 1. Add Docker's official GPG key
sudo install -m 0755 -d /etc/apt/keyrings
curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo gpg --dearmor -o /etc/apt/keyrings/docker.gpg
sudo chmod a+r /etc/apt/keyrings/docker.gpg

# 2. Add Docker repository to Apt sources
echo \
  "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.gpg] https://download.docker.com/linux/ubuntu \
  $(lsb_release -cs) stable" | sudo tee /etc/apt/sources.list.d/docker.list > /dev/null

# 3. Install Docker packages
sudo apt update
sudo apt install -y docker-ce docker-ce-cli containerd.io docker-buildx-plugin docker-compose-plugin
```

### Step 2.3: Manage Docker as a Non-Root User
Allow your current Linux user to run Docker commands without `sudo`:
```bash
sudo usermod -aG docker $USER
newgrp docker
```

Verify installation:
```bash
docker --version
docker compose version
```

### Step 2.4: Configure Firewall (UFW)
Allow SSH, HTTP (Port 80), and HTTPS (Port 443):
```bash
sudo ufw allow OpenSSH
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw enable
```

---

## 3. Application Deployment

### Step 3.1: Clone the Repository
Choose your target directory (e.g., `/home/username/apps` or `/opt`):
```bash
mkdir -p ~/apps && cd ~/apps
git clone https://github.com/radityaputranto/photo-review-laravel-vue.git
cd photo-review-laravel-vue
```

### Step 3.2: Configure Environment (`.env`)
Create the production `.env` file:
```bash
cp .env.example .env
```

Edit `.env` using `nano`:
```bash
nano .env
```

Configure key production parameters:
```dotenv
APP_NAME=FotoApp
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=http://your-homelab-ip # e.g. http://192.168.1.50 or https://foto.yourdomain.com

LOG_CHANNEL=stack
LOG_LEVEL=error

# Database Configuration
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=fotoapp
DB_USERNAME=root
DB_PASSWORD=YourStrongDatabasePassword123!

# Google Drive (If using Google Cloud Service Account)
GOOGLE_APPLICATION_CREDENTIALS=storage/app/google-credentials.json
GOOGLE_DRIVE_CACHE_MINUTES=10
```
*(Save and exit in nano by pressing `CTRL + O`, `Enter`, then `CTRL + X`)*

### Step 3.3: Build & Start Docker Containers
Launch the containers using Docker Compose:
```bash
docker compose -f docker-compose.prod.yml up -d --build
```
> *Tip: The initial build will download base images (PHP 8.4, Nginx, Node, MySQL) and compile frontend assets. This takes 1-3 minutes.*

Verify all 3 containers are healthy and running:
```bash
docker compose -f docker-compose.prod.yml ps
```

---

## 4. Initial Laravel Setup

Execute the following commands once containers are running:

### Step 4.1: Generate Application Key
```bash
docker compose -f docker-compose.prod.yml exec app php artisan key:generate
```

### Step 4.2: Run Database Migrations & Default Seeders
```bash
docker compose -f docker-compose.prod.yml exec app php artisan migrate:fresh --seed --force
```

### Step 4.3: Link Public Storage & Set Linux Permissions
```bash
# Create storage symlink
docker compose -f docker-compose.prod.yml exec app php artisan storage:link

# Set proper ownership and permissions for storage and cache directories
docker compose -f docker-compose.prod.yml exec app chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
docker compose -f docker-compose.prod.yml exec app chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache
```

### Step 4.4: Cache Configurations for Production Performance
```bash
docker compose -f docker-compose.prod.yml exec app php artisan config:cache
docker compose -f docker-compose.prod.yml exec app php artisan route:cache
docker compose -f docker-compose.prod.yml exec app php artisan view:cache
```

---

## 5. Google Drive Integration (Optional)

If you plan to use Google Drive photo syncing:
1. Place your Service Account JSON key inside `storage/app/`:
   ```bash
   cp /path/to/downloaded-key.json storage/app/google-credentials.json
   ```
2. Adjust ownership:
   ```bash
   docker compose -f docker-compose.prod.yml exec app chown www-data:www-data /var/www/html/storage/app/google-credentials.json
   ```
3. See [docs/google-drive.md](google-drive.md) for full service account configuration.

---

## 6. Accessing Your Application

### Option A: Local Network (LAN)
Open your browser and visit:
```
http://<YOUR_UBUNTU_SERVER_IP>
```
*(Example: `http://192.168.1.100`)*

### Option B: Remote Access via Cloudflare Tunnel (Recommended for Homelabs)
If you want secure HTTPS access without exposing public router ports:
1. Install `cloudflared` on Ubuntu Server.
2. Route `foto.yourdomain.com` to `http://localhost:80`.
3. Update `APP_URL=https://foto.yourdomain.com` in your `.env` file.

### Default Login Accounts:
- **Super Admin**: `superadmin@fotoapp.com`
- **Admin**: `admin@fotoapp.com`
- **Photographer**: `photographer@fotoapp.com`
- **Password**: `password` *(Change this immediately after first login)*

---

## 7. Daily Operations & Maintenance

### Checking Logs
```bash
# App & PHP-FPM logs
docker compose -f docker-compose.prod.yml logs -f app

# Web Server (Nginx) logs
docker compose -f docker-compose.prod.yml logs -f web

# Database logs
docker compose -f docker-compose.prod.yml logs -f db
```

### Restarting / Stopping Services
```bash
# Restart all services
docker compose -f docker-compose.prod.yml restart

# Stop all services
docker compose -f docker-compose.prod.yml down

# Start all services
docker compose -f docker-compose.prod.yml up -d
```

### Updating the Application (Pull New Git Updates)
When you push code updates to GitHub:
```bash
# 1. Pull latest changes
git pull origin main

# 2. Rebuild and restart containers
docker compose -f docker-compose.prod.yml up -d --build

# 3. Run new database migrations (if any)
docker compose -f docker-compose.prod.yml exec app php artisan migrate --force

# 4. Refresh configuration cache
docker compose -f docker-compose.prod.yml exec app php artisan optimize:clear
docker compose -f docker-compose.prod.yml exec app php artisan optimize
```

### Database Backup
To backup MySQL database on your Ubuntu server:
```bash
docker compose -f docker-compose.prod.yml exec db mysqldump -u root -pYourStrongDatabasePassword123! fotoapp > ~/backup_$(date +%F_%T).sql
```
