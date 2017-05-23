class OrderedClass(type):
    @classmethod
    def __prepare__(metacls, name, bases, **kwds):
        return {}

    def __new__(cls, name, bases, namespace, **kwds):
        print("__new__ cls =", cls)
        result = super().__new__(cls, name, bases, dict(namespace))
        result.members = tuple(namespace)
        return result


class A(metaclass=OrderedClass):
    def one(self): pass

    def two(self): pass

    def three(self): pass

    def four(self): pass


print(A.members)
