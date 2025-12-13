# Dashboard Al-Iman

High-performance TALL Stack application dengan Laravel Octane (FrankenPHP) + JIT + Opcache.

## Tech Stack

- **Framework**: Laravel 12
- **Frontend**: Livewire 3 + Alpine.js + Tailwind CSS
- **Server**: Laravel Octane dengan FrankenPHP
- **Database**: PostgreSQL 17 (Alpine)
- **Cache**: Redis 7
- **Storage**: MinIO (S3-compatible)
- **Mail**: Mailpit (development)
- **Queue**: Redis dengan dedicated worker containers
- **PHP**: 8.4 dengan Opcache + JIT enabled

## Features

✅ **High Performance**
- FrankenPHP dengan worker mode (2 workers, max 1000 requests)
- PHP 8.4 dengan JIT compilation (256MB buffer)
- Opcache optimized untuk production
- Redis untuk session & cache
- Static file caching dengan proper headers

✅ **Development Friendly**
- Docker Compose untuk development & production
- Xdebug support untuk debugging
- Hot reload dengan Vite
- Mailpit untuk email testing
- MinIO untuk S3-compatible storage testing

✅ **Production Ready**
- Multi-stage Docker builds
- Health checks untuk semua services
- Resource limits & reservations
- Automated queue workers (2 replicas)
- Comprehensive logging
- Security headers configured

✅ **Scalable Architecture**
- Separate worker containers
- Redis-based queue system
- Database connection pooling
- Horizontal scaling ready

## Quick Start

```bash
# Clone repository
git clone <repository-url> dashboard-al-iman
cd dashboard-al-iman

# Lihat setup lengkap
cat SETUP.md

# Quick development start
make dev-build
make dev-up
make install
make key-generate
make migrate
make npm-build
```

Access: http://localhost

## Documentation

- [Setup Instructions](SETUP.md) - Detailed installation guide
- [Makefile Commands](#useful-commands) - Quick command reference

## Useful Commands

```bash
make help            # Show all available commands
make dev-up          # Start development
make prod-up         # Start production
make shell           # Access container
make db-shell        # PostgreSQL shell
make migrate         # Run migrations
make cache-clear     # Clear caches
```

## Project Structure

```
dashboard-al-iman/
├── docker/
│   ├── frankenphp/
│   │   ├── Dockerfile.dev       # Development image
│   │   ├── Dockerfile.prod      # Production image
│   │   ├── php.ini              # PHP configuration
│   │   └── Caddyfile            # FrankenPHP config
│   └── supervisor/
│       └── supervisord.conf     # Queue worker config
├── src/                         # Laravel application
├── docker-compose.dev.yml       # Development environment
├── docker-compose.prod.yml      # Production environment
├── Makefile                     # Automation commands
└── SETUP.md                     # Setup instructions
```

## Performance Benchmarks

**Opcache Stats:**
- Memory: 512MB (production)
- Interned Strings: 64MB
- Max Files: 65,536
- JIT Buffer: 256MB

**Resource Limits (Production):**
- App: 1.5 CPU / 1GB RAM
- Worker: 0.5 CPU / 512MB RAM (x2)
- PostgreSQL: 0.5 CPU / 512MB RAM
- Redis: 0.3 CPU / 256MB RAM

## Environment Variables

See `.env.dev` for development and `.env.prod` for production configuration.

**Important:** Always change default passwords in production!

## Security

- All services internal by default in production
- Security headers configured in Caddyfile
- CSRF protection enabled
- XSS protection enabled
- Password hashing with bcrypt
- Rate limiting configured

## Contributing

1. Fork the repository
2. Create feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Open Pull Request

## License

MIT License

## Support

For issues and questions, please open an issue on GitHub.

---

Made with ❤️ for Dashboard Al-Iman