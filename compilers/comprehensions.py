import ast

from .helpers import compiled_children_by_node, indent, var_count


# HELPERS
# recursilvey create for loops
def comprehensions_to_for_loop(comprehensions, inner_expression, indentation=""):
    if len(comprehensions) == 0:
        return indentation + inner_expression

    comprehension = comprehensions.pop(0)
    target, iter, ifs, is_async = comprehension
    target_source = target["source"]
    target_items_to_unpack = target["items_to_unpack"]
    body = comprehensions_to_for_loop(comprehensions, inner_expression, indentation + "    ")
    if len(ifs) > 0:
        ifs = " and ".join(f"({if_stmt})" for if_stmt in ifs)
        body = f"if () {{ {body} }}"
    if len(target_items_to_unpack) == 0:
        tuple_unpacking = ""
    else:
        tuple_unpacking = (
            indentation + "    " +
            f"list({', '.join(target_items_to_unpack)}) = __list({target_source});" + "\n"
        )
    return (
        indentation + f"foreach ({iter} as {target_source}) {{" + "\n" +
        tuple_unpacking +
        # body is indented itself
        body + "\n" +
        indentation + "}"
    )


# COMPILERS

# NOTE: This compiler does not return a string
#       because the comprehensions are siblings in python
#       but children in PHP.
#       That can't be compiled that way by this function.
def compile_comprehension(node, compiled_children):
    # RESTRICTION: only tuples with 2 elements supported
    if isinstance(node.target, ast.Tuple):
        joined_tuple_items = '_'.join(
            # strip '$'
            elt[1:]
            for elt in compiled_children_by_node[node.target]['elts']
        )
        compiled_children["target"] = {
            "source": f"$__{compiled_children['iter'][1:]}_item",
            "items_to_unpack": compiled_children_by_node[node.target]['elts'],
        }
    else:
        compiled_children["target"] = {
            "source": compiled_children["target"],
            "items_to_unpack": [],
        }
    # is_async is a int
    compiled_children["is_async"] = bool(node.is_async)
    return compiled_children


def compile_list_comp(node, compiled_children):
    php = indent(node) + "$__comprehension_result = array();\n"
    php += comprehensions_to_for_loop(
        [
            compiled_comprehension.values()
            for compiled_comprehension in compiled_children["generators"]
        ],
        f"array_push($__comprehension_result, {compiled_children['elt']});",
        indent(node)
    )
    php += "\n" + indent(node) + "return $__comprehension_result;"
    # variables that must be made accessible in the function call
    top_level_iter = compiled_children['generators'][0]['iter']

    func_call = (
        f"call_user_func(function($self, {top_level_iter}) {{" + "\n" +
        php + "\n" +
        f"}}, $this, {top_level_iter})"
    )
    return func_call


# l = [True for row in table for cell in row]
#
# $__comprehension_result = array();
# foreach ($table => $row) {
#   foreach ($row => $cell) {
#       array_push($__comprehension_result, True);
#   }
# }
# $l = $__comprehension_result;
# unset($__comprehension_result);
