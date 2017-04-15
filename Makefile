up: vendor
	PHPDOXINIZER_CONFIG=phpdoxinizer.json php -d variables_order=EGPCS -S 127.0.0.1:8111 -t public/ public/router.php

vendor:
	composer install

.PHONY: vendor up

