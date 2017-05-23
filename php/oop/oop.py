class A():
    def b(self):
        return 1

class B(A):
    def b(self):
        return super().b() + 1

b = B()

def n(self):
    return super(B, self).b() + 10

b.b = n
print(b.b(b))
