<?php

require_once __DIR__.DIRECTORY_SEPARATOR.'helpers.php';
require_once xpath(__DIR__, '../vendor/jneuendorf/php_adt/php_adt/init.php');


function dict(...$args) {
    return new \php_adt\Dict(...$args);
}

class_alias('\php_adt\Dict', 'dict');
