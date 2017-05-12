<?php

use PHPUnit\Framework\TestCase;

require_once implode(
    DIRECTORY_SEPARATOR,
    [__DIR__, '..', '..', 'vendor', 'phpunit', 'phpunit', 'src', 'Framework', 'Assert', 'Functions.php']
);

class OopTest extends TestCase {

    public function testInstanceMethods() {
        $a = new A();
        assertEquals($a->b(), 1);
        $b = new B();
        assertEquals($b->b(), 2);
    }

    public function testChangingInstanceMethodsOnClass() {
        $a1 = new A();
        $a2 = new A();
        $b1 = new B();
        $b2 = new B();

        assertEquals($a1->b(), 1);
        assertEquals($a2->b(), 1);
        assertEquals($b2->b(), 2);
        assertEquals($b2->b(), 2);
        // set function property (it is no method anymore)
        $a1->b = function() {
            return 3;
        };
        assertEquals($a1->b(), 3);
        assertEquals($a2->b(), 1);
        assertEquals($b1->b(), 2);
        assertEquals($b2->b(), 2);

        $b1->b = function($self) {
            return super(get_class($self), $self)->b() + 3;
        };
        assertEquals($a1->b(), 3);
        assertEquals($a2->b(), 1);
        assertEquals($b1->b($b1), 4);
        assertEquals($b2->b(), 2);
    }

    // public function testChangingInstanceMethodsOn2Class() {
    //     $a1 = new A();
    //     $a2 = new A();
    //     $b1 = new B();
    //     $b2 = new B();
    //
    //     // echo 'initial call', $a1->b(), "\n";
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
    //     assertEquals($b2->b(), 2);
    //     assertEquals($b2->b(), 2);
    //
    //     $b1->b = function($self) {
    //         return super('B', $self)->b() + 3;
    //     };
    //     assertEquals($b1->b($b1), 4);
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
