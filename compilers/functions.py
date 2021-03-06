import ast

from ordered_dict import OrderedDict
from utils import nl, join

from .helpers import compiled_children_by_node, indent


def compile_function_definition(node, fields, modifiers=None):
    name, args, body, decorator_list, returns = fields.values()
    if modifiers is None:
        modifiers = ""
    else:
        modifiers = f"{' '.join(modifiers)} "
    return (
        f"{indent(node)}{modifiers}function {name}({args}) {{{nl}"
            f"{join(nl, body)}{nl}"
        f"{indent(node)}}}"
    )


# class Sub extends Super {
#     function get_a() {
#         return $this->__get_a($this);
#     }
#
#     function __get_a($self) {
#         return $self->a;
#     }
# }
# RESTRICTION: Convention to name first argument `self`.
def compile_method_definition(node, fields):
    delegation_target = fields.copy()
    delegation_target["name"] = f"__{fields['name']}"
    # the python method has self as 1st arg -> strip "$self, " from start
    delegation_target["args"] = fields['args'][7:]
    if len(delegation_target["args"]) > 0:
        delegation_target["args"] = f"$self, {delegation_target['args']}"
    else:
        delegation_target["args"] = "$self"

    delegator = fields.copy()
    if delegator["name"] == "__init__":
        delegator["name"] = "__construct"
        delegation_target["name"] = f"__init__"
    delegator["args"] = fields['args'][7:]
    delegation_args = delegation_target['args'].replace('$self', '$this')
    delegator["body"] = [
        indent(node, 1) +
        f"return $this->{delegation_target['name']}({delegation_args});"
    ]
    return (
        f"{compile_function_definition(node, delegator, modifiers=['public'])}{nl}"
        f"{compile_function_definition(node, delegation_target, modifiers=['protected'])}"
    )


# For each field: Returns the compiled child or the field itself
# (2nd case if the field was no ast node).
# @param name_mapping [dict] map field name to ast node class name
#   (e.g. for function_def: args -> arguments)
def merge_plain_and_compiled_fields(node, compiled_children):
    result = OrderedDict()
    # print("in merge_plain_and_compiled_fields......")
    # print(compiled_children)
    for name, field in ast.iter_fields(node):
        # print(name)
        if name in compiled_children:
            result[name] = compiled_children[name]
        else:
            result[name] = field
    return result


###############################################################################
# COMPILERS
def compile_function_def(node, compiled_children, ancestors):
    merged_fields = merge_plain_and_compiled_fields(
        node,
        compiled_children,
    )
    if len(ancestors) > 0 and isinstance(ancestors[0], ast.ClassDef):
        return compile_method_definition(node, merged_fields)
    else:
        return compile_function_definition(node, merged_fields)


def compile_call(node, compiled_children):
    print("compile_call", compiled_children)
    # python's super() call becomes just `parent`
    if compiled_children['func'] == "parent":
        return f"{compiled_children['func']}"
    else:
        return f"{compiled_children['func']}({join('', compiled_children['args'])})"


def compile_arguments(node, compiled_children):
    print("compile_arguments:", node._fields)
    return join('', compiled_children)


def compile_return(node, compiled_children):
    if node.value is not None:
        return f"{indent(node)}return {join('', compiled_children)};"
    return ""
