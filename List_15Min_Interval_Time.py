import datetime
from datetime import timedelta

dateList = ""
time_now = datetime.datetime(2012, 2, 23, 0, 0)
i = 0
while i<288:
	t = time_now.strftime('%H:%M')
	dateList+= "<option value=\"" + t + "\">" + t + "</option>\n"
	time_now = time_now + timedelta(minutes=5)
	i = i + 1

print(dateList)
