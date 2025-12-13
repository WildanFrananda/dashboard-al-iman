# Dashboard Al-Iman - Developer Specification

## 📋 Overview
A dashboard application built with the TALL Stack (Tailwind, Alpine.js, Livewire, Laravel), focusing on **high performance** by using Laravel Octane + FrankenPHP as the application server.

---

## 🏗️ Architecture Stack

### Backend
- **Framework**: Laravel 12 (latest)
- **PHP**: 8.4 (Alpine Linux based)
- **Server**: FrankenPHP (Worker Mode) via Laravel Octane
- **Database**: PostgreSQL 17 Alpine
- **Cache/Session**: Redis 7 Alpine
- **Queue Driver**: Redis
- **Storage**: MinIO (S3-compatible)

### Frontend
- **UI Framework**: Livewire 3 (full-page components)
- **JavaScript**: Alpine.js (minimal, reactive)
- **CSS**: Tailwind CSS 3 (utility-first)
- **Build Tool**: Vite (HMR, fast refresh)

### Infrastructure
- **Container**: Docker + Docker Compose
- **Process Manager**: Supervisor (untuk queue workers)
- **Web Server**: Caddy (via FrankenPHP)

---

## ⚙️ Performance Configuration

### 1. PHP Opcache + JIT
**Development:**
```ini
opcache.enable=1
opcache.memory_consumption=256MB
opcache.validate_timestamps=1       # File changes detected
opcache.revalidate_freq=2           # Check every 2 seconds
opcache.jit=tracing
opcache.jit_buffer_size=128MB
```

**Production:**
```ini
opcache.enable=1
opcache.memory_consumption=512MB
opcache.validate_timestamps=0       # No file checking (faster)
opcache.save_comments=0             # Strip comments
opcache.jit=tracing
opcache.jit_buffer_size=256MB
opcache.max_accelerated_files=65536
```

**Benefit**: 
- 2–3x faster response time
- More efficient memory usage
- Significantly reduced CPU usage

### 2. FrankenPHP Worker Mode
```
Worker Count: 2 (optimized for a dual-core CPU)
Max Requests per Worker: 1000
```

**How it works:**
- Laravel is booted once when the container starts
- Workers handle requests without re-bootstrapping
- After 1000 requests, workers automatically restart (to prevent memory leaks)

**Benefit**:
- Bootstrap overhead ~50-100ms hilang
- Request handling ~10-50ms (sangat cepat)
- Memory persistent antar request

### 3. Redis Configuration
**Development:**
- No password
- Simple key-value

**Production:**
```bash
requirepass: strong_password
maxmemory: 256MB
maxmemory-policy: allkeys-lru  # Otomatis hapus key jarang dipakai
appendonly: yes                 # Persistence enabled
```

**Usage:**
- Session storage (fast, scalable)
- Cache (Laravel cache facade)
- Queue backend (jobs, notifications)

### 4. PostgreSQL Optimization
**Version**: 17 (Alpine - image size ~80MB)

**Benefits Alpine:**
- Image size kecil (bandwidth efficient)
- Fast container startup
- Security updates frequent

**Connection:**
- Internal network only (production)
- Persistent volume untuk data
- Health check configured

---

## 🐳 Docker Architecture

### Development Environment (`docker-compose.dev.yml`)

**Services:**
1. **app** (FrankenPHP + Laravel)
   - Port: 80, 443
   - Volume: `./src` mounted (live code sync)
   - Xdebug: Enabled port 9003
   - Hot reload: Via Vite

2. **postgres** (Database)
   - Port: 5432 (tidak exposed, internal only)
   - Volume: `postgres_data` (persistent)
   - Credentials: postgres/secret

3. **redis** (Cache & Queue)
   - Port: 6379 (internal)
   - Volume: `redis_data` (persistent)
   - Mode: Append-only file (AOF)

