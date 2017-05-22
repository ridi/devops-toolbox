#!/bin/bash

MYSQL=`mysql --defaults-file=/etc/mysql/debian.cnf -e "SHOW GLOBAL STATUS LIKE 'Slave_running'" | grep 'Slave_running' | awk '{ print  $2 }'`
res="$MYSQL"
if [ $res != "ON" ]; then
  touch replication.broken
  echo 'Replication was broken' | /usr/bin/mail -s '[db] Replication was broken' sentinel@ridi.com
fi
#MYSQL=`mysql --defaults-file=/etc/mysql/debian.cnf -e "SHOW SLAVE STATUS\G"`
