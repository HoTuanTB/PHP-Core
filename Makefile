build:
	docker-compose up -d --build
up:
	docker-compose up -d
php:
	docker-compose exec php bash
down:
	docker-compose down
migrate:
	php artisan migrate
seed:
	php artisan db:seed
