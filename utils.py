import re

from ordered_dict import OrderedDict


# can be used in f-strings (because backslashes are not allowed)
nl = "\n"

first_cap_re = re.compile('(.)([A-Z][a-z]+)')
all_cap_re = re.compile('([a-z0-9])([A-Z])')


def camel_to_snake(name):
    s1 = first_cap_re.sub(r'\1_\2', name)
    return all_cap_re.sub(r'\1_\2', s1).lower()


def join(sep, obj):
    if hasattr(obj, "join"):
        return obj.join(sep)
    return sep.join(obj)


def is_hashable(obj):
    try:
        hash(obj)
        return True
    except Exception as e:
        return False


def custom_hash(obj):
    if isinstance(obj, OrderedDict):
        print("custom hash for", obj)
        return tuple(() for tup in obj.items())
    raise RuntimeError(
        "Could not hash object with type '" +
        obj.__class__.__name__ +
        "'."
    )


def inverted_dict(dictionary):
    try:
        result = {}
        for key in dictionary:
            value = dictionary[key]
            if is_hashable(value):
                result[value] = key
            else:
                print("not hashable:", value)
                result[custom_hash(value)] = key
        return result
    except Exception as e:
        print(dictionary, e)
        raise e
    # return {
    #     dictionary[key]: key
    #     for key in dictionary
    #     # if is_hashable(dictionary[key])
    # }
