<?php
require_once __DIR__.DIRECTORY_SEPARATOR.'function_scopes.php';


// $__FUNC_NAMES = [];

function __named_function($name, $func, $decorators=array()) {
    // global $__FUNC_NAMES;
    // $FUNC
    // $func->__name__ = $name;
    // TODO: use dict class
    foreach ($decorators as $decorator) {
        $func = $decorator($func);
    }
    return $func;
}

function __print($objects, $sep='', $end="\n", $file=null, $flash=False) {
    echo implode($sep, $objects).$end;
}

$Printer__print = function($self, $args, $kwargs) {
    echo "I'm the old guy...";
    echo $args, $kwargs, "\n";
};

class Printer {
    function __print($args, $kwargs) {
        return $Printer__print($this, $args, $kwargs);
    }
}

// early binding (also for lookups etc.)
$__log_to_default_logger = '__print';
$log_to = function($logger=null) use ($_log_to_default_logger) {
    __register_scope(10, 0);
    if ($logger === null) {
        $logger = $__log_to_default_logger;
    }
    __set_var(10, 'logger', $logger);

    __set_var(10, 'decorator', function($func) {
        __register_scope(11, 10);
        __set_var(11, 'wrapper', function($args, $kwargs) {
            __register_scope(12, 11);
            call_user_func(
                __get_var(12, 'logger'),
                "{".__get_var(12, 'func')->__qualname__."}(*$args, **$kwargs)"
            );
            call_user_func(__get_var(12, 'func'), $args, $kwargs);
        });
        return __get_var(11, 'wrapper');
    });
    return __get_var(10, 'decorator');
};

__set_var(0, 'printer', new Printer());


__set_var(0, 'foo', __named_function(
    'foo',
    function($a, $b) {
        __register_scope(21, 0);
        call_user_func(
            __get_attr(__get_var(21, 'printer'), '__print'),
            'inside the function'
        );
        // NOTE: `+` will become `__add`
        return $a + $b;
    },
    array($log_to)
));

__set_var(0, 'print_function', __named_function('print_function', function($self, $args, $kwargs) {
    __register_scope(22, 0);
    echo 'I\'m the new guy';
    // DON'T USE __get_var when variable is part of the arguments
    echo $args, $kwargs;
}));

$Printer__print = __get_var(0, 'print_function');

call_user_func(__get_var(0, 'foo'), 1, 2);

echo "\n";

__set_var(0, 'func', __named_function('func', function() {
    __register_scope(23, 0);
    // 1. looked up when run
    // TODO: ?!
    // 2. the scope it's looked up in is determined at "compile-time"
    echo 'func: item = ', __get_var(23, 'item'), "\n";
}));

// NOTE: compile range(3) to range(0,2)
foreach (range(0, 2) as $item) {
    // NOTE: register item in scope
    __set_var(0, 'item', $item);
    call_user_func(__get_var(0, 'func'));
}


// # and this does weird stuff, because list comprehensions have their own scope
// # -> does not change the value of `item`
// [func() for item in range(3)]


// __set_var(0, 'decorator', function($func) {
//     __register_scope(24, 0);
//     __set_var(24, 'called', 0);
//
//     __set_var(24, 'wrapper', function() {
//         __register_scope(241, 24);
//         // NOTE: python's `nonlocal` creates different `store`
//         __set_var_nonlocal(241, 'called', __get_var(241, 'called') + 1);
//         call_user_func(__get_var(241, 'func'));
//         echo 'called <func_name> '.__get_var(241, 'called').' time(s)', "\n";
//     });
//     return __get_var(24, 'wrapper');
// });

// def decorator(func):
//     called = 0
//
//     def wrapper(*args, **kwargs):
//         nonlocal called
//         called = called + 1
//         func(*args, **kwargs)
//         print("called", func.__qualname__, called, "time(s)")
//     return wrapper

$decorator = function($func) {
    $called = 0;
    $wrapper = function() use (&$func, &$called) {
        $called = $called + 1;
        $func();
        echo 'called <func_name> '.$called.' time(s)', "\n";
    };
    return $wrapper;
};

__set_var(0, 'foo', __named_function(
    'foo',
    function() {},
    // [__get_var(0, 'decorator')]
    [$decorator]
));

__set_var(0, 'bar', __named_function(
    'bar',
    function() {},
    // [__get_var(0, 'decorator')]
    [$decorator]
));


call_user_func(__get_var(0, 'foo'));
call_user_func(__get_var(0, 'bar'));
call_user_func(__get_var(0, 'foo'));
