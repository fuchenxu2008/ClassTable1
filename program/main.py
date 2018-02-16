# -*- coding: UTF-8 -*-
from login import *
from makeiCalendar import *
import sys


if __name__ == "__main__":
    uname = sys.argv[1]
    psw = sys.argv[2]
    getClassOnline(uname, psw)
    generateICS(uname)
    print(name)
