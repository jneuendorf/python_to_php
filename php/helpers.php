<?php


// This is necessary because in PHP `(function() {})()` is invalid syntax.
// But e.g. for list comprehensions everything must happen inside an expression
// and a function call is an expression.
function __call_func($func) {
    $args = func_get_args();
    return call_user_func_array($args[0], array_slice($args, 1));
}
