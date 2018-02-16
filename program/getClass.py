# -*- coding: UTF-8 -*-
from bs4 import BeautifulSoup


class TimeTable:
    classTable = {}
    classInfo = []
    classDay = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday']
    colspan = [1, 1, 1, 1, 1]
    classGrid = {}
    timeSet = []
    for h in range(9, 20):
        for m in ['00', '30']:
            timeSet.append(str(h) + ':' + m)
    classCode = ''
    classTitle = ''
    classStartTime = ''
    classEndTime = ''
    classLecturer = ''
    classLocation = ''
    classFrequency = ''

    def __init__(self, start_time, title, lecturer, location, frequency):
        self.classStartTime = start_time
        self.classEndTime = TimeTable.calcuEndTime(start_time)
        self.classTitle = title
        self.classCode = title[0:6]
        self.classLecturer = lecturer
        self.classLocation = location
        self.classFrequency = frequency

    @staticmethod
    def calcuEndTime(t):
        time = t.split(':')
        h = time[0]
        m = time[1]
        if m == '00':
            m = '30'
        else:
            m = '00'
            h = str(int(h) + 1)
        return h + ':' + m


def getClasses(date, html):
    row = 0
    # print('date: ' + str(date))
    TimeTable.classInfo = []
    TimeTable.classGrid = {}
    soup = BeautifulSoup(html, 'lxml')
    span = soup.find(text=TimeTable.classDay[date - 1]).find_parents('td')[0].get('colspan')
    try:
        span = int(span)
    except ValueError:
        span = 1
        TimeTable.colspan[date - 1] = span
        return
    else:
        TimeTable.colspan[date - 1] = span
    for t in TimeTable.timeSet:
        timeNode = soup.find(text=t).find_parents('td')[0]
        classTag = getNextCol(timeNode, date)
        for i in range(0, len(classTag)):
            if classTag[i].find('table') is not None:
                classDetails = classTag[i].table.findAll('td')
                classTitle = classDetails[0].get_text()
                if len(TimeTable.classInfo) > 0:
                    lastIndex = len(TimeTable.classInfo) - 1
                    # Compare with last item
                    if i == 0 and TimeTable.classInfo[lastIndex].classTitle == classTitle:
                        TimeTable.classInfo[lastIndex].classEndTime = TimeTable.calcuEndTime(timeNode.get_text())
                        # print('same as last, merged')
                    # Compare with upper item
                    elif row > 0:
                        prekey = str(row - 1) + '/' + str(i)
                        if TimeTable.classGrid.__contains__(prekey):
                            # print('detected upper: ' + prekey)
                            preIndex = TimeTable.classGrid[prekey]
                            # print('preIndex = ' + str(preIndex))
                            if TimeTable.classInfo[preIndex].classTitle == classTitle:
                                TimeTable.classInfo[preIndex].classEndTime = TimeTable.calcuEndTime(timeNode.get_text())
                                # print('same as upper, merged')
                            else:
                                createNewClass(timeNode, classDetails)
                        else:
                            createNewClass(timeNode, classDetails)
                    else:
                        createNewClass(timeNode, classDetails)
                else:
                    createNewClass(timeNode, classDetails)
                key = str(row) + '/' + str(i)
                lastkey = str(row - 1) + '/' + str(i)
                if row > 0 and TimeTable.classGrid.__contains__(lastkey):
                    ##
                    if TimeTable.classInfo[TimeTable.classGrid[lastkey]].classTitle != classTitle:
                        TimeTable.classGrid[key] = len(TimeTable.classInfo) - 1
                        # print('situation 1')
                    else:
                        TimeTable.classGrid[key] = TimeTable.classGrid[lastkey]
                        # print('situation 2')
                        ##
                else:
                    TimeTable.classGrid[key] = len(TimeTable.classInfo) - 1
                    #     print('situation 1')
                    # print('Inserted key : ' + key+" : "+str(TimeTable.classGrid[key]))
                    # print(TimeTable.classGrid)
                    # for c in TimeTable.classInfo:
                    #     print(c.classTitle)
        row += 1
        # print('row changing to: '+str(row))
    TimeTable.classTable[TimeTable.classDay[date - 1]] = TimeTable.classInfo


def createNewClass(timeNode, classDetails):
    newClass = TimeTable(timeNode.get_text(), classDetails[0].get_text(), classDetails[1].get_text(),
                         classDetails[2].get_text(), classDetails[3].get_text())
    TimeTable.classInfo.append(newClass)
    # print('\ncreated new')


def getNextCol(node, date):
    span = Span(date)
    if date == 1:
        return node.find_next_siblings()[0:span]
    if date == 2:
        return node.find_next_siblings()[Span(1): Span(1) + span]
    if date == 3:
        return node.find_next_siblings()[Span(1) + Span(2): Span(1) + Span(2) + span]
    if date == 4:
        return node.find_next_siblings()[Span(1) + Span(2) + Span(3): Span(1) + Span(2) + Span(3) + span]
    if date == 5:
        return node.find_next_siblings()[
               Span(1) + Span(2) + Span(3) + Span(4): Span(1) + Span(2) + Span(3) + Span(4) + span]


def Span(d):
    return TimeTable.colspan[d - 1]
