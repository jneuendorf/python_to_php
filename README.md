# python_to_php
A python to PHP transpiler.

Still in an early stage. Just found [dan-da/py2php](https://github.com/dan-da/py2php) which might be a helpful source.

## Features to be

- Compile any python code (though probably with some restrictions/conventions)
  - classes
    - class methods
    - instance methods
    - multiple inheritance
  - functions
    - scoping like in python (e.g. support `nonlocal`)
    - decorators
  - comprehensions
  - resolve imports
  - support python's native built-in types (tuple, list etc.) and functions (e.g. len)
    - emulate the protocol for rich comparison, attribute lookup etc.
- produce readable (as possible) PHP code while being performant enough
