<?php

class Super {

    // function __construct() {
    //     call_user_func_array(
    //         get_class().'::__init__',
    //         array_merge(
    //             array($this),
    //             func_get_args()
    //         )
    //     );
    // }
    function __construct($a, $b) {
        $this->__init__($this, $a, $b);
    }

    function __init__($self, $a, $b) {
        $self->a = $a;
        $self->b = $b;
        var_dump($self);
    }
}


class Sub extends Super {
    function get_a() {
        return $this->__get_a($this);
    }

    function __get_a($self) {
        return $self->a;
    }
}






$sup = new Super(1, 2);
$sub = new Sub(3, 4);
var_dump($sub->get_a());
