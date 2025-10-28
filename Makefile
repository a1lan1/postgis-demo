sail-rebuild:
	./vendor/bin/sail down -v
	./vendor/bin/sail build --no-cache
	./vendor/bin/sail up -d

clear:
	php artisan config:clear
	php artisan cache:clear
	php artisan route:clear
	php artisan optimize:clear

test:
	rm -rf coverage coverage.xml public/build
	yarn build
	yarn playwright install
	php artisan config:clear --env=testing
	php artisan test --coverage --parallel
# 	php artisan test --parallel --group=browser
# 	php artisan test tests/Feature --parallel

lint:
	make ide
	./vendor/bin/phpstan analyse
	./vendor/bin/rector process --ansi
	./vendor/bin/pint --parallel
	yarn format
	yarn lint

ide:
	php artisan ide-helper:generate
	php artisan ide-helper:models -RW
	php artisan ide-helper:meta
