# def decorator(argument):
#     print("decorated")
#     return argument


class MyClass():
    """docstring for MyClass"""

    # @decorator
    # def __init__(self, a):
    #     self.a = a

    def f(arg1, arg2=1, *kwargs):
        print(str(kwargs))
        return arg1 + arg2

    
