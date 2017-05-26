<?php

$type = new stdClass();
$type->__class__ = $type;
$type->__name__ = 'type';
$type->__is_class__ = True;
$type->__is_metaclass__ = True;

$object = new stdClass();
$object->__mro__ = [$object];
$object->__class__ = $type;
$object->__name__ = 'object';
$object->__bases__ = [];
$object->__is_class__ = True;
$object->__is_metaclass__ = False;
$object->__toString = function($self) {
    return $self->__str__($self);
};

$type->__mro__ = [$type, $object];
$type->__bases__ = [$object];

function __create_instance($cls) {
    $obj = new stdClass();
    $obj->__class__ = $cls;
    $obj->__is_class__ = False;
    $obj->__is_metaclass__ = False;
    return $obj;
}

// class type(name, bases, dict) or any metaclass
function __create_class($name, $bases, $dict, $metaclass) {
    global $object;

    if (count($bases) === 0) {
        $bases = [$object];
    }

    $cls = __create_instance($metaclass);
    $cls->__name__ = $name;
    // auto insert object if no bases given
    $cls->__bases__ = $bases;
    $cls->__dict__ = $dict;
    $cls->__mro__ = array_merge(
        [$cls],
        c3_linearization(array_map(function($base) {
            return $base->__mro__;
        }, $bases))
    );
    $cls->__is_class__ = True;

    $cls->__call__ = function($self, ...$args) {
        return __call_func($self, '__new__', ...$args);
    };
    $cls->__new__ = function($self, ...$arguments) {
        $obj = __create_instance($self);
        if (isinstance($obj, $self)) {
            // call the constructor compiled from python
            $result = $obj->__init__(...$arguments);
            if ($result !== None) {
                throw new \TypeError('__init__() should return None, not \''.type($result).'\'', 1);
            }
        }
        return $obj;
    };
    $cls->__getattribute__ = function($self, $name) {
        $metaclass = $self->__class__;

        if ($metaclass::$__dict__->has($name)) {
            $attribute = $metaclass::$__dict__->get($name);
            // Does Metaclass.__dict__ have a foobar item that is a data descriptor?
            if ($attribute->__get__ and $attribute->__set__) {
                return $attribute->__get__($self, $metaclass);
            }
        }

        if ($self->__dict__->has($name)) {
            $attribute = $self->__dict__->get($name);
            // Does Class.__dict__ have a foobar item that is a descriptor (of any kind)?
            if ($attribute->__get__) {
                return $attribute->__get__(None, $self);
            }
            else {
                return $attribute;
            }
        }

        // Does Metaclass.__dict__ have a foobar item that is not a data descriptor?
        if ($metaclass::$__dict__->has($name)) {
            $attribute = $metaclass::$__dict__->get($name);
            if ($attribute->__get__) {
                return $attribute->__get__($self, $metaclass);
            }
            else {
                return $attribute;
            }
        }
        else {
            return $metaclass::__getattr__($name);
        }
    };
    return $cls;
}

/*
static PyObject* type_call(PyTypeObject *type, PyObject *args, PyObject *kwds) {
    PyObject *obj;

    if (type->tp_new == NULL) {
        PyErr_Format(PyExc_TypeError,
                     "cannot create '%.100s' instances",
                     type->tp_name);
        return NULL;
    }

#ifdef Py_DEBUG
    // type_call() must not be called with an exception set,
    // because it can clear it (directly or indirectly) and so the
    // caller loses its exception
    assert(!PyErr_Occurred());
#endif

    obj = type->tp_new(type, args, kwds);
    obj = _Py_CheckFunctionResult((PyObject*)type, obj, NULL);
    if (obj == NULL)
        return NULL;

    // Ugly exception: when the call was type(something),
    // don't call tp_init on the result.
    if (type == &PyType_Type &&
        PyTuple_Check(args) && PyTuple_GET_SIZE(args) == 1 &&
        (kwds == NULL ||
         (PyDict_Check(kwds) && PyDict_GET_SIZE(kwds) == 0)))
        return obj;

    // If the returned object is not an instance of type,
    // it won't be initialized.
    if (!PyType_IsSubtype(Py_TYPE(obj), type))
        return obj;

    type = Py_TYPE(obj);
    if (type->tp_init != NULL) {
        int res = type->tp_init(obj, args, kwds);
        if (res < 0) {
            assert(PyErr_Occurred());
            Py_DECREF(obj);
            obj = NULL;
        }
        else {
            assert(!PyErr_Occurred());
        }
    }
    return obj;
}
*/

// constructor
$type->__call__ = function($self, ...$args) {
    // var_dump($self);
    // var_dump($args);

    // class type(object)
    $n = count($args);
    if ($n === 1) {
        $object = $args[0];
        return $object->__class__;
    }
    // class type(name, bases, dict)
    else if ($n === 3) {
        return __create_class($args[0], $args[1], $args[2], $self);
    }
    else {
        throw new TypeError('type() takes 1 or 3 arguments', 1);
    }
};


// var_dump($type);


// class object {
//     public static $__mro__ = ['object'];
//     // public static $__class__ = 'type';
// }
// // needs to be done dynamically because php would raise an error that `type` would redeclare static `__class__` as non-static.
// object::$__class__ = 'type';
// object::$__name__ = 'object';
// object::$__bases__ = [];

