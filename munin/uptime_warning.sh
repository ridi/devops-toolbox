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

base_dir=$(dirname $0)
munin_dir=/etc/munin

source $base_dir/.env

uptime_new_max=`expr $uptime_max + $MUNIN_UPTIME_THRESHOLD_ADD`
group_arr=(${group//-/ })
munin_node_config=$(IFS=";" ; echo "${group_arr[*]};$munin_node")

# Reset munin.config
lines=`cat -n /etc/munin/munin.conf | sed -n "/$munin_node_config\]/,/\[/p" | awk '{print $1}' | sed -e 2b -e '$!d'`
lines=(${lines//' '/ })
sed "${lines[0]},${lines[1]} s/uptime.uptime.warning $uptime_max/uptime.uptime.warning $uptime_new_max/" $munin_dir/munin.conf > $munin_dir/munin_tmp.conf
mv $munin_dir/munin.conf $munin_dir/munin.conf.$(date +%s) && mv $munin_dir/munin_tmp.conf $munin_dir/munin.conf

# Create Asana task
php $base_dir/asana_client/create-uptime-task.php $munin_node@$group $uptime_now $uptime_max $uptime_new_max
