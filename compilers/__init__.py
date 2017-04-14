import ast

from ordered_dict import OrderedDict
from node_visitor import TreeCreatorNodeVisitor
from tree import Tree
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
        # name = name_mapping[name] if name in name_mapping else name
        print(name)
        if name in compiled_children:
            result.append(compiled_children[name])
        else:
            result.append(field)
    return result


def compile_all(ast, filename):
    print("compiling", filename)
    visitor = TreeCreatorNodeVisitor()
    return visitor.visit(ast)
    # root = Tree(name="root", ast_node=ast)
    # # create children for `root`
    # visitor.visit(ast, root)
    # # for node in root:
    # #     print(str(node))
    # return root.compile()


def compile_list(nodes):
    return [node.compile() for node in nodes]


def compile_module(node, compiled_children):
    print("compile_module:", compiled_children["body"])
    return join(nl, compiled_children["body"])


def compile_function_def(node, compiled_children):
    print("compile_function_def:", node._fields, compiled_children)
    # name = node.name
    # name_mapping = {
    #     "args": "arguments",
    # }
    merged_fields = merge_plain_and_compiled_fields(
        node,
        compiled_children,
        # name_mapping
    )
    print("merged_fields", merged_fields)
    name, args, body, decorator_list, returns = merged_fields
    return (
        f"function {name}({args}){{"
        f"{join(nl, body)}"
        f"}}"
    )


def compile_arguments(node, compiled_children):
    print("compile_arguments:", node._fields)
    return join('', compiled_children)


def compile_return(node, compiled_children):
    # print("compile_return:", node._fields)
    # print(node.value)
    if node.value is not None:
        return f"return {join('', compiled_children)};"
    return ""


def compile_name(node, compiled_children):
    # print("compile_name:", node._fields)
    return f"${node.id}"
