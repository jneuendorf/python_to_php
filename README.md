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
    - keyword and positional arguments
    - scoping like in python (e.g. support `nonlocal`)
    - decorators
  - comprehensions
  - resolve imports
  - support python's native built-in types (tuple, list etc.) and functions (e.g. len)
    - emulate the protocol for rich comparison, attribute lookup etc.
- produce readable (as possible) PHP code while being performant enough


## Restrictions (things that probably won't compile correctly)

- nested classes (http://stackoverflow.com/a/1765716)
- virtual subclasses (abstract base classes)
- `__metaclass__` attribute on classes to define what meta class to use. Should use `metaclass=MC` in the class definition.
