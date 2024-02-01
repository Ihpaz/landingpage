#!/bin/bash

# ----------------------------------------------------------------------
# Create the .env file if it does not exist.
# ----------------------------------------------------------------------

if [[ ! -f "/application/.env" ]] && [[ -f "/application/.env.example" ]];
then
cp /application/.env.example /.env
fi

# ----------------------------------------------------------------------
# Run Composer
# ----------------------------------------------------------------------

if [[ ! -d "/application/vendor" ]];
then
cd /application
composer install --prefer-dist --no-dev -o
composer dump-autoload -o
fi

# ----------------------------------------------------------------------
# Start crontab
# ----------------------------------------------------------------------

service cron start

# ----------------------------------------------------------------------
# Start supervisord
# ----------------------------------------------------------------------

exec /usr/bin/supervisord -n -c /etc/supervisor/conf.d/worker.conf