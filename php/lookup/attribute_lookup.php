<?php

function getattr_object($object, $name, $default) {
    $cls = type($object);
    return $cls->__getattribute__($name);
}
