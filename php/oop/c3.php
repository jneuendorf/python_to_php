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

// ported from https://github.com/arximboldi/heterarchy/blob/master/heterarchy.litcoffee
function c3_linearization($mros) {
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
        $mros = array_filter(
            array_map(function($mro) use ($next) {
                return array_filter($mro, function($cls) use ($next) {
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

/*
merge = function(inputs) {
  var next, results;
  results = [];
  while (!isEmpty(inputs)) {
    next = find(map(inputs, head), function(candidate) {
      return every(inputs, function(input) {
        return indexOf.call(tail(input), candidate) < 0;
      });
    });
    assert(next != null, "Inconsistent multiple inheritance");
    inputs = reject(map(inputs, function(lst) {
      return without(lst, next);
    }), isEmpty);
    results.push(next);
  }
  return results;
};
*/
