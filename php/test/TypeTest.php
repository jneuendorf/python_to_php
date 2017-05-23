<?php

use PHPUnit\Framework\TestCase;

class TypeTest extends TestCase {

    public function testBasicClassCreation() {
        global $type;
        // var_dump($type->__call__);
        // $f = $type->__call__;
        // $A = $f('A', [], dict());
        // $A = call_user_func($type->__call__, $type, 'A', [], dict());
        $A = __call_func($type, '__call__', 'A', [], dict());
        // $A = $type->__call__('A', [], dict());

        // assertEquals($A->__class__, 'Type');
        // var_dump($f($A));
        // assertEquals($f($A), $type);
        assertEquals(call_user_func($type->__call__, $type, $A), $type);
        // assertEquals(type($A), $type);
        assertEquals($A->__name__, 'A');
        assertEquals($A->__bases__, []);

        assertTrue(isinstance($A, $type));

        /*
        #0  type(type) called at [python_to_php/php/oop/object_relations.php:31]
        #1  isinstance(type, type) called at [python_to_php/php/oop/object_relations.php:53]
        #2  is_class(type) called at [python_to_php/php/oop/object_relations.php:61]
        #3  is_meta_or_class(type) called at [python_to_php/php/oop/object_relations.php:5]
        #4  issubclass(type, type) called at [python_to_php/php/oop/object_relations.php:31]

        #5  isinstance(type, type) called at [python_to_php/php/oop/object_relations.php:53]
        #6  is_class(type) called at [python_to_php/php/oop/object_relations.php:61]
        #7  is_meta_or_class(type) called at [python_to_php/php/oop/object_relations.php:5]
        #8  issubclass(type, type) called at [python_to_php/php/oop/object_relations.php:31]

        #9  isinstance(type Object ([__name__] => A,[__bases__] => Array (),[__class__] => type), type) called at [python_to_php/php/test/TypeTest.php:15]
        #10 TypeTest->testBasicClassCreation()
        */

        // class A(metaclass=type): pass
        // isinstance(A, type) -> True
        // a = A()
        // isinstance(a, type) -> False
    }


}
