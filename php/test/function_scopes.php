<?php

$__SCOPES = [];
// Saves parent id for each scope id.
$__SCOPE_PARENTS = [];
// $__NONLOCALS = [];

// NOTE: static variables??
// NOTE: references (`&`)??

function __register_scope($scope_id, $parent_scope_id) {
    global $__SCOPES;
    global $__SCOPE_PARENTS;
    // global $__NONLOCALS;
    $__SCOPES[$scope_id] = [];
    $__SCOPE_PARENTS[$scope_id] = $parent_scope_id;
    // $__NONLOCALS[$scope_id] = [];
}

// function __set_nonlocal($scope_id, $varname) {
//     global $__NONLOCALS;
//     $__NONLOCALS[$scope_id][$varname] = True;
// }

// returns a reference to the variable with the given name in the next outer scope where it is defined
function &__lookup_nonlocal($scope_id, $varname) {
    global $__SCOPES;
    global $__SCOPE_PARENTS;
    // NOTE: This should not happen if python has no errors.
    // if (!array_key_exists($__SCOPE_PARENTS[$scope_id], $__SCOPES)) {
    //     throw new Exception('Used nonlocal lookup in global scope.', 1);
    // }
    // echo "nonlocal lookup: ($scope_id, $varname)\n";
    $parent_scope_id = $__SCOPE_PARENTS[$scope_id];
    // echo "parent id = $parent_scope_id\n";
    $parent_scope = &$__SCOPES[$parent_scope_id];
    while ($parent_scope !== null && !array_key_exists($varname, $parent_scope)) {
        $parent_scope_id = $__SCOPE_PARENTS[$parent_scope_id];
        // echo "parent id = $parent_scope_id\n";
        $parent_scope = &$__SCOPES[$parent_scope_id];
    }

    // NOTE: This should not happen if python has no errors.
    // if (!array_key_exists($varname, $parent_scope)) {
    //     throw new Exception('Error during nonlocal lookup.', 1);
    // }
    return $parent_scope[$varname];
}

function __set_var($scope_id, $varname, $value, $nonlocal_lookup=False) {
    global $__SCOPES;
    if (!$nonlocal_lookup) {
        $scope = &$__SCOPES[$scope_id];
        $scope[$varname] = $value;
    }
    else {
        $var = &__lookup_nonlocal($scope_id, $varname);
        $var = $value;
    }
    // echo "__set_var $scope_id $varname\n";
    return $value;
}

function __set_var_nonlocal($scope_id, $varname, $value) {
    return __set_var($scope_id, $varname, $value, True);
}

function &__get_var($scope_id, $varname) {
    global $__SCOPES;
    // defined in local scope
    if (array_key_exists($varname, $__SCOPES[$scope_id])) {
        return $__SCOPES[$scope_id][$varname];
    }
    // defined somewhere outside the local scope
    else {
        return __lookup_nonlocal($scope_id, $varname);
    }
}

// function &__get_var_nonlocal($scope_id, $varname) {
//     return __get_var($scope_id, $varname, True);
// }



__register_scope(0, null);

$a = function() {
    // $__scope_id = uniqid('', True);
    __register_scope(1, 0);

    // $var = 1;
    __set_var(1, 'var', 1);
    __set_var(1, 'b', function() {
        __register_scope(2, 1);
        __set_var(2, 'c', function() {
            __register_scope(3, 2);
            // NONLOCAL: using variable from 2 scopes outside
            __set_var_nonlocal(3, 'var', 4);
        });
        call_user_func(__get_var(2, 'c'));
    });


    // $b = function() {
    //     __register_scope(2, get_defined_vars());
    //     $c = function() {
    //         __register_scope(2, get_defined_vars());
    //         // using variable from 2 scopes outside
    //         // $var = 4;
    //         __set_var(2, 'var', 4);
    //     };
    // };

    // $b();
    call_user_func(__get_var(1, 'b'));
    return __get_var(1, 'var');
};



// $res = $a();
// echo "\nRESULT: ";
// var_dump($res);
// echo "\n";
// var_dump($__SCOPES);




// As a note, get_defined_vars() does not return a set of variable references (as I hoped). For example:
//
//
// // define a variable
// $my_var = "foo";
//
// // get our list of defined variables
// $defined_vars = get_defined_vars();
//
// // now try to change the value through the returned array
// $defined_vars["my_var"] = "bar";
//
// echo $my_var, "\n";
//
//
// will output "foo" (the original value). It'd be nice if get_defined_vars() had an optional argument to make them references, but I imagine its a rather specialized request. You can do it yourself (less conveniently) with something like:
//
//
// $defined_vars = array();
// $var_names = array_keys(get_defined_vars());
//
// foreach ($var_names as $var_name)
// {
//     $defined_vars[$var_name] =& $$var_name;
// }
//
