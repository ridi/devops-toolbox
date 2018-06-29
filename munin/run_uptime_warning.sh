#!/bin/bash

base_dir=$(dirname $0)
source $base_dir/.env

# Run script with given prameters
./uptime_warning.sh $@

# Check the last return code
if [ $? != 0 ]; then
  mail -s "Uptime warning script :: $(hostname)" ${EMAIL_ON_ERROR} <<< "Script Error."
fi
