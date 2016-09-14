#!/bin/sh

# This file should be run on the server to pull the latest version of the repo

# Make sure only root can run our script
if [ "$(id -u)" != "0" ]; then
   echo "This script must be run as root" 1>&2
   exit 1
fi

echo "Pulling latest version from the GitHub repo..."
git checkout master
git fetch --all --recurse-submodules
git reset --hard origin/master
chgrp -R www-data .
composer install
php artisan october:up