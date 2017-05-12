PHP_TEST_SETUP_FILES = ./php/test/oop.php


tests:
	./vendor/phpunit/phpunit/phpunit --bootstrap $(PHP_TEST_SETUP_FILES) --no-globals-backup ./php/test/OopTest
