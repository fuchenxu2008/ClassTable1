# -*- coding: UTF-8 -*-
from icalendar import Calendar, Event
from datetime import datetime
from datetime import timedelta
from getClass import *


# class iCalendar:
def iCalendar(cal, date, classTitle, startTime, endTime, lecturer, location, frequency):
    weekdic = getInterval(frequency)
    # print(weekdic)
    for start_week in weekdic:
        event = Event()
        event.add('summary', classTitle + ' by ' + lecturer)
        start = datetime.strptime(getDate(date, start_week, startTime), "%Y-%m-%d %H:%M")
        end = datetime.strptime(getDate(date, start_week, endTime), "%Y-%m-%d %H:%M")
        event.add('dtstart', start)
        event.add('dtend', end)
        event.add('location', location)
        event.add('description', frequency)
        end_week = weekdic.get(start_week)
        repeat_time = end_week - start_week + 1
        if repeat_time > 1:
            event.add('rrule', {'freq': 'weekly', 'count': str(repeat_time)})
        event.add('dtstamp', datetime.now())
        # event.add('uid', classTitle[0:6])
        cal.add_component(event)


def generateICS(uname):
    cal = Calendar()
    cal.add('prodid', '-//Chenxu.Fu15//Class TimeTable Crawler//EN')
    cal.add('version', '2.0')
    for date in TimeTable.classDay:
        if TimeTable.classTable.__contains__(date):
            for c in TimeTable.classTable[date]:
                iCalendar(cal, date, c.classTitle, c.classStartTime, c.classEndTime, c.classLecturer, c.classLocation, c.classFrequency)
    f = open(uname+'.ics', 'wb')
    f.write(cal.to_ical())
    f.close()
    print('File Written.')


def getDate(date, week, timeNode):
    for d in range(0, 6):
        if date == TimeTable.classDay[d]:
            days = d + (week - 1) * 7
            if len(timeNode) < 5:
                timeNode = '0' + timeNode
            term_start = datetime.strptime('2017-09-04', '%Y-%m-%d')
            dateTime = (term_start + timedelta(days=days)).strftime('%Y-%m-%d') + ' '+timeNode
            return dateTime


def getInterval(weeks):
    timeInterval = {}
    weeks = weeks.split(':')[1]
    a = weeks.split(',')
    for i in range(0, len(a)):
        b = a[i].split('-')
        begin = int(b[0])
        if len(b) > 1:
            end = int(b[1])
            if begin >= 5 and end >= 5:
                timeInterval[begin + 1] = end + 1
            elif begin < 5 and end >= 5:
                timeInterval[begin] = 4
                timeInterval[6] = end + 1
            else:
                timeInterval[begin] = end
        else:
            if begin >= 5:
                timeInterval[begin + 1] = begin + 1
            else:
                timeInterval[begin] = begin
    return timeInterval
