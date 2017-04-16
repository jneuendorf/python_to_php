def compile_num(node, compiled_children):
    print("compile_num:", compiled_children)
    if not isinstance(node.n, complex):
        return str(node.n)
    return f"__complex({node.n.real},{node.n.imag})"


def compile_str(node, compiled_children):
    return f"'{node.s}'"


def compile_u_sub(node, compiled_children):
    print("compile_u_sub", node, compiled_children)
    return "-"


def compile_unary_op(node, compiled_children):
    print("compile_unary_op", node, compiled_children)
    return f"{compiled_children['op']}({compiled_children['operand']})"
