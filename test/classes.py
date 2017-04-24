class MyClass():

    def __init__(self):
        # super().__init__()
        self.a = "a"

    def get_a(self):
        return self.a


class Subclass(A, B):
    pass
