<?php

// All classes written in python inherit from this class
// It is not part of the static `__mro__` attribute.
abstract class __BaseClass {


    // class methods
    // public static function __new__($cls, ...$arguments) {
    //     $obj = new $cls();
    //     if (isinstance($obj, $cls)) {
    //         // call the constructor compiled from python
    //         $result = $obj->__init__(...$arguments);
    //         if ($result !== None) {
    //             throw new TypeError('__init__() should return None, not \''.type($result).'\'', 1);
    //         }
    //
    //     }
    // }

    // Whenever a class inherits from another class, __init_subclass__ is called on that class. This way, it is possible to write classes which change the behavior of subclasses. This is closely related to class decorators, but where class decorators only affect the specific class they’re applied to, __init_subclass__ solely applies to future subclasses of the class defining the method.
    // This method is called whenever the containing class is subclassed. cls is then the new subclass. If defined as a normal instance method, this method is implicitly converted to a class method.
    // Keyword arguments which are given to a new class are passed to the parent’s class __init_subclass__. For compatibility with other classes using __init_subclass__, one should take out the needed keyword arguments and pass the others over to the base class.
    // The default implementation object.__init_subclass__ does nothing, but raises an error if it is called with any arguments.
    static function __init_subclass__($cls) {
        if (func_num_args() > 0) {
            throw new Error('...', 1);
        }
    }

    public static function __call__(...$arguments) {
        return new static(...$arguments);
    }

    public static function __callStatic($name, $arguments) {
        // echo '__callStatic(): ', $name, ' on ', get_called_class(),  "\n";
        foreach (static::$__mro__ as $cls) {
            if (array_key_exists($cls.'_class_'.$name, $GLOBALS)) {
                // echo "> global ".$cls.'_class_'.$name." exists\n";
                return $GLOBALS[$cls.'_class_'.$name](get_called_class(), ...$arguments);
            }
        }
        throw new AttributeError('\''.get_called_class().'\' has no attribute \''.$name.'\'', 1);
    }

    public static function __setattr__($cls, $name, $value) {
        $GLOBALS[$cls.'_class_'.$name] = $value;
    }

    public static function __getattr__($cls, $name, $value) {
        if (array_key_exists($cls.'_class_'.$name, $GLOBALS)) {
            return $GLOBALS[$cls.'_class_'.$name];
        }
        else {
            throw new AttributeError("'$cls' has no attribute '$name'", 1);
        }
    }

    // instance methods
    public function __init() {
        $this->__properties = [];
    }

    public function __call($name, $arguments) {
        // echo '__call(): ', $name, ' on instance of ', get_class($this),  "\n";
        if (array_key_exists($name, $this->__properties)) {
            // echo 'using $this->__properties', "\n";
            // don't pass implicit `self` because this property is a function and not a method
            return $this->__properties[$name](...$arguments);
            // return $this->__properties[$name]($this, ...$arguments);
        }
        foreach (static::$__mro__ as $cls) {
            // not dynamically set for this instance => defined on the class => use global
            if (array_key_exists($cls."_".$name, $GLOBALS)) {
                // echo "> global ".$cls."_".$name." exists\n";
                return $GLOBALS[$cls."_".$name]($this, ...$arguments);
            }
        }
        throw new AttributeError('\''.get_class($this).'\' has no attribute \''.$name.'\'', 1);
    }

    public function __set($name, $value) {
        $this->__properties[$name] = $value;
    }
}

// class Object extends __BaseClass {
//
// }
// $Object = 'Object';
// $object = 'Object';

// This one's handling `super()` calls.
// TODO: RESTRICTION?:
// The attribute is dynamic and can change whenever the inheritance hierarchy is updated.
class Super {
    // the instance invoking super(); may be None
    public $__self__;
    // the type of the instance invoking super(); may be None
    public $__self_class__;
    // the class invoking super()
    public $__thisclass__;

    public $_called_static;
    public $_mro;

    public function __construct($type, $object_or_type) {
        $this->__thisclass__ = $type;
        $this->__self__ = $object_or_type;
        if (is_string($object_or_type)) {
            // "If the 2nd argument is a type, issubclass(type2, type) must be true (this is useful for classmethods)."
            if (!issubclass($object_or_type, $type)) {
                throw new Exception('Invalid arguments for super()', 1);
            }
            $__self_class__ = $object_or_type;
            $this->_called_static = True;
        }
        else {
            // "If the 2nd argument is an object, isinstance(obj, type) must be true."
            if (!isinstance($object_or_type, $type)) {
                throw new Exception('Invalid arguments for super()', 1);
            }
            $__self_class__ = get_class($object_or_type);
            $this->_called_static = False;
        }
        $this->__self_class__ = $__self_class__;

        $this->_mro = array_slice(
            $__self_class__::$__mro__,
            array_search($__self_class__, $__self_class__::$__mro__) + 1
        );
    }

