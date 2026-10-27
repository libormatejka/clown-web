DC = docker-compose --project-name clownweb -f .docker/docker-compose.yml

.PHONY: up down install migrate seed build smoke fix-perms

up:
	$(DC) up -d

down:
	$(DC) down

# Privátní balíčky potřebují GitHub token: COMPOSER_AUTH='{"github-oauth":{"github.com":"..."}}' make install
install:
	$(DC) run --rm -T -e COMPOSER_AUTH php composer install --no-interaction

migrate:
	$(DC) exec -T -u www-data php php bin/console migrations:migrate --no-interaction

seed:
	$(DC) exec -T -u www-data php php bin/console app:seed

build:
	$(DC) exec -T node npm run build

smoke:
	python3 tools/smoke-test.py $(EMAIL) $(PASSWORD)

# Kontejner spouští konzoli jako root; PHP-FPM běží jako www-data
fix-perms:
	$(DC) exec -T php sh -c 'chmod -R a+rwX temp log'
