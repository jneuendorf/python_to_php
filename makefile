PHP = php
PHP_UNIT = ./vendor/phpunit/phpunit/phpunit

tests:
	@# https://stackoverflow.com/a/12141716/6928824
	$(PHP) -d xdebug.profiler_enable=on $(PHP_UNIT) --bootstrap ./php/test/phpunit_bootstrap.php --no-globals-backup ./php/test
