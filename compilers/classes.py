import ast

from utils import inverted_dict, join, nl
from .helpers import compiled_children_by_node, indent


def compile_class_def(node, compiled_children):
    if len(node.bases) == 0:
        extends = ""
    else:
        # TODO: create superclass
        bases = '_'.join(
            base[1:]
            for base in compiled_children['bases']
        )
        superclass_name = f"__{bases}"
        extends = f" extends {superclass_name}"
    # for f in compiled_children["body"]:
    #     f = inverted_dict(compiled_children_by_node)[f]
    #     if isinstance(f, ast.FunctionDef):
    #         print(">>>", f)
    return (
        f"class {node.name}{extends} {{{nl}" +
        "\n\n".join(compiled_children["body"]) + "\n"
        "}\n"
    )
