def compile_arg(node, compiled_children):
    # print(node.ast_node._fields)
    # print(node.ast_node.arg, node.ast_node.annotation)
    return "${}".format(node.ast_node.arg)
