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

function py_print($objects, $sep='', $end="\n", $file=null, $flash=False) {
    echo implode($sep, $objects).$end;
}

function qualname($obj) {
    // TODO: replace with helper method
    if (is_object($obj) && method_exists($object, '__qualname__')) {
        // TODO: this must be done differently because the qualname contains info that the object is unaware of
        return $obj->__qualname__();
    }
    return '<qualname>';
}

// returns a callable object
function get_method() {}

// Dynamically change the method of an object (-> doesn't affect the class -> new instances will keep the previously active method)
function set_method($obj, $method_name, $method) {
    $cls = get_class($obj);
    if (property_exists($cls, '__slots__') && in_array($method_name, $cls::$__slots__)) {
        $obj->$method_name = $method;
    }
    else {
        throw new \AttributeError("'$cls' object has no attribute '$method_name'" ,1);
    }
}


function Printer__print($self, ...$args) {
    echo "I'm the old guy...\n";
    echo '(*'.implode(',', $args).')', "\n";
}
$Printer__print = 'Printer__print';

class Printer {
    function __print(...$args) {
        global $Printer__print;
        // echo "in printer->__print\n";
        return $Printer__print($this, ...$args);
    }
}

// early binding (also for lookups etc.)
$_GLOBALS['__log_to_default_logger'] = $Printer__print;
$log_to = function($logger=null) {
    if ($logger === null) {
        echo 'using default', "\n";
        $logger = $_GLOBALS['__log_to_default_logger'];
    }

    $decorator = function($func) use (&$logger) {
        $wrapper = function(...$args) use (&$logger, &$func) {
            $logger(null, qualname($func)."(*".implode(',', $args).")");
            $func(...$args);
        };
        return $wrapper;
    };
    return $decorator;
};

$printer = new Printer();


$foo = __named_function(
    'foo',
    function($a, $b) use (&$printer) {
        // http://php.net/manual/en/language.types.callable.php
        call_user_func(
            // TODO
            // __get_attr($printer, '__print'),
            [$printer, '__print'],
            'inside the function'
        );
        // NOTE: `+` will become `double_dispatch()`
        return $a + $b;
    },
    // [$log_to([$printer, '__print'])]
    [$log_to($Printer__print)]
);

$print_function = __named_function('print_function', function($self, ...$args) {
    echo 'I\'m the new guy', "\n";
    // DON'T USE __get_var when variable is part of the arguments
    echo implode(',', $args), "\n";
});

$Printer__print = $print_function;

call_user_func($foo, 1, 2);

echo "\n\n";

$func = __named_function('func', function() use (&$item) {
    // 1. looked up when run
    // TODO: ?!
    // 2. the scope it's looked up in is determined at "compile-time"
    echo 'func: item = ', $item, "\n";
});

// NOTE: compile range(3) to range(0,2)
foreach (range(0, 2) as $item) {
    call_user_func($func);
}


// # and this does weird stuff, because list comprehensions have their own scope
// # -> does not change the value of `item`
// [func() for item in range(3)]

$decorator = function($func) {
    $called = 0;
    $wrapper = function() use (&$func, &$called) {
        $called = $called + 1;
        $func();
        echo 'called <func_name> '.$called.' time(s)', "\n";
    };
    return $wrapper;
};

$foo = __named_function(
    'foo',
    function() {},
    [$decorator]
);

$bar = __named_function(
    'bar',
    function() {},
    [$decorator]
);


call_user_func($foo);
call_user_func($bar);
call_user_func($foo);



// lookup load/store cases
// 1.
// def a():
//     # local
//     a = 1
//     def b():
//         # implicit nonlocal load lookup (only)
//         print(a)
$a = function() {
    $a = 1;
    $b = function() use (&$a) {
        echo $a;
    };
};
// 2.
// def a():
//     # local
//     a = 1
//     def b():
//         nonlocal a
//         # explicit nonlocal load lookup
//         print(a)
//         # nonlocal store
//         a = 2
$a = function() {
    $a = 1;
    $b = function() use (&$a) {
        echo $a;
        $a = 2;
    };
};
// 3.
// def a():
//     # local
//     a = 1
//     def b():
//         # implict local store
//         a = 2
$a = function() {
    $a = 1;
    $b = function() {
        $a = 2;
    };
};
// 4. lookup across multiple scopes with local inbetween
// def a():
//     a = 1
//     def b():
//         def c():
//             print(a)
$a = function() {
    $a = 1;
    $b = function() use (&$a) {
        $c = function() use (&$a) {
            echo $a;
        };
    };
};


// => use `use ($var)`
//  if:
//      1. function contains `nonlocal var`
//      2. only `Load()`s that variable
//      3. an inner function has 1. or 2.
//  else:
//      ~> means the function and all inner functions only use local `Store()`
