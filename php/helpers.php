<?php

// Generates a cross-platform path string from a path string of the form 'dir/subdir/file'.
// Used for `require_once`.
function xpath($dir, $path) {
    return implode(DIRECTORY_SEPARATOR, array_merge([$dir], explode('/', $path)));
}

// // This is necessary because in PHP `(function() {})()` is invalid syntax.
// // But e.g. for list comprehensions everything must happen inside an expression
// // and a function call is an expression.
// function __call_func($func) {
//     $args = func_get_args();
//     return call_user_func_array($args[0], array_slice($args, 1));
// }

// call a closure object attached to `$object` and set $self as first argument
function __call_func($object, $method_name, ...$args) {
    return call_user_func($object->$method_name, $object, ...$args);
}
