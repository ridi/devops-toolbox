#!/bin/bash

base_dir=$(dirname $0)
source $base_dir/.env

# Run script with given prameters
$base_dir/uptime_warning.sh $@

# Log params for debuggin
echo $@ >> /var/log/munin/munin_asana.log

# Check the last return code
if [ $? != 0 ]; then
  mail -s "Uptime warning script :: $(hostname)" ${EMAIL_ON_ERROR} <<< "Script Error."
fi
