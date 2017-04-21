compiled_children_by_node = {}

_var_count = 0


def var_count():
    res = _var_count
    _var_count += 1
    return res


def indent(node=None, more=0):
    if node is None:
        col_offset = 0
    else:
        col_offset = node.col_offset
    more *= 4
    return (col_offset * " ") + (more * " ")
