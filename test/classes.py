class MyClass():

    a = 2

    def __init__(self):
        # super().__init__()
        self.a = "a"

    def get_a(self):
        return self.a


class Subclass(MyClass):

    def get_a(self):
        return super().get_a()
