import ast

from ordered_dict import OrderedDict
from node_visitor import TreeCreatorNodeVisitor
from utils import nl, join

from .arg import compile_arg
from .load import compile_load


# For each field: Returns the compiled child or the field itself
# (2nd case if the field was no ast node).
# @param name_mapping [dict] map field name to ast node class name
#   (e.g. for function_def: args -> arguments)
def merge_plain_and_compiled_fields(node, compiled_children):
    result = []
    # print("in merge_plain_and_compiled_fields......")
    # print(compiled_children)
    for name, field in ast.iter_fields(node):
        # print(name)
        if name in compiled_children:
            result.append(compiled_children[name])
        else:
            result.append(field)
    return result


def indent(node, more=0):
    more *= 4
    return (node.col_offset * " ") + (more * " ")


###############################################################################
# COMPILERS
def compile_all(ast, filename):
    print("compiling", filename)
    visitor = TreeCreatorNodeVisitor()
    return visitor.visit(ast)


def compile_module(node, compiled_children):
    print("compile_module:", compiled_children["body"])
    return join(nl, compiled_children["body"])


def compile_function_def(node, compiled_children):
    print("compile_function_def:", node._fields, compiled_children)
    merged_fields = merge_plain_and_compiled_fields(
        node,
        compiled_children,
    )
    print("merged_fields", merged_fields)
    name, args, body, decorator_list, returns = merged_fields
    return (
        f"{indent(node)}function {name}({args}){{{nl}"
            f"{join(nl, body)}{nl}"
        f"{indent(node)}}}"
    )


def compile_arguments(node, compiled_children):
    print("compile_arguments:", node._fields)
    return join('', compiled_children)


def compile_return(node, compiled_children):
    if node.value is not None:
        return f"{indent(node)}return {join('', compiled_children)};"
    return ""


def compile_name(node, compiled_children):
    return f"${node.id}"


def compile_pass(node, compiled_children):
    return ""
