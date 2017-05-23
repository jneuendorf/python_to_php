<?php

function getattr($object, $name, $default=null) {
    try {
        // if (is_metaclass($object)) {
        //     return getattr_metaclass($object, $name, $default);
        // }
        if (is_class($object)) {
            return getattr_class($object, $name, $default);
        }
        // it's a non-class object
        else {
            return getattr_object($object, $name, $default);
        }
    }
    catch (\Exception $e) {
        if (func_num_args() === 3) {
            return $default;
        }
        else {
            throw new AttributeError("'".str($object)."' has not attribute '$name'", 1);
        }
    }
}
