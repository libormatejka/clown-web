DC = docker-compose --project-name clownweb -f .docker/docker-compose.yml

.PHONY: up down install migrate seed build smoke fix-perms exec\:php exec\:node init db-drob db-create db-load db clean-all clean-temp clean-log clean-tempMedia

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

# Vstup do PHP kontejneru
exec\:php:
	$(DC) exec php sh

# Vstup do Node kontejneru
exec\:node:
	$(DC) exec node sh

# Spustí celý projekt znovu od nuly
init:
	$(DC) up -d --build
	$(DC) run --rm -T -e COMPOSER_AUTH php composer install --no-interaction
	$(DC) exec -T node npm install
	make fix-perms
	make db

#DB
db-drob:
	$(DC) exec -T -u www-data php php bin/console orm:schema-tool:drop --force --full-database

db-create:
	$(DC) exec -T -u www-data php php bin/console orm:schema-tool:create

db-load:
	$(DC) exec -T -u www-data php php bin/console app:seed

db:
	make db-drob
	make db-create
	make db-load

.PHONY: clean-all clean-temp clean-log clean-tempMedia
clean-all:
	make clean-temp
	make clean-log
	make clean-tempMedia

# Soubory v temp/log vytváří kontejner (uid www-data), proto se mažou uvnitř kontejneru jako root
clean-temp:
	$(DC) exec -T php sh -c 'rm -rf temp && mkdir temp && chmod 777 temp'

clean-log:
	$(DC) exec -T php sh -c 'rm -rf log && mkdir log && chmod 777 log'

clean-tempMedia:
	$(DC) exec -T php sh -c 'rm -rf www/media/temp && mkdir www/media/temp && chmod 777 www/media/temp'
