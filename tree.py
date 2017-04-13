import re

import compilers


first_cap_re = re.compile('(.)([A-Z][a-z]+)')
all_cap_re = re.compile('([a-z0-9])([A-Z])')


def camel_to_snake(name):
    s1 = first_cap_re.sub(r'\1_\2', name)
    return all_cap_re.sub(r'\1_\2', s1).lower()


class Tree():

    def __init__(self, name, ast_node, children=None):
        self.name = name
        self.ast_node = ast_node
        if children is None:
            children = []
        self._parent = None
        self._children = []
        for child in children:
            self.add_child(child)

    def __iter__(self):
        def postordered(node):
            nodes = []
            for child in node.children():
                nodes.extend(postordered(child))
            nodes.append(node)
            return nodes
        return iter(postordered(self))

    def __str__(self):
        return ("  " * self.level()) + self.name

    def compile(self):
        print("...in compile() of ", self.name)
        method = getattr(compilers, "compile_" + camel_to_snake(self.name))
        if self.is_leaf():
            print("compiling", self.name)
            return method(node=self, compiled_children=None)
        return method(
            node=self,
            compiled_children="".join(
                child.compile() for child in self.children()
            )
        )

    # tree helpers

    def children(self):
        return list(self._children)

    def parent(self):
        return self._parent

    def is_leaf(self):
        return len(self.children()) == 0

    def add_child(self, child):
        self._children.append(child)
        child._parent = self
        return self

    def path_to_root(self):
        path = [self]
        node = self
        while node.parent() is not None:
            node = node.parent()
            path.append(node)
        return path

    def level(self):
        return len(self.path_to_root()) - 1
