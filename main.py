import ast
import sys

import compilers

if __name__ == "__main__":
    argv = sys.argv

    filenames = argv[1:]

    for filename in filenames:
        with open(filename, "r") as file:
            source = file.read()
            ast_tree = ast.parse(source)
            php = compilers.compile_all(ast_tree, filename)

            print(
                "\n"
                "#############################################################"
                "\n"
            )
            print(php)


# key features of python (that might not be trivial in php):
# - multi inheritance
# - kwargs
# - import / modules
# - meta classes
# - decorators
# - generators (available in newer php)
