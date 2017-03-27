#!/bin/bash

label=$1
if [ "$label" != "uptime" ]
then
  exit
fi

group=$2
munin_node=$3
uptime_now=$4
uptime_max=${5//:/}

if [ $(echo "$uptime_now < $uptime_max"|bc) = 1 ]; then
  exit
fi

uptime_new_max=`expr $uptime_max + 20`
group_arr=(${group//-/ })
munin_node_config=$(IFS=";" ; echo "${group_arr[*]};$munin_node")

#reset munin.config
lines=`cat -n /etc/munin/munin.conf | sed -n "/$munin_node_config\]/,/\[/p" | awk '{print $1}' | sed -e 2b -e '$!d'`
lines=(${lines//' '/ })
sed "${lines[0]},${lines[1]} s/uptime.uptime.warning $uptime_max/uptime.uptime.warning $uptime_new_max/" /etc/munin/munin.conf > /etc/munin/munin_tmp.conf
mv /etc/munin/munin.conf /etc/munin/munin.conf.$(date +%s) && mv /etc/munin/munin_tmp.conf /etc/munin/munin.conf

#create asana task
php /home/test/munin_integration/asana/asana_client/create-uptime-task.php $munin_node $uptime_now $uptime_max $uptime_new_max
