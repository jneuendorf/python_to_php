import ast

from .helpers import indent
from .tuple import compile_tuple_unpack


# ctx=Del()
def compile_store(node, compiled_children):
    # print("compile_store:", compiled_children)
    return ""


def compile_assign(node, compiled_children):
    # print("compile_store:", compiled_children)
    # check for tuples -> unpacking with `list`
    for idx, target in enumerate(node.targets):
        if isinstance(target, ast.Tuple):
            compiled_children['targets'][idx] = compile_tuple_unpack(target)
    targets = "".join(
        f"{target} = "
        for target in compiled_children['targets']
    )
    return f"{indent(node)}{targets}{compiled_children['value']};"


def compile_delete(node, compiled_children):
    # print("compile_delete:", compiled_children)
    targets = ", ".join(compiled_children["targets"])
    return f"unset({targets});"


# ctx=Del()
def compile_del(node, compiled_children):
    return ""
