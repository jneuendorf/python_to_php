<?php

use PHPUnit\Framework\TestCase;

class TypeTest extends TestCase {

    public function testBuiltinClasses() {
        global $object;
        global $type;

        assertEquals(type($type), $type);
        assertEquals(type($object), $type);

        assertTrue(isinstance($type, $type));

        assertTrue(issubclass($type, $object));
        assertFalse(issubclass($object, $type));
    }

    public function testBasicClassCreation() {
        global $object;
        global $type;

        $A = type('A', [], dict());

        assertEquals($A->__name__, 'A');
        assertEquals($A->__bases__, [$object]);
        assertEquals($A->__mro__, [$A, $object]);
        assertEquals($A->__dict__, dict());
        assertEquals($A->__class__, $type);

        assertTrue($A->__is_class__);
        assertFalse($A->__is_metaclass__);
    }

    public function testAdvancedClassCreation() {
        global $object;
        global $type;
        $A = type('A', [], dict());
        $B = type('B', [], dict());
        $C = type('C', [$A, $B], dict());

        assertEquals($C->__bases__, [$A, $B]);
        assertEquals($C->__mro__, [$C, $A, $B, $object]);
    }

    // public function testClassMetaClassRelations() {
    //     global $object;
    //     global $type;
    //
    //     // this is made sure in previous tests
    //     $A = type('A', [], dict());
    //     assertEquals(type($A), $type);
    //
    //     // TODO: move the following assertions to OopTest
    //     assertTrue(isinstance($A, $type));
    //
    //     assertFalse(issubclass($A, $type));
    //     assertTrue(issubclass($A, $object));
    //
    //
    //     // class A(metaclass=type): pass
    //     // isinstance(A, type) -> True
    //     // a = A()
    //     // isinstance(a, type) -> False
    // }

}
