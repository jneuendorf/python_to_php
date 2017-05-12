import ast

from utils import inverted_dict, join, nl
from .helpers import compiled_children_by_node, indent


###############################################################################
# HELPERS
def compile_superclass(name, bases):
    return (
        f"class {name} {{{nl}" +
        # f"{nl}{(nl * 2).join(compiled_children['body'])}{nl}"
        "}\n"
    )

###############################################################################
# COMPILERS
def compile_class_def(node, compiled_children):
    if len(node.bases) == 0:
        extends = ""
        superclass = ""
    else:
        # strip leading "$" from each compiled base name
        bases = '_'.join(
            base[1:]
            for base in compiled_children['bases']
        )
        superclass_name = f"__{bases}"
        extends = f" extends {superclass_name}"
        superclass = compile_superclass(superclass_name, compiled_children["bases"])
    # for f in compiled_children["body"]:
    #     f = inverted_dict(compiled_children_by_node)[f]
    #     if isinstance(f, ast.FunctionDef):
    #         print(">>>", f)
    return (
        superclass +
        f"class {node.name}{extends} {{{nl}" +
        f"{nl}{(nl * 2).join(compiled_children['body'])}{nl}"
        "}\n"
    )
