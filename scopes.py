# coding: utf-8


class Printer():

    def print(self, *args, **kwargs):
        print("I'm the old guy...")
        print(*args, **kwargs)


def log_to(logger=print):
    def decorator(func):
        def wrapper(*args, **kwargs):
            logger("{}(*{}, **{})".format(func.__qualname__, args, kwargs))
            result = func(*args, **kwargs)
            # logger("-> {}".format(result))
        return wrapper
    return decorator


printer = Printer()


# is looked up when this module is imported
# => the decorator uses the OLD GUY
@log_to(printer.print)
def foo(a, b):
    # is looked up whenever the function is run
    # => this uses the NEW GUY
    printer.print("inside the function")
    return a + b


# replace printer's print function
def print_function(self, *args, **kwargs):
    print("I'm the new guy!!")
    print(*args, **kwargs)


Printer.print = print_function


foo(1, 2)


print("\n")


def func():
    # 1. looked up when run
    # 2. the scope it's looked up in is determined at "compile-time"
    print("func: item =", item)


# NameError: name 'item' is not defined
# print("item1", item)

# so this works (for loops don't create a new scope)
for item in range(3):
    func()

# and this does weird stuff, because list comprehensions have their own scope
# -> does not change the value of `item`
[func() for item in range(3)]


# In[17]:
def decorator(func):
    called = 0

    def wrapper(*args, **kwargs):
        nonlocal called
        called = called + 1
        func(*args, **kwargs)
        print("called", func.__qualname__, called, "time(s)")
    return wrapper


# new value for `foo`
@decorator
def foo():
    pass


@decorator
def bar():
    pass


# In[18]:

foo()
bar()
foo()


# # for more stuff on how exactly this stuff works:
#
# https://www.youtube.com/watch?v=E9wS6LdXM8Y


# class MethodNames():
#
#     def a(self):
#         print("instance", self)
#
#     @classmethod
#     def a(cls):
#         print("class", cls)
#
#
# mn = MethodNames()
# NOTE: class methods can also be called on the instance
# mn.a()
# MethodNames.a()


class A:
    def method(self, *args):
        print(self, "-", *args)


def new_instance_method(self, *args):
    print(self, "=>", *args)


A.method = new_instance_method


a = A()
a2 = A()
a.method(1, 2)
a2.method(3, 4)
A.method(["some new self"], 1, 2)


def new_instance_method(self, *args):
    print(self, "===>", *args)


a.method = new_instance_method
# 1 === self
a.method(1, 2)
