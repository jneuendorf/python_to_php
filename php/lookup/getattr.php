<?php

// object.__getattribute__(self, name)
// Called unconditionally to implement attribute accesses for instances of the class. If the class also defines __getattr__(), the latter will not be called unless __getattribute__() either calls it explicitly or raises an AttributeError. This method should return the (computed) attribute value or raise an AttributeError exception. In order to avoid infinite recursion in this method, its implementation should always call the base class method with the same name to access any attributes it needs, for example, object.__getattribute__(self, name).
//
// Note This method may still be bypassed when looking up special methods as the result of implicit invocation via language syntax or built-in functions. See Special method lookup.
function getattr($object, $name, $default=null) {
    try {
        // NOTE: must not use `__call_func` here because that would call `getattr` (-> infinite loop)
        return call_user_func($object->__class__->__getattribute__, $object->__class__, ...$args);
        // if (is_metaclass($object)) {
        //     return getattr_metaclass($object, $name, $default);
        // }
        // if (is_class($object)) {
        //     return getattr_class($object, $name, $default);
        // }
        // // it's a non-class object
        // else {
        //     return getattr_object($object, $name, $default);
        // }
    }
    catch (AttributeError $e) {
        if (func_num_args() === 3) {
            return $default;
        }
        else {
            // throw new AttributeError("'".str($object)."' has no attribute '$name'", 1);
            throw $e;
        }
    }
    // throw all other errors (as they indicate an error in the lookup itself)
}
