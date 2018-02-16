# -*- coding: UTF-8 -*-
import sys
import ssl
from http import cookiejar
from urllib import parse
from urllib import request
from getClass import *


def getClassOnline(uname, psw):
    ssl._create_default_https_context = ssl._create_unverified_context
    print('Gathering stuff and login data...')
    # 声明一个CookieJar对象实例来保存cookie
    cookie = cookiejar.CookieJar()
    # 利用urllib.request库的HTTPCookieProcessor对象来创建cookie处理器,也就CookieHandler
    cookie_support = request.HTTPCookieProcessor(cookie)
    # 通过CookieHandler创建opener
    opener = request.build_opener(cookie_support)

    header = {
        'User-Agent': 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/59.0.3071.115 Safari/537.36',
        'Connection': 'keep-alive'
    }
    login_url = 'https://ebridge.xjtlu.edu.cn/urd/sits.urd/run/siw_lgn'

    username = uname
    password = psw

    # username = 'jin.dai15'
    # password = 'nanjidamaoxian'

    # 此处的open方法打开网页
    response1 = opener.open(login_url)

    loginForm = {}
    html = response1.read().decode('utf-8')
    soup = BeautifulSoup(html, 'lxml')
    for line in soup.form.findAll('input'):
        if 'value' in line.attrs:
            loginForm[line.attrs['name']] = line.attrs['value']
        else:
            loginForm[line.attrs['name']] = ''

    loginForm['MUA_CODE.DUMMY.MENSYS.1'] = username
    loginForm['PASSWORD.DUMMY.MENSYS.1'] = password

    loginData = parse.urlencode(loginForm).encode(encoding='UTF8')

    req = request.Request(login_url, loginData, header)
    print('Attempting to Log In...')
    response2 = opener.open(req)
    html2 = response2.read().decode('utf-8')

    soup2 = BeautifulSoup(html2, 'lxml')
    try:
        portal_url = 'https://ebridge.xjtlu.edu.cn/urd/sits.urd/run/' + \
                 soup2.find('input', {'name': 'HREF.DUMMY.MENSYS.1'}).attrs['value']
    except AttributeError:
        print('Login Failed.')
        sys.exit()
    print('Successfully Logged In.')
    response3 = opener.open(portal_url)
    html3 = response3.read().decode('utf-8')
    print('Redirected to Portal.')

    soup3 = BeautifulSoup(html3, 'lxml')
    timetable_page = 'https://ebridge.xjtlu.edu.cn/urd/sits.urd/run/' + soup3.find('a', {'id': 'TIMETABLE'}).attrs[
        'href']
    response4 = opener.open(timetable_page)
    html4 = response4.read().decode('utf-8')
    print('Entered TimeTable Page.')

    soup4 = BeautifulSoup(html4, 'lxml')
    try:
        href = soup4.find(text="My Personal Class Timetable").find_parents('a')[0].get('href')
    except AttributeError:
        try:
            href = soup4.find(text="My Personal Class Timetable (Week 2 - 14 )").find_parents('a')[0].get('href')
        except AttributeError:
            print('No class yet.')
            sys.exit()
    myTimetable_url = 'https://ebridge.xjtlu.edu.cn/urd/sits.urd' + href[2:]
    print('Fetching Your Class Timetable...\n\n')
    response5 = opener.open(myTimetable_url)
    html5 = response5.read().decode('utf-8')

    # write_file('./'+uname+'.php',html5)

    for d in range(1, 6):
        getClasses(d, html5)

#     for date in TimeTable.classDay:
#         print(date)
#         for c in TimeTable.classTable[date]:
#             print(
#                 '\n' + c.classStartTime + '-' + c.classEndTime + '\n' + c.classTitle + '\n' + c.classLecturer + '\n' + \
#                 c.classLocation + '\n' + c.classFrequency + '\n')
#
# getClassOnline()
def write_file(path,data):
    f = open(path,'w')
    f.write(data)
    f.close()
