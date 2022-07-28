#!/bin/ash
echo "* * * * * cd /var/ww/html &w& php artisan schedule:run >> /dev/null 2>&1" >> /etc/crontabs/root

# You can put the rest of your entrypoint.sh below this line
