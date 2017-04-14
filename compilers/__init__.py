import ast

from ordered_dict import OrderedDict
from node_visitor import TreeCreatorNodeVisitor
from tree import Tree

from .arg import compile_arg
from .load import compile_load


# For each field: Returns the compiled child or the field itself
# (2nd case if the field was no ast node).
# @param name_mapping [dict] map field name to ast node class name (e.g. for function_def: args -> arguments)
def get_fields(ast_node, compiled_children, name_mapping):
    result = []
    print("in get_fields......")
    print(compiled_children)
    for name, field in ast.iter_fields(ast_node):
        name = name_mapping[name] if name in name_mapping else name
        print(name)
        if name in compiled_children:
            result.append(compiled_children[name])
        else:
            result.append(field)
    return result


def compile_all(ast, filename):
    print("compiling", filename)
    visitor = TreeCreatorNodeVisitor()
    root = Tree(name="root", ast_node=ast)
    # create children for `root`
    visitor.visit(ast, root)
    # for node in root:
    #     print(str(node))
    return root.compile()


def compile_list(nodes):
    return [node.compile() for node in nodes]


def compile_root(node, compiled_children):
    print("root's children:", compiled_children)
    return compiled_children.join()


def compile_function_def(node, compiled_children):
    print("compile_function_def:", node.ast_node._fields, compiled_children)
    # name = node.ast_node.name
    name_mapping = {
        "args": "arguments",
    }
    name, args, body, decorator_list, returns = get_fields(
        node.ast_node,
        compiled_children,
        name_mapping
    )
    return (
        f"function ${name}({args}){{"
        f"{body}"
        f"}}"
    )


def compile_arguments(node, compiled_children):
    print("compile_arguments:", node.ast_node._fields)
    return compiled_children.join()


def compile_return(node, compiled_children):
    # print("compile_return:", node.ast_node._fields)
    # print(node.ast_node.value)
    if node.ast_node.value is not None:
        return f"return {compiled_children.join()};"
    return ""


def compile_name(node, compiled_children):
    # print("compile_name:", node.ast_node._fields)
    return f"${node.ast_node.id}"
