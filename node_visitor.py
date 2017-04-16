import ast

import compilers
from ordered_dict import OrderedDict
from utils import camel_to_snake


def dump(node, annotate_fields=True, include_attributes=False, indent='  '):
    """
    Return a formatted dump of the tree in *node*. This is mainly useful for
    debugging purposes. The returned string will show the names and the values
    for fields. This makes the code impossible to evaluate, so if evaluation is
    wanted *annotate_fields* must be set to False.  Attributes such as line
    numbers and column offsets are not dumped by default. If this is wanted,
    *include_attributes* can be set to True.
    """
    def _format(node, level=0):
        if isinstance(node, ast.AST):
            fields = [(a, _format(b, level)) for a, b in ast.iter_fields(node)]
            if include_attributes and node._attributes:
                fields.extend([(a, _format(getattr(node, a), level))
                               for a in node._attributes])
            return ''.join([
                node.__class__.__name__,
                '(',
                ', '.join(
                    ('%s=%s' % field for field in fields)
                    if annotate_fields else
                    (b for a, b in fields)
                ),
                ')'])
        elif isinstance(node, list):
            lines = ['[']
            lines.extend((indent * (level + 2) + _format(x, level + 2) + ','
                         for x in node))
            if len(lines) > 1:
                lines.append(indent * (level + 1) + ']')
            else:
                lines[-1] += ']'
            return '\n'.join(lines)
        return repr(node)

    if not isinstance(node, ast.AST):
        raise TypeError('expected AST, got %r' % node.__class__.__name__)
    return _format(node)


def parseprint(code, filename="<string>", mode="exec", **kwargs):
    """Parse some code from a string and pretty-print it."""
    node = parse(code, mode=mode)   # An ode to the code
    print(dump(node, **kwargs))


def get_name(node):
    return camel_to_snake(node.__class__.__name__)


###############################################################################
class TreeCreatorNodeVisitor():

    def visit(self, ast_node, compiled_children_by_node):
        print("\nvisiting", get_name(ast_node))
        # print(dump(ast_node, include_attributes=True))
        compiled_children = OrderedDict()
        for field, value in ast.iter_fields(ast_node):
            # print("iterating:", get_name(ast_node), field)
            if isinstance(value, list):
                compiled_children[field] = [
                    self.visit(item, compiled_children_by_node)
                    for item in value
                    if isinstance(item, ast.AST)
                ]
            elif isinstance(value, ast.AST):
                compiled_children[field] = self.visit(
                    value,
                    compiled_children_by_node
                )
        compile_func = getattr(
            compilers,
            "compile_" + get_name(ast_node)
        )
        compiled_children_by_node[ast_node] = compiled_children.copy()
        return compile_func(ast_node, compiled_children)
