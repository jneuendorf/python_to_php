<?php

// TODO: tuple of type objects (or recursively, other such tuples)
function issubclass($cls, $classinfo) {
    if (!is_meta_or_class($cls)) {
        return False;
    }
    // single class given => wrap it in an iterable
    if (is_meta_or_class($classinfo)) {
        $classinfo = [$classinfo];
    }
    else if (!is_array($classinfo) && !isinstance($classinfo, 'tuple')) {
        throw new TypeError('issubclass() arg 2 must be a class or tuple of classes');
    }

    foreach ($classinfo as $superclass) {
        if (is_meta_or_class($superclass)) {
            if (__issubclass($cls, $superclass)) {
                return True;
            }
        }
    }
    return False;
}

function isinstance($object, $classinfo) {
    return issubclass(type($object), $classinfo);
}

// @param $class [class|metaclass] Either a class object or a meta class (string).
function __issubclass($class, $superclass) {
// function __issubclass($a, $b) {
    // do {
    //     if ($a === $b) {
    //         return True;
    //     }
    //     $a = $a->;
    // } while ($a !== null);

    // // All classes (meta or not) are subclasses.
    // if ($superclass === 'object') {
    //     return True;
    // }
    // meta class
    if (is_metaclass($class)) {
        if (in_array($superclass, $class::$__mro__, True)) {
            return True;
        }
    }
    // normal class / instance of metaclass
    else {
        if (in_array($superclass, $class->__mro__, True)) {
            return True;
        }
    }
    return $superclass === 'object';
}

// All classes are instances of `type`.
function is_class($cls) {
    // prevent infinite loops in `isinstance` because `type(type) == type`
    // if ($cls === 'type') {
    //     return True;
    // }
    global $type;
    return isinstance($cls, $type);
}

function is_metaclass($cls) {
    return is_string($cls) && class_exists($cls);
}

function is_meta_or_class($cls) {
    return is_class($cls) or is_metaclass($cls);
}
