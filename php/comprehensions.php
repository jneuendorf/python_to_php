<?php

class __Comprehension {

    public function __construct($target, $iter, $ifs, $is_async, $child=null) {
        # code...
    }

    public function evaluate($callback) {

    }
}



function __comprehension($target, $iter, $ifs, $is_async) {
    return [$target, $iter, $ifs, $is_async];
}



function __list_comprehension($callback, $comprehensions) {
    $result = [];
    foreach ($comprehensions as $comprehension) {
        # code...
    }
}

// [True for item in array]
$array = [1, 2, 3];
var_dump(__list_comprehension(
    // pass targets of last comprehension
    function($item) {
        return True;
    },
    [
        [
            'item',
            $array,
            [],
            0
        ]
    ]
));
