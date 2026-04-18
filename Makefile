.PHONY: help dev-up dev-down dev-build prod-up prod-down prod-build install migrate seed cache-clear logs

help: ## Show this help message
	@echo 'Usage: make [target]'
	@echo ''
	@echo 'Available targets:'
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "  \033[36m%-20s\033[0m %s\n", $$1, $$2}'

# Development
dev-up: ## Start development environment
	docker compose -f docker-compose.dev.yml up -d

dev-down: ## Stop development environment
	docker compose -f docker-compose.dev.yml down

dev-build: ## Build development containers
	docker compose -f docker-compose.dev.yml build --no-cache

dev-logs: ## Show development logs
	docker compose -f docker-compose.dev.yml logs -f

# Production
prod-up: ## Start production environment
	docker compose -f docker-compose.prod.yml up -d

prod-down: ## Stop production environment
	docker compose -f docker-compose.prod.yml down

prod-build: ## Build production containers
	docker compose -f docker-compose.prod.yml build --no-cache

prod-logs: ## Show production logs
	docker compose -f docker-compose.prod.yml logs -f

# Laravel Commands
install: ## Install Laravel dependencies
	docker compose -f docker-compose.dev.yml exec app composer install
	docker compose -f docker-compose.dev.yml exec app npm install

key-generate: ## Generate application key
	docker compose -f docker-compose.dev.yml exec app php artisan key:generate

migrate: ## Run database migrations
	docker compose -f docker-compose.dev.yml exec app php artisan migrate

migrate-fresh: ## Fresh migration with seed
	docker compose -f docker-compose.dev.yml exec app php artisan migrate:fresh --seed

seed: ## Run database seeders
	docker compose -f docker-compose.dev.yml exec app php artisan db:seed

cache-clear: ## Clear all caches
	docker compose -f docker-compose.dev.yml exec app php artisan optimize:clear

cache-build: ## Build caches for production
	docker compose -f docker-compose.prod.yml exec app php artisan config:cache
	docker compose -f docker-compose.prod.yml exec app php artisan route:cache
	docker compose -f docker-compose.prod.yml exec app php artisan view:cache

shell: ## Access app container shell
	docker compose -f docker-compose.dev.yml exec app sh

tinker: ## Laravel Tinker
	docker compose -f docker-compose.dev.yml exec app php artisan tinker

# Assets
pnpm-dev: ## Run npm dev
	docker compose -f docker-compose.dev.yml exec app pnpm dev

pnpm-build: ## Build assets for production
	docker compose -f docker-compose.dev.yml exec app pnpm build

pnpm-ci: ## Install pnpm dependencies
	docker compose -f docker-compose.dev.yml exec app pnpm install

pnpm-watch: ## Watch assets changes
	docker compose -f docker-compose.dev.yml exec app pnpm dev --watch

# Database
db-shell: ## Access PostgreSQL shell
	docker compose -f docker-compose.dev.yml exec postgres psql -U postgres -d dashboard_al_iman

# Cleanup
clean: ## Remove all containers, volumes and images
	docker compose -f docker-compose.dev.yml down -v --rmi all
	docker compose -f docker-compose.prod.yml down -v --rmi all