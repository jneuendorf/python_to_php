<?php

// TODO: tuple of type objects (or recursively, other such tuples)
function issubclass($cls, $classinfo) {
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
    if (in_array($superclass, $class->__mro__, True)) {
        return True;
    }
    global $object;
    return $superclass === $object;
}

// All classes are instances of `type`.
function is_class($cls) {
    return $cls->__is_class__;
}

function is_metaclass($cls) {
    return $cls->__is_metaclass__;
}

function is_meta_or_class($cls) {
    return is_class($cls) or is_metaclass($cls);
}
