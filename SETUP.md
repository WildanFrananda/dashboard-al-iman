# Dashboard Al-Iman - Setup Instructions

## Prerequisites
- Docker & Docker Compose
- Make (optional, tapi recommended)

## Initial Setup

### 1. Clone & Setup Structure
```bash
mkdir dashboard-al-iman && cd dashboard-al-iman

# Copy semua file configuration dari artifact ke struktur folder yang benar
mkdir -p docker/frankenphp docker/supervisor src
```

### 2. Install Laravel 12
```bash
# Install Laravel di folder src/
docker run --rm -v $(pwd):/app composer create-project laravel/laravel:^12.0 src

# Atau manual:
cd src
composer create-project laravel/laravel:^12.0 .
cd ..
```

### 3. Install TALL Stack Dependencies
```bash
cd src

# Install Livewire
composer require livewire/livewire

# Install Tailwind & dependencies
npm install -D tailwindcss postcss autoprefixer @tailwindcss/forms @tailwindcss/typography
npm install alpinejs

# Install Laravel Octane with FrankenPHP
composer require laravel/octane
php artisan octane:install --server=frankenphp

cd ..
```

### 4. Copy Environment Files
```bash
# Development
cp .env.dev src/.env

# Production (saat deploy)
cp .env.prod src/.env
```

### 5. Setup Tailwind & Vite
Copy `tailwind.config.js` ke `src/`

Edit `src/resources/css/app.css`:
```css
@tailwind base;
@tailwind components;
@tailwind utilities;
```

Edit `src/resources/js/app.js`:
```javascript
import './bootstrap';
import 'livewire';
import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();
```

### 6. Build & Run Development
```bash
# Build containers
make dev-build

# Start services
make dev-up

# Install dependencies
make install

# Generate app key
make key-generate

# Run migrations
make migrate

# Build assets
make npm-build
```

### 7. Access Application
- **App**: http://localhost
- **Mailpit**: http://localhost:8025
- **MinIO Console**: http://localhost:9001 (user: minio, pass: minio123)

## Production Deployment

### 1. Prepare Environment
```bash
# Copy & edit production env
cp .env.prod .env

# IMPORTANT: Ubah semua password di .env:
# - DB_PASSWORD
# - REDIS_PASSWORD
# - AWS_SECRET_ACCESS_KEY
# - MINIO_ROOT_PASSWORD
```

### 2. Build & Deploy
```bash
# Build production images
make prod-build

# Start production
make prod-up

# Generate key (first time only)
docker-compose -f docker-compose.prod.yml exec app php artisan key:generate

# Run migrations
docker-compose -f docker-compose.prod.yml exec app php artisan migrate --force

# Build caches
make cache-build
```

### 3. Setup MinIO Bucket
```bash
# Access MinIO container
docker-compose -f docker-compose.prod.yml exec minio sh

# Create bucket
mc alias set local http://localhost:9000 $MINIO_ROOT_USER $MINIO_ROOT_PASSWORD
mc mb local/dashboard-al-iman
mc anonymous set download local/dashboard-al-iman
```

## Useful Commands

### Development
```bash
make dev-up          # Start dev environment
make dev-down        # Stop dev environment
make dev-logs        # View logs
make shell           # Access container shell
make tinker          # Laravel Tinker
make npm-watch       # Watch asset changes
```

### Production
```bash
make prod-up         # Start production
make prod-down       # Stop production
make prod-logs       # View logs
make cache-build     # Build production caches
```

### Database
```bash
make migrate         # Run migrations
make migrate-fresh   # Fresh migration + seed
make db-shell        # PostgreSQL shell
```

### Maintenance
```bash
make cache-clear     # Clear all caches
make clean           # Remove all containers & volumes
```

## Performance Tips

### 1. Opcache Verification
```bash
docker-compose -f docker-compose.prod.yml exec app php -i | grep opcache
```

### 2. Check JIT Status
```bash
docker-compose -f docker-compose.prod.yml exec app php -r "echo opcache_get_status()['jit']['enabled'] ? 'JIT Enabled' : 'JIT Disabled';"
```

### 3. Monitor Workers
```bash
docker-compose -f docker-compose.prod.yml exec app supervisorctl status
```

## Troubleshooting

### Permission Issues
```bash
docker-compose -f docker-compose.dev.yml exec app chown -R www-data:www-data storage bootstrap/cache
docker-compose -f docker-compose.dev.yml exec app chmod -R 775 storage bootstrap/cache
```

### Clear All Caches
```bash
make cache-clear
```

### Restart Specific Service
```bash
docker-compose -f docker-compose.dev.yml restart app
docker-compose -f docker-compose.dev.yml restart worker
```

## Security Checklist (Production)

- [ ] Change all passwords in `.env.prod`
- [ ] Set `APP_DEBUG=false`
- [ ] Configure proper `APP_URL`
- [ ] Setup SSL certificates (Let's Encrypt recommended)
- [ ] Enable firewall rules
- [ ] Setup backup for `postgres_data` & `storage_uploads`
- [ ] Configure proper SMTP for emails
- [ ] Review Caddyfile security headers
- [ ] Enable HTTPS in Caddyfile

## Backup Strategy

### Database Backup
```bash
docker-compose -f docker-compose.prod.yml exec postgres pg_dump -U postgres dashboard_al_iman > backup_$(date +%Y%m%d).sql
```

### Restore Database
```bash
cat backup_20240101.sql | docker-compose -f docker-compose.prod.yml exec -T postgres psql -U postgres dashboard_al_iman
```

### Volume Backup
```bash
docker run --rm -v dashboard-al-iman_storage_uploads:/data -v $(pwd):/backup alpine tar czf /backup/uploads_backup.tar.gz -C /data .
```