from node_visitor import TreeCreatorNodeVisitor
from tree import Tree

from .arg import compile_arg
from .load import compile_load


def compile_all(ast, filename):
    print("compiling", filename)
    visitor = TreeCreatorNodeVisitor()
    root = Tree(name="root", ast_node=ast)
    # create children for `root`
    visitor.visit(ast, root)
    # for node in root:
    #     print(str(node))
    return root.compile()


def compile_root(node, compiled_children):
    print("root's children:", compiled_children)
    return compiled_children


def compile_function_def(node, compiled_children):
    print("compile_function_def:", node.ast_node._fields)
    return "function ${0}({1}){{}}".format(compiled_children)


def compile_arguments(node, compiled_children):
    print("compile_arguments:", node.ast_node._fields)
    return compiled_children


def compile_return(node, compiled_children):
    print("compile_return:", node.ast_node._fields)
    print(node.ast_node.value)
    return "return ${};".format(compiled_children)


def compile_name(node, compiled_children):
    print("compile_name:", node.ast_node._fields)
    return "${}".format(node.ast_node.id)
