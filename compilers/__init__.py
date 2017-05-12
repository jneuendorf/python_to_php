import ast

from ordered_dict import OrderedDict
from node_visitor import TreeCreatorNodeVisitor
from utils import nl, join

from .helpers import compiled_children_by_node, indent

from .arg import compile_arg
from .classes import *
from .comprehensions import *
from .functions import *
from .load import compile_load
from .literals import *
from .tuple import *
from .variables import *


###############################################################################
# HELPERS


###############################################################################
# COMPILERS
def compile_all(ast, filename):
    print("compiling", filename)
    visitor = TreeCreatorNodeVisitor()
    return visitor.visit(ast, compiled_children_by_node)


def compile_module(node, compiled_children):
    # print("compile_module:", compiled_children["body"])
    return join(nl, compiled_children["body"])


def compile_name(node, compiled_children, ancestors):
    if node.id == "super":
        name = "parent"
    else:
        name = f"${node.id}"
    # TODO: this breaks the name of a superclass because the bases have a ClassDef as 1st ancestor
    # if len(ancestors) > 0 and isinstance(ancestors[0], ast.ClassDef):
    #     name = f"public {name}"
    return name


def compile_pass(node, compiled_children):
    return ""


def compile_expr(node, compiled_children):
    return f"({compiled_children['value']})"


# True, False, None
def compile_name_constant(node, compiled_children):
    if node.value is not None:
        return str(node.value)
    return "null"


# Attribute access: b = a.prop (load), a.prop = 2 (store)
def compile_attribute(node, compiled_children):
    if compiled_children["value"] == "parent":
        return f"{compiled_children['value']}::{node.attr}"
    else:
        return f"{compiled_children['value']}->{node.attr}"
