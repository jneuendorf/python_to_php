import collections


class OrderedDict(collections.OrderedDict):

    def join(self):
        return "".join(
            value if not isinstance(value, list) else "".join(value)
            for key, value in self.items()
        )
