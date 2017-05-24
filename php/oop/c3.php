<?php

function array_find($array, $predicate) {
    foreach ($array as $value) {
        if ($predicate($value)) {
            return $value;
        }
    }
    return null;
}

function array_every($array, $predicate) {
    foreach ($array as $value) {
        if (!$predicate($value)) {
            return False;
        }
    }
    return True;
}

function array_filter_reindex($array, $callback) {
    return array_values(array_filter($array, $callback));
}

// ported from https://github.com/arximboldi/heterarchy
function c3_linearization($mros) {
    // foreach ($mros as $mro) {
    //     echo '['.implode(', ', array_map(function($cls) {
    //         return $cls->__name__;
    //     }, $mro)).']';
    // }
    $result = [];
    while (count($mros) > 0) {
        $next = array_find(
            array_map(function($mro) {
                return $mro[0];
            }, $mros),
            function($candidate) use ($mros) {
                return array_every($mros, function($mro) use ($candidate) {
                    return !in_array($candidate, array_slice($mro, 1));
                });
            }
        );
        if ($next === null) {
            throw new Exception('Inconsistent multiple inheritance', 1);
        }
        $mros = array_filter_reindex(
            array_map(function($mro) use ($next) {
                return array_filter_reindex($mro, function($cls) use ($next) {
                    return $cls !== $next;
                });
            }, $mros),
            function($mro) {
                return count($mro) !== 0;
            }
        );
        $result[] = $next;
    }
    return $result;
}
