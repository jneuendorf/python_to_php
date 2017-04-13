from node_visitor import TreeCreatorNodeVisitor
from tree import Tree


def compile(ast, filename):
    print("compiling", filename)
    visitor = TreeCreatorNodeVisitor()
    root = Tree(name="root", ast_node=ast)
    # create children for `root`
    visitor.visit(ast, root)
    for node in root:
        print(str(node))


def join_tree(arg):
    pass
