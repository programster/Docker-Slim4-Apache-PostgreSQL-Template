# Please do not manually call this file!
# This script is run by the docker container when it is "run"


# Create the .env file
php /root/create-env-file.php /.env


# Run migrations after waiting for the database to be available.
/usr/bin/sleep 10
php /var/www/site/scripts/migrate.php


# Run the apache process in the background
/usr/sbin/apache2 -D APACHE_PROCESS &


# Start the cron service in the foreground
# We dont run apache in the FG, so that we can restart apache without container
# exiting.
cron -f
