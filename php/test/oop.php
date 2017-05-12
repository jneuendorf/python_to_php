<?php

// function exception_error_handler($errno, $errstr, $errfile, $errline) {
//     throw new \ErrorException($errstr, $errno, 0, $errfile, $errline);
// }
// set_error_handler("exception_error_handler");



class AttributeError extends \Exception {}

class __Class {

}

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
            // dynamically set (on this instance only)
            // var_dump($cls);
            // echo "\n";
            // if `$this->__self__->__properties` doesn't exist it means we're in the `__init` method
            // if (array_key_exists($name, $this->__self__->__properties)) {
            //     // // don't pass implicit `self` because this property is a function and not a method
            //     // echo 'args = ';
            //     // var_dump($arguments);
            //     // return $this->__self__->__properties[$name](...$arguments);
            //     return $this->__self__->__properties[$name]($this->__self__, ...$arguments);
            // }
            // else
            // not dynamically set => defined as method => use global
            if (array_key_exists($cls."_".$name, $GLOBALS)) {
                // echo "> global ".$cls."_".$name." exists\n";
                return $GLOBALS[$cls."_".$name]($this->__self__, ...$arguments);
            }
            // else if (method_exists($this->__self__, $name)) {
            //     echo "> method exists\n";
            //     return $this->__self__->$name($this->__self__, ...$arguments);
            // }
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

class A {
    // // array keys of these 2 arrays are disjoint
    // holds class properties that were set dynamically
    public static $__class_properties;
    public static $__mro__;
    // holds properties that were set dynamically
    public $__properties;

    public static function __init_static() {
        self::$__mro__ = ['A'];
        // static::$__class_properties_name = '__'.static::$__prefix.'_class_properties';
        // $__class_properties_name = static::$__class_properties_name;
        // TODO: Use this method to init classmethods etc.
        self::$__class_properties = [
            'classmethod' => function($self) {
                return $self;
            },
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

    public function __call($name, $arguments) {
        echo '__call()', $name, ' on instance of ', get_class($this),  "\n";
        // var_dump($this);
        // var_dump(get_called_class());
        // var_dump(static::$__mro__);
        foreach (static::$__mro__ as $cls) {
            // dynamically set (on this instance only)
            // echo '>>> ', $name, ' ', $cls, "\n";
            // var_dump($cls);
            // echo "\n";
            if (array_key_exists($name, $this->__properties)) {
                echo 'using $this->__properties', "\n";
                // don't pass implicit `self` because this property is a function and not a method
                return $this->__properties[$name](...$arguments);
                // return $this->__properties[$name]($this, ...$arguments);
            }
            // not dynamically set => statically defined => use global
            else if (array_key_exists($cls."_".$name, $GLOBALS)) {
                echo "> global ".$cls."_".$name." exists\n";
                return $GLOBALS[$cls."_".$name]($this, ...$arguments);
            }
        }
        throw new \AttributeError('\''.get_class($this).'\' has no attribute \''.$name.'\'', 1);
    }

    public static function __callStatic($name, $arguments) {
        $func = static::$__class_properties[$name];
        return $func(get_class(), ...$arguments);
    }

    public function __set($name, $value) {
        $this->__properties[$name] = $value;
    }

    public function __setStatic($name, $value) {
        $this->__class_properties[$name] = $value;
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


    // public function __construct() {
    //     $this->__init();
    // }

    public static function __init_static() {
        // self::$__B_mro = ['B', 'A'];
        self::$__mro__ = ['B', 'A'];
    }

    // public function __init() {
    //     // super(__CLASS__, $this)->__init();
    //     $this->__properties = [];
    //     // $this->__properties = array_merge(
    //     //     $this->__properties,
    //     //     [
    //     //         'b' => function($self) {
    //     //             var_dump(get_parent_class());
    //     //             // return $this->__call_super('b') + 1;
    //     //             return super(__CLASS__, $this)->b() + 1;
    //     //             // return parent::b() + 1;
    //     //         },
    //     //     ]
    //     // );
    // }
}

B::__init_static();


// echo "===> A\n";
//
// $a1 = new A();
// $a2 = new A();
// echo 'initial call', $a1->b(), "\n";
// // set function property (it is no method anymore)
// $a1->b = function() {
//     return 3;
// };
// echo 'after function property was set ', $a1->b(), "\n\n";
// echo 'a2.b() ', $a2->b(), "\n\n";
//
// echo "===> B\n";
//
// $b = new B();
// $result = $b->b();
// echo 'result = ', $result, "\n\n";


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
