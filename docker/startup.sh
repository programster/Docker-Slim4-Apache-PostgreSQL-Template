#!/bin/bash
# Please do not manually call this file!
# This script is run by the docker container when it is "run"

# Stop if anything fails
set -e


# remap the user so things like logs can be accessed on the host easily.
/bin/bash /root/remap-user.sh


# Ensure the logs directory for supervisor exists.
mkdir -p /logs/supervisor \
  && chown --recursive admin:admin /logs \
  && chmod --recursive 775 /logs


# Make sure apache and cron services arenot running, just in case. Supervisor will manage them.
service apache2 stop
service cron stop


# Create the .env file
php /root/create-env-file.php /.env


# Run migrations after waiting for the database to be available.
php /var/www/site/scripts/wait-for-database.php
php /var/www/site/scripts/migrate.php


# Start supervisord to manage all processes and run in foreground
exec /usr/bin/supervisord -n
