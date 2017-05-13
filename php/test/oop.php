<?php

if (!function_exists('exception_error_handler')) {
    function exception_error_handler($errno, $errstr, $errfile, $errline) {
        throw new \ErrorException($errstr, $errno, 0, $errfile, $errline);
    }
    set_error_handler("exception_error_handler");
}


class AttributeError extends \Exception {}

class __Class {}

// All classes written in python inherit from this class
// It is not part of the static `__mro__` attribute.
class __BaseClass {
    public function __call($name, $arguments) {
        echo '__call(): ', $name, ' on instance of ', get_class($this),  "\n";
        if (array_key_exists($name, $this->__properties)) {
            echo 'using $this->__properties', "\n";
            // don't pass implicit `self` because this property is a function and not a method
            return $this->__properties[$name](...$arguments);
            // return $this->__properties[$name]($this, ...$arguments);
        }
        // var_dump($this);
        // var_dump(get_called_class());
        // var_dump(static::$__mro__);
        foreach (static::$__mro__ as $cls) {
            // dynamically set (on this instance only)
            // echo '>>> ', $name, ' ', $cls, "\n";
            // var_dump($cls);
            // echo "\n";
            // not dynamically set for this instance => defined on the class => use global
            if (array_key_exists($cls."_".$name, $GLOBALS)) {
                echo "> global ".$cls."_".$name." exists\n";
                return $GLOBALS[$cls."_".$name]($this, ...$arguments);
            }
        }
        throw new \AttributeError('\''.get_class($this).'\' has no attribute \''.$name.'\'', 1);
    }

    public static function __callStatic($name, $arguments) {
        // $func = static::$__class_properties[$name];
        // return $func(get_class(), ...$arguments);
        echo '__callStatic(): ', $name, ' on ', get_called_class(),  "\n";
        if (array_key_exists($name, static::$__class_properties)) {
            echo 'using static::$__class_properties', "\n";
            $func = static::$__class_properties[$name];
            // don't pass implicit `self` because this property is a function and not a method
            return $func(...$arguments);
        }
        foreach (static::$__mro__ as $cls) {
            // dynamically set (on this instance only)
            // echo '>>> ', $name, ' ', $cls, "\n";
            // var_dump($cls);
            // echo "\n";
            // not dynamically set => statically defined => use global
            if (array_key_exists($cls."_class_".$name, $GLOBALS)) {
                echo "> global ".$cls."_class_".$name." exists\n";
                return $GLOBALS[$cls."_class_".$name](get_called_class(), ...$arguments);
            }
        }
        throw new \AttributeError('\''.get_called_class().'\' has no attribute \''.$name.'\'', 1);
    }

    public function __set($name, $value) {
        $this->__properties[$name] = $value;
    }

    public function __setStatic($name, $value) {
        $this->__class_properties[$name] = $value;
    }
}

// This one's handling `super()` calls.
class Super {
    // the instance invoking super(); may be None
    public $__self__;
    // the type of the instance invoking super(); may be None
    public $__self_class__;
    // the class invoking super()
    public $__thisclass__;

    public $_mro;

    public function __construct($cls, $instance) {
        $this->__thisclass__ = $cls;
        $this->__self__ = $instance;
        $__self_class__ = get_class($instance);
        $this->__self_class__ = $__self_class__;

        $this->_mro = array_slice(
            $__self_class__::$__mro__,
            array_search($__self_class__, $__self_class__::$__mro__) + 1
        );
        // echo "constructing new Super():\n";
        // var_dump($this);
    }

    public function __call($name, $arguments) {
        // echo 'super() >>> ', $name, '  ';
        // var_dump($this->__self__);
        foreach ($this->_mro as $cls) {
            // var_dump($cls);
            // echo "\n";
            // `isset($GLOBALS[$cls."_".$name])` is `True` => use instance method of `$cls`
            if (array_key_exists($cls."_".$name, $GLOBALS)) {
                // echo "> global ".$cls."_".$name." exists\n";
                return $GLOBALS[$cls."_".$name]($this->__self__, ...$arguments);
            }
            echo "using next class in mro...\n";
        }
        throw new \AttributeError('\''.$this->__self_class__.'\' has no attribute \''.$name.'\'', 1);
    }

    public static function __callStatic($name, $arguments) {
        $func = static::$__class_properties[$name];
        return $func(get_class(), ...$arguments);
    }
}


function set_class_property($cls, $name, $value) {
    return $cls::set_class_property($name, $value);
}

function super($cls, $instance) {
    return new Super($cls, $instance);
}

function set_attr($object, $name, $value) {
    // $object is a class
    if (is_string($object) && class_exists($object)) {
        $cls = $object;
        // instance method -> affect all instances
        // if (is_callable($value) && array_key_exists($name, $cls::$__properties)) {
        if (is_callable($value) && array_key_exists($lcls.'_'.$name, $GLOBALS)) {
            $GLOBALS[$lcls.'_'.$name] = $value;
        }
    }
}


// instance method compiled from `class A: def b(): pass`
// Changing this variable means changing the method on the class:
// class A:
//     def b():
//         return 1
// A.b = lambda: 2
$A_b = function($self) {
    return 1;
};

$A_class_classmethod = function($self) {
    return $self;
};

class A extends __BaseClass {
    // holds class properties that were set dynamically
    public static $__class_properties;
    public static $__mro__;
    // holds properties that were set dynamically
    public $__properties;

    public static function __init_static() {
        self::$__mro__ = ['A'];
        self::$__class_properties = [
            // 'classmethod' => function($self) {
            //     return $self;
            // },
        ];
    }

    public function __init() {
        $this->__properties = [];
    }

    // public static function set_class_property($name, $value) {
    //     static::$__class_properties[$name] = $value;
    // }

    public function __construct() {
        // 1.: register methods
        $this->__init();
        // 2.: normal body (compiled from python)
        // ...
    }
}
A::__init_static();



$B_b = function($self) {
    return super('B', $self)->b() + 1;
};
class B extends A {
    public static $__class_properties;
    public static $__mro__;
    // holds properties that were set dynamically
    public $__properties;


    public function __construct() {
        $this->__init();
    }

    public static function __init_static() {
        self::$__mro__ = ['B', 'A'];
        self::$__class_properties = [];
    }

    public function __init() {
        $this->__properties = [];
    }
}
B::__init_static();


// echo A::classmethod(), "\n";
// // A::set_class_property('classmethod', function($self) {
// //     return "class '$self'";
// // });
// // set_class_property('A', 'classmethod', function($self) {
// //     return "class '$self'";
// // });
// A::$__class_properties['classmethod'] = function($self) {
//     return "class '$self'";
// };
// echo A::classmethod(), "\n";