4. **mailpit** (Email Testing)
   - SMTP: 1025 (internal)
   - Web UI: 8025 (http://localhost:8025)
   - All emails caught, tidak terkirim real

5. **minio** (S3 Storage)
   - API: 9000 (internal)
   - Console: 9001 (http://localhost:9001)
   - Credentials: minio/minio123

6. **worker** (Queue Worker)
   - 1 instance di development
   - Process queue jobs asynchronously

**Development Features:**
- Code changes langsung terdeteksi (no rebuild)
- Xdebug untuk step-by-step debugging
- Mailpit catch all emails
- MinIO untuk test upload files
- Full logging enabled

### Production Environment (`docker-compose.prod.yml`)

**Key Differences:**

1. **Multi-stage Build**
   - Build stage: Install dependencies + optimize
   - Final stage: Runtime only (smaller image)
   - No dev dependencies included

2. **Security:**
   - All passwords dari environment variables
   - No ports exposed except 80/443
   - Debug mode: OFF
   - Opcache validate_timestamps: OFF

3. **Resource Limits:**
   ```yaml
   app:
     cpus: 1.5
     memory: 1GB
   
   worker:
     cpus: 0.5
     memory: 512MB
     replicas: 2  # 2 worker instances
   
   postgres:
     cpus: 0.5
     memory: 512MB
   
   redis:
     cpus: 0.3
     memory: 256MB
   ```

4. **Health Checks:**
   - PostgreSQL: `pg_isready` check
   - Redis: ping check
   - MinIO: HTTP health endpoint
   - Retry logic configured

5. **Restart Policy:**
   - `unless-stopped`: Auto restart on failure
   - Persistent antar reboot server

---

## 📁 File Structure

```
dashboard-al-iman/
├── docker/
│   ├── frankenphp/
│   │   ├── Dockerfile.dev          # Dev image dengan Xdebug
│   │   ├── Dockerfile.prod         # Prod image optimized
│   │   ├── php.ini                 # Custom PHP settings
│   │   └── Caddyfile               # FrankenPHP web server config
│   └── supervisor/
│       └── supervisord.conf        # Queue worker process manager
│
├── src/                            # Laravel 12 application
│   ├── app/
│   │   ├── Livewire/              # Livewire components di sini
│   │   ├── Models/
│   │   ├── Http/
│   │   └── ...
│   ├── resources/
│   │   ├── views/                 # Blade templates
│   │   ├── css/
│   │   │   └── app.css            # Tailwind imports
│   │   └── js/
│   │       └── app.js             # Alpine.js + Livewire
│   ├── database/
│   ├── routes/
│   ├── config/
│   │   └── octane.php             # Octane configuration
│   ├── tailwind.config.js         # Tailwind configuration
│   ├── vite.config.js             # Vite build config
│   ├── composer.json
│   ├── package.json
│   └── .env                       # Environment (gitignored)
│
├── docker-compose.dev.yml         # Development orchestration
├── docker-compose.prod.yml        # Production orchestration
├── .env.dev                       # Dev environment template
├── .env.prod                      # Prod environment template
├── Makefile                       # Command shortcuts
├── .gitignore
├── README.md
├── SETUP.md
└── DEVELOPER_SPEC.md             # This file
```

---

## 🔧 Environment Variables

### Critical Variables (Must Change in Production)

```bash
# Application
APP_KEY=                           # Generate via: php artisan key:generate
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

# Database
DB_PASSWORD=                       # CHANGE THIS!

# Redis
REDIS_PASSWORD=                    # CHANGE THIS!

# MinIO S3
AWS_ACCESS_KEY_ID=                 # CHANGE THIS!
AWS_SECRET_ACCESS_KEY=             # CHANGE THIS!
MINIO_ROOT_USER=                   # Same as AWS_ACCESS_KEY_ID
MINIO_ROOT_PASSWORD=               # Same as AWS_SECRET_ACCESS_KEY

# Mail (Production SMTP)
MAIL_HOST=smtp.mailtrap.io
MAIL_USERNAME=
MAIL_PASSWORD=
```

### Storage Configuration

**Development:**
```bash
FILESYSTEM_DISK=local
AWS_ENDPOINT=http://minio:9000
```

**Production:**
```bash
FILESYSTEM_DISK=s3
AWS_ENDPOINT=http://minio:9000
AWS_BUCKET=dashboard-al-iman
AWS_USE_PATH_STYLE_ENDPOINT=true
```

---

## 🚀 Deployment Workflow

### Development Workflow

```bash
# 1. Initial setup
make dev-build          # Build containers
make dev-up             # Start services
make install            # composer + npm install
make key-generate       # Generate APP_KEY
make migrate            # Run migrations
make npm-build          # Build assets

# 2. Daily development
make dev-up             # Start
make npm-watch          # Watch asset changes
make dev-logs           # Monitor logs
make tinker             # Laravel tinker
make shell              # Container shell access

# 3. Database operations
make migrate            # Run new migrations
make migrate-fresh      # Fresh database
make db-shell           # PostgreSQL CLI
```

### Production Deployment

```bash
# 1. Preparation
# - Edit .env.prod dengan credentials real
# - Push code ke repository
# - SSH ke production server

# 2. Deploy
make prod-build         # Build optimized images
make prod-up            # Start containers

# 3. First time setup
docker-compose -f docker-compose.prod.yml exec app php artisan key:generate
docker-compose -f docker-compose.prod.yml exec app php artisan migrate --force

# 4. Optimize
make cache-build        # Cache config, routes, views

# 5. Setup MinIO bucket
docker-compose -f docker-compose.prod.yml exec minio sh
mc alias set local http://localhost:9000 $MINIO_ROOT_USER $MINIO_ROOT_PASSWORD
mc mb local/dashboard-al-iman
mc anonymous set download local/dashboard-al-iman
exit

# 6. Monitor
make prod-logs          # Check logs
```

### Deployment Checklist

**Before Deploy:**
- [ ] Change all passwords in `.env.prod`
- [ ] Set `APP_DEBUG=false`
- [ ] Configure correct `APP_URL`
- [ ] Setup SSL certificates
- [ ] Test database connection
- [ ] Backup existing data (if updating)

**After Deploy:**
- [ ] Verify services health: `docker ps`
- [ ] Check logs: `make prod-logs`
- [ ] Test application access
- [ ] Verify queue workers: `docker-compose -f docker-compose.prod.yml exec app supervisorctl status`
- [ ] Check Opcache status: `docker-compose -f docker-compose.prod.yml exec app php -i | grep opcache`
- [ ] Setup monitoring/alerting
- [ ] Configure backup cron jobs

---

## 🎨 Frontend Development

### Livewire Components

**Best Practices:**
```php
// app/Livewire/UserTable.php
namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;

class UserTable extends Component
{
    use WithPagination;
    
    public $search = '';
    
    public function render()
    {
        return view('livewire.user-table', [
            'users' => User::where('name', 'like', "%{$this->search}%")
                ->paginate(10)
        ]);
    }
}
```

**Blade Template:**
```blade
<div>
    <input wire:model.live="search" type="text" placeholder="Search...">
    
    <table>
        @foreach($users as $user)
            <tr>
                <td>{{ $user->name }}</td>
            </tr>
        @endforeach
    </table>
    
    {{ $users->links() }}
</div>
```

### Tailwind Usage

**Purge Configuration sudah optimal:**
```js
content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./app/Livewire/**/*.php",  // Scan Livewire components
]
```

**Development:**
```bash
make npm-watch    # Auto-rebuild on changes
```

**Production:**
```bash
make npm-build    # Minified, purged CSS
```

### Alpine.js Integration

**Minimal usage, contoh:**
```html
<div x-data="{ open: false }">
    <button @click="open = !open">Toggle</button>
    <div x-show="open">Content</div>
</div>
```

---

## 🔄 Queue System

### Queue Configuration

**Driver:** Redis

**Queue Names:**
- `default` - General jobs
- `high` - Priority jobs (emails, notifications)
- `low` - Background processing

**Worker Setup:**

Development: 1 worker container
Production: 2 worker containers (replicas)

**Supervisor Config:**
```ini
[program:queue-worker]
command=php artisan queue:work redis --tries=3 --queue=default,high,low
numprocs=2              # 2 processes
autorestart=true
```

### Creating Jobs

```bash
php artisan make:job ProcessUpload
```

```php
// app/Jobs/ProcessUpload.php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class ProcessUpload implements ShouldQueue
{
    use Queueable;
    
    public function __construct(public $file) {}
    
    public function handle()
    {
        // Process file
    }
}
```

**Dispatch:**
```php
ProcessUpload::dispatch($file)->onQueue('high');
```

### Monitoring Queues

```bash
# Check failed jobs
php artisan queue:failed

# Retry failed job
php artisan queue:retry {id}

# Monitor in real-time
php artisan queue:monitor
```

---

## 📊 Monitoring & Logging

### Application Logs

**Location:**
- Development: `src/storage/logs/laravel.log`
- Production: Volume `app_logs`

**View logs:**
```bash
make dev-logs          # Follow all service logs
make shell             # Then: tail -f storage/logs/laravel.log
```

### Performance Monitoring

**Check Opcache:**
```bash
docker-compose -f docker-compose.prod.yml exec app php -r "print_r(opcache_get_status());"
```

**Check JIT:**
```bash
docker-compose -f docker-compose.prod.yml exec app php -r "echo opcache_get_status()['jit']['enabled'] ? 'Enabled' : 'Disabled';"
```

**Check Workers:**
```bash
docker-compose -f docker-compose.prod.yml exec app supervisorctl status
```

### Database Queries

**Enable query logging (development):**
```php
// In AppServiceProvider boot()
DB::listen(function ($query) {
    Log::info($query->sql, $query->bindings);
});
```

---

## 🔐 Security Considerations

### 1. Environment Variables
- Never commit `.env` files
- Use strong passwords (min 16 chars)
- Rotate credentials regularly

### 2. Caddy Security Headers
```
X-Content-Type-Options: nosniff
X-Frame-Options: SAMEORIGIN
X-XSS-Protection: 1; mode=block
Referrer-Policy: no-referrer-when-downgrade
```

### 3. Laravel Security
- CSRF protection (auto-enabled)
- SQL injection prevention (Eloquent ORM)
- XSS prevention (Blade auto-escaping)
- Mass assignment protection

### 4. File Uploads
```php
// Always validate uploads
$request->validate([
    'avatar' => 'required|image|max:2048', // Max 2MB
]);

// Store safely
$path = $request->file('avatar')->store('avatars', 's3');
```

### 5. Rate Limiting
```php
// routes/web.php
Route::middleware('throttle:60,1')->group(function () {
    // 60 requests per minute
});
```

---

## 🧪 Testing

### Setup Testing Database

```bash
# Add to .env.testing
DB_CONNECTION=pgsql
DB_HOST=postgres
DB_DATABASE=dashboard_al_iman_test
```

### Run Tests

```bash
docker-compose -f docker-compose.dev.yml exec app php artisan test
```

### Livewire Testing

```php
use Livewire\Livewire;

test('can search users', function () {
    Livewire::test(UserTable::class)
        ->set('search', 'John')
        ->assertSee('John Doe');
});
```

---

## 📈 Scaling Considerations

### Horizontal Scaling

**Ready for:**
- Multiple app containers (load balancer needed)
- Multiple worker containers (already 2 in production)
- Redis cluster (for high availability)
- PostgreSQL replicas (read-only replicas)

### Vertical Scaling

**Adjust resources in docker-compose.prod.yml:**
```yaml
deploy:
  resources:
    limits:
      cpus: '2.0'      # Increase CPU
      memory: 2G       # Increase RAM
```

### Caching Strategy

**Use Laravel Cache:**
```php
// Cache for 1 hour
$users = Cache::remember('all_users', 3600, function () {
    return User::all();
});
```

---

## 🆘 Troubleshooting

### Common Issues

**1. Permission Denied (storage/logs)**
```bash
make shell
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
```

**2. Database Connection Refused**
- Check container running: `docker ps`
- Check credentials in `.env`
- Verify network: `docker network inspect dashboard-al-iman_app-network`

**3. Assets Not Loading**
```bash
make npm-build
make cache-clear
```

**4. Queue Not Processing**
```bash
# Check worker status
docker-compose -f docker-compose.dev.yml exec app supervisorctl status

# Restart workers
docker-compose -f docker-compose.dev.yml restart worker
```

**5. Xdebug Not Working**
- Check IDE listening on port 9003
- Verify `xdebug.client_host=host.docker.internal`
- VSCode: Install "PHP Debug" extension

---

## 📚 Additional Resources

- [Laravel Documentation](https://laravel.com/docs/12.x)
- [Livewire Documentation](https://livewire.laravel.com/docs)
- [FrankenPHP Documentation](https://frankenphp.dev/)
- [Tailwind CSS Documentation](https://tailwindcss.com/docs)
- [Alpine.js Documentation](https://alpinejs.dev/)

---

## 💡 Development Tips

1. **Use Livewire untuk interactive UI** - Hindari JavaScript manual
2. **Leverage Tailwind utilities** - Jangan buat custom CSS kecuali perlu
3. **Monitor Opcache di production** - Pastikan hit rate >95%
4. **Use queue untuk heavy tasks** - Email, file processing, etc
5. **Test di container** - `make shell` untuk debugging
6. **Check logs regularly** - `make dev-logs` atau `make prod-logs`
7. **Use Redis untuk session** - Sudah configured, scalable
8. **Optimize queries** - Use eager loading, avoid N+1

---

**Dibuat untuk**: Dashboard Al-Iman Development Team  
**Last Updated**: December 2025  
**Maintainer**: DevOps Team