    public function __call($name, $arguments) {
        if ($this->_called_static === True) {
            return $this->__call_static($name, $arguments);
        }
        foreach ($this->_mro as $cls) {
            // `isset($GLOBALS[$cls."_".$name])` is `True` => use instance method of `$cls`
            if (array_key_exists($cls."_".$name, $GLOBALS)) {
                // echo "> global ".$cls."_".$name." exists\n";
                return $GLOBALS[$cls."_".$name]($this->__self__, ...$arguments);
            }
            echo "using next class in mro...\n";
        }
        throw new AttributeError('\''.$this->__self_class__.'\' has no attribute \''.$name.'\'', 1);
    }

    public function __call_static($name, $arguments) {
        // echo '__call_static(): ', $name, ' on super of ', $this->__self_class__,  "\n";
        foreach ($this->_mro as $cls) {
            if (array_key_exists($cls.'_class_'.$name, $GLOBALS)) {
                // echo "> global ".$cls.'_class_'.$name." exists\n";
                return $GLOBALS[$cls.'_class_'.$name]($this->__self_class__, ...$arguments);
            }
        }
        throw new AttributeError('\'super\' has no attribute \''.$name.'\'', 1);
    }
}


function super($cls, $instance) {
    return new Super($cls, $instance);
}

// // All classes are instances of `type`.
// function is_class($cls) {
//     return isinstance($cls, 'Type');
// }
//
// function is_metaclass($cls) {
//     return $cls instanceof Type;
// }

function setattr($object, $name, $value) {
    // $object is a class
    if (is_string($object) && class_exists($object)) {
        $cls = $object;
        $cls::__setattr__($cls, $name, $value);
    }
    // objects handle their setting with their `__set` magic method
    else if (is_object($object)) {
        $object->$name = $value;
    }
}

// function getattr($object, $name, $default=null) {
//     try {
//         // $object is a class
//         if (is_string($object) && class_exists($object)) {
//             $cls = $object;
//             return $cls::__getattr__($cls, $name, $value);
//         }
//         // objects handle their setting with their `__get` magic method
//         else if (is_object($object)) {
//             return $object->$name;
//         }
//     }
//     catch (\Exception $e) {
//         if (func_num_args() === 3) {
//             return $default;
//         }
//         else {
//             throw new AttributeError("'".str($object)."' has not attribute '$name'", 1);
//         }
//     }
// }

// // TODO: tuple of type objects (or recursively, other such tuples)
// function issubclass($cls, $classinfo) {
//     // single class given => wrap it in an iterable
//     if (is_string($classinfo)) {
//         $classinfo = [$classinfo];
//     }
//     else if (!is_array($classinfo) && !isinstance($classinfo, 'Tuple')) {
//         throw new TypeError('issubclass() arg 2 must be a class or tuple of classes');
//     }
//
//     foreach ($classinfo as $superclass) {
//         if (__issubclass($cls, $superclass)) {
//             return True;
//         }
//     }
//     return False;
// }
//
// function isinstance($object, $classinfo) {
//     return issubclass(get_class($object), $classinfo);
// }
//
// function __issubclass($class, $superclass) {
//     foreach ($class::$__mro__ as $cls) {
//         if ($cls === $class) {
//             return True;
//         }
//     }
//     return False;
// }

// NOTE: must compile `callable(obj)` to `__callable($obj)` because 'callable' is a reserved word in PHP
function __callable($object) {
    // NOTE: no need to check for function-name strings because we compile functions as closure objects
    if (is_object($object) && $object instanceof \Closure) {
        return True;
    }
    // TODO: maybe check classes (if not made instances of meta class)
    try {
        $method = getattr($object, '__call__');
        return True;
    }
    catch (\Exception $e) {
        return False;
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
    public static $__mro__;
    // holds properties that were set dynamically
    public $__properties;

    public static function __init_static() {
        self::$__mro__ = ['A'];
    }

    public function __construct() {
        // 1.: register methods
        $this->__init();
        // 2.: normal body (compiled from python)
        // ...
    }
}
A::__init_static();
// create var for more consistent compilation of `Name`s:
// 1. `a = 1` -> `$a = 1`
// 2.
// `class A:
//      pass
// A.__dict__`
// ->
// `class A {}
// getattr($A, '__dict__')`
$A = 'A';



$B_b = function($self) {
    return super('B', $self)->b() + 1;
};
class B extends A {
    public static $__mro__;
    // holds properties that were set dynamically
    public $__properties;


    public function __construct() {
        $this->__init();
    }

    public static function __init_static() {
        self::$__mro__ = ['B', 'A'];
    }
}
B::__init_static();
$B = 'B';
