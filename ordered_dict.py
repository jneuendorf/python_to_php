import collections


class OrderedDict(collections.OrderedDict):

    def __str__(self):
        return "".join(value for key, value in self.items())
