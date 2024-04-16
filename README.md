# logviewer
Logviewer is a Prestashop module that allows you to check out your shop's logs and exceptions from the back-office (for those who can't be bothered with using a shell or grap the logs via FTP).   
# The settings
Upon installation you'll be able to set the amount of logs you want to see by setting the number of days for the log history and the types of logs you want (by context and level, which apply only to logs).   
The maximum days is 5, which should be more than enough and can already represent a substantial amount of data (especialy for the dev enviroment).
# The list views
There are two lists:
- logs (read from either dev.log or prod.log)
- exceptions (read from the dated exceptions logs)   

The lists are initially empty: click the settings icon on the upper-right corner of the grid view and update the list.   
The lists are not updated automatically (they could be but I've decided against it) but via the settings icon.
Both lists can filtered and searched.

