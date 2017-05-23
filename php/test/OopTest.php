<?php

use PHPUnit\Framework\TestCase;

require_once implode(
    DIRECTORY_SEPARATOR,
    [__DIR__, '..', '..', 'vendor', 'phpunit', 'phpunit', 'src', 'Framework', 'Assert', 'Functions.php']
);

class OopTest extends TestCase {

    // public function testInstantiatingObjects() {
    //     $a = new A();
    //     $a2 = A::__call__();
    //     $b = new B();
    //
    //     assertTrue(isinstance($a, 'A'));
    //     assertTrue(isinstance($a2, 'A'));
    //     assertTrue(isinstance($b, 'b'));
    // }
    //
    // public function testInstanceMethods() {
    //     $a = new A();
    //     assertEquals($a->b(), 1);
    //     $b = new B();
    //     assertEquals($b->b(), 2);
    // }
    //
    // public function testChangingInstanceMethodsOnClass() {
    //     $a1 = new A();
    //     $a2 = new A();
    //     $b1 = new B();
    //     $b2 = new B();
    //
    //     assertEquals($a1->b(), 1);
    //     assertEquals($a2->b(), 1);
    //     assertEquals($b2->b(), 2);
    //     assertEquals($b2->b(), 2);
    //     // set function property (it is no method anymore)
    //     $a1->b = function() {
    //         return 3;
    //     };
    //     assertEquals($a1->b(), 3);
    //     assertEquals($a2->b(), 1);
    //     assertEquals($b1->b(), 2);
    //     assertEquals($b2->b(), 2);
    //
    //     // in order to have `$self` the according parameter must be passed
    //     $b1->b = function($self) {
    //         return super(get_class($self), $self)->b() + 3;
    //     };
    //     assertEquals($a1->b(), 3);
    //     assertEquals($a2->b(), 1);
    //     assertEquals($b1->b($b1), 4);
    //     assertEquals($b2->b(), 2);
    // }
    //
    // public function testClassMethods() {
    //     assertEquals(A::classmethod(), 'A');
    //     assertEquals(B::classmethod(), 'B');
    // }
    //
    // public function testChangingClassMethods() {
    //     global $A, $B;
    //     assertEquals(B::classmethod(), 'B');
    //     setattr($A, 'classmethod', function($self) {
    //         return $self.' new';
    //     });
    //     // different value because `B extends A`
    //     assertEquals(B::classmethod(), 'B new');
    //     setattr($B, 'classmethod', function($self) {
    //         return super('B', $self)->classmethod().' with super';
    //     });
    //     assertEquals(B::classmethod(), 'B new with super');
    // }
}


// echo A::classmethod(), "\n";
// // A::set_class_property('classmethod', function($self) {
// //     return "class '$self'";
// // });
// // set_class_property('A', 'classmethod', function($self) {
// //     return "class '$self'";
// // });
// A::$__class_properties['classmethod'] = function($self) {
//     return "class '$self'";
// };
// echo A::classmethod(), "\n";