// object.__new__(cls[, ...])
// Called to create a new instance of class cls. __new__() is a static method (special-cased so you need not declare it as such) that takes the class of which an instance was requested as its first argument. The remaining arguments are those passed to the object constructor expression (the call to the class). The return value of __new__() should be the new object instance (usually an instance of cls).
//
// Typical implementations create a new instance of the class by invoking the superclass’s __new__() method using super(currentclass, cls).__new__(cls[, ...]) with appropriate arguments and then modifying the newly-created instance as necessary before returning it.
//
// If __new__() returns an instance of cls, then the new instance’s __init__() method will be invoked like __init__(self[, ...]), where self is the new instance and the remaining arguments are the same as were passed to __new__().
//
// If __new__() does not return an instance of cls, then the new instance’s __init__() method will not be invoked.
//
// __new__() is intended mainly to allow subclasses of immutable types (like int, str, or tuple) to customize instance creation. It is also commonly overridden in custom metaclasses in order to customize class creation.
//
// object.__init__(self[, ...])
// Called after the instance has been created (by __new__()), but before it is returned to the caller. The arguments are those passed to the class constructor expression. If a base class has an __init__() method, the derived class’s __init__() method, if any, must explicitly call it to ensure proper initialization of the base class part of the instance; for example: BaseClass.__init__(self, [args...]).
//
// Because __new__() and __init__() work together in constructing objects (__new__() to create it, and __init__() to customize it), no non-None value may be returned by __init__(); doing so will cause a TypeError to be raised at runtime.



// When a class definition is executed, the following steps occur:
// - the appropriate metaclass is determined
// - the class namespace is prepared
// - the class body is executed
// - the class object is created

// NOTE: Changed in version 3.6: Subclasses of type which don’t override type.__new__ may no longer use the one-argument form to get the type of an object.
class type /*extends object*/ {
    public static $__mro__ = ['type', 'object'];
    public $__name__;
    public $__bases__;
    public $__class__;
    // public $__mro__;

    function __construct($name, $bases, $dict) {
        $this->__name__ = $name;
        $this->__bases__ = $bases;
        $this->__class__ = get_called_class();
    }

    static function __repr__() {
        return '<class \'type\'>';
    }

    static function __str__() {
        return '<class \'type\'>';
    }

    // static function __getattribute__($self, $name) {
    //     if ($self::$__dict__->has($name)) {
    //         $attribute = $self::$__dict__->get($name);
    //         if ($attribute->__get__ and $attribute->__set__) {
    //             return $attribute->__get__(, );
    //         }
    //     }
    // }

    // INSTANCE METHODS (== CLASS METHODS OF CLASSES)
    function __new__($metaclass, $name, $bases, $kwargs=[]) {
        $cls = new $metaclass();
        $cls->__class = $metaclass;
        if (isinstance($cls, $metaclass)) {
            // call the constructor compiled from python
            $result = $cls->__init__(...$arguments);
            if ($result !== None) {
                throw new \TypeError('__init__() should return None, not \''.type($result).'\'', 1);
            }
        }
        return $cls;
    }

    // `$self` is the class object.
    // E.g. `Class.foobar`.
    function __getattribute__($self, $name) {
        $metaclass = get_class($self);

        if ($metaclass::$__dict__->has($name)) {
            $attribute = $metaclass::$__dict__->get($name);
            // Does Metaclass.__dict__ have a foobar item that is a data descriptor?
            if ($attribute->__get__ and $attribute->__set__) {
                return $attribute->__get__($self, $metaclass);
            }
        }

        if ($self->__dict__->has($name)) {
            $attribute = $self->__dict__->get($name);
            // Does Class.__dict__ have a foobar item that is a descriptor (of any kind)?
            if ($attribute->__get__) {
                return $attribute->__get__(None, $self);
            }
            else {
                return $attribute;
            }
        }

        // Does Metaclass.__dict__ have a foobar item that is not a data descriptor?
        if ($metaclass::$__dict__->has($name)) {
            $attribute = $metaclass::$__dict__->get($name);
            if ($attribute->__get__) {
                return $attribute->__get__($self, $metaclass);
            }
            else {
                return $attribute;
            }
        }
        else {
            return $metaclass::__getattr__($name);
        }
    }

    // enable construction of class'es instances:
    // class A:
    //      pass
    // a = A()
    function __call__(...$arguments) {
        $cls = $this;
        return $cls->__new__(...$arguments);
    }

    // NOTE: instance methods here will be classmethods on the class

    // function __toString() {
    //     return 'TO BE DONE';
    // }
}

// function type(...$args) {
//     // class type(object)
//     if (func_num_args() === 1) {
//         $object = $args[0];
//         if (is_object($object)) {
//             return $object->__class__;
//         }
//         // else: meta class -> php class -> string
//         $metaclass = $object;
//         var_dump($metaclass);
//         debug_print_backtrace();
//         $mro = $metaclass::$__mro__;
//         // count can't be 0
//         if (count($mro) === 1) {
//             // == $mro[0]
//             return $mro[0];
//         }
//         return $mro[1];
//     }
//     // class type(name, bases, dict)
//     else if (func_num_args() === 3) {
//         return new type($args[0], $args[1], $args[2]);
//     }
//     else {
//         throw new \TypeError('type() takes 1 or 3 arguments', 1);
//     }
// }


/*
// creating a class:

$A = type('A', [], []);
*/
