from .helpers import compiled_children_by_node

def compile_tuple(node, compiled_children):
    elements = ", ".join(compiled_children['elts'])
    return f"__tuple({elements})"


# tuple unpackin: a, b = c
def compile_tuple_unpack(node):
    elements = ", ".join(compiled_children_by_node[node]['elts'])
    return f"list({elements})"
