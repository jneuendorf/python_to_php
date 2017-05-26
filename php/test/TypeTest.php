<?php

use PHPUnit\Framework\TestCase;

class TypeTest extends TestCase {

    public function testBuiltinClasses() {
        global $object;
        global $type;

        assertEquals(type($type), $type);
        assertEquals(type($object), $type);
        // special case
        assertTrue(isinstance($type, $type));

        assertTrue(issubclass($type, $object));
        assertFalse(issubclass($object, $type));
    }

    public function testMetaclassCreation() {
        global $type;

        $meta = type('Meta', [$type], dict());
        assertTrue(issubclass($meta, $type));
    }

    public function testBasicClassCreation() {
        global $object;
        global $type;

        $dict = dict(null, [1 => 2, '3' => False]);
        $A = type('A', [], $dict);

        assertEquals($A->__name__, 'A');
        assertEquals($A->__bases__, [$object]);
        assertEquals($A->__mro__, [$A, $object]);
        assertEquals($A->__dict__, $dict);
        assertEquals($A->__class__, $type);

        assertTrue($A->__is_class__);
        assertFalse($A->__is_metaclass__);

        assertTrue(isinstance($A, $type));
        assertFalse(issubclass($A, $type));
        assertTrue(issubclass($A, $object));
    }

    public function testAdvancedClassCreation() {
        global $object;
        global $type;
        $A = type('A', [], dict());
        $B = type('B', [], dict());
        $C = type('C', [$A, $B], dict());

        assertEquals($C->__bases__, [$A, $B]);
        assertEquals($C->__mro__, [$C, $A, $B, $object]);
        assertEquals($C->__class__, $type);
    }

    public function testBasicInstanceCreation() {
        global $object;
        global $type;
        $A = type('A', [], dict());
        $a = __new($A);

        assertTrue(isinstance($a, $A));
        assertTrue(isinstance($a, $object));
        assertFalse(isinstance($a, $type));
        assertEquals($a->__class__, $A);

        assertFalse(is_class($a));
        assertFalse(is_metaclass($a));
    }
}
