def f(a):
    return a


def g():
    pass


# SCOPES / CLOSURES
global_var = 10


def a():
    def b():
        def c():
            in_c = 3
            return in_a + in_b + in_c
        in_b = 2
        return c()
    in_a = 1
    # ouputs 3
    return b()


# This should equal the above.
# In PHP vars should be passed by reference to make sure it behaves correctly.
def a():
    def b(in_a):
        def c():
            in_c = 3
            return in_a + in_b + in_c
        in_b = 2
        return c(in_a, in_b)
    in_a = 1
    return b(in_a)


# function a() {
#     $b = function() {
#         $in_b = 2;
#         return $in_a + $in_b;
#     };
#     $in_a = 1;
#     # outputs 2 because $in_a evaluates to 0
#     return $b();
# }

# TODO: ellipsis, starred arguments, kwargs
