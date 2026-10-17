FROM debian:12

# Set the timezone, to fix possible timezone issue
ENV TZ=Europe/London
ENV DEBIAN_FRONTEND=noninteract

RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

RUN apt-get update && apt-get dist-upgrade -y

# Add an admin user that will own things like the logs.
RUN adduser admin

# Upgrade and install ondrej PPA
RUN apt-get update && apt-get dist-upgrade -y \
    && apt-get install -y apt-transport-https ca-certificates curl git gpg

# Add the repository for later versions of PHP.
RUN curl -fsSL https://packages.sury.org/php/apt.gpg \
  | gpg --dearmor -o /usr/share/keyrings/sury-php.gpg \
  && echo "deb [arch=amd64 signed-by=/usr/share/keyrings/sury-php.gpg] https://packages.sury.org/php/ bookworm main" \
  > /etc/apt/sources.list.d/php.list

# Expose port 80 for the web requests
EXPOSE 80

# Install the relevant packages
RUN apt-get update && apt-get install vim apache2 curl libapache2-mod-php8.5 git unzip supervisor cron composer \
    php8.5-cli php8.5-xml php8.5-mbstring php8.5-curl php8.5-bcmath  \
    php8.5-pgsql php8.5-zip php8.5-gd -y

# add the www-data user to the admin group.
RUN usermod --append www-data --groups admin

# Enable the php mod we just installed
RUN a2enmod php8.5 && a2enmod rewrite

# Manually set the apache environment variables in order to get apache to work immediately.
ENV APACHE_RUN_USER=www-data
ENV APACHE_RUN_GROUP=www-data
ENV APACHE_LOG_DIR=/var/log/apache2
ENV APACHE_RUN_DIR=/var/run/apache2

# It appears that the new apache requires these env vars as well
ENV APACHE_LOCK_DIR=/var/lock/apache2
ENV APACHE_PID_FILE=/var/run/apache2/apache2.pid

# Set display errors to on, we can decide in the application layer whether to hide or not based on the environment.
# Also bump up limits like the maximum
# upload size and memory limits, as we want to allow admins to upload massive hoard files and download large
# generated PDFs.
RUN sed -i 's;display_errors = .*;display_errors = On;' /etc/php/8.5/apache2/php.ini && \
    sed -i 's;post_max_size = .*;post_max_size = 100M;' /etc/php/8.5/apache2/php.ini && \
    sed -i 's;upload_max_filesize = .*;upload_max_filesize = 100M;' /etc/php/8.5/apache2/php.ini

# Add the site's code to the container.
# When in development, use a volume to overwrite this area.
COPY --chown=admin:www-data --chmod=u=rwX,g=rX,o= site /var/www/site

# Running this manually to set sticky bit on the directory as setting
# 2750 on the previous copy does not seem to work.
RUN chmod g+s /var/www/site

# Install PHP packages
RUN  cd /var/www/site && umask 027 && composer install

# Update our apache sites available with the config we created
ADD docker/apache-config.conf /etc/apache2/sites-enabled/000-default.conf

# Uncomment these if you are having your container handle SSL rather than using
# a reverse proxy like traefik
#EXPOSE 443
#VOLUME /etc/apache2/ssl
#RUN a2enmod ssl
#ADD docker/apache-ssl-config.conf /etc/apache2/sites-available/default-ssl.conf
#RUN a2ensite default-ssl

# Use the crontab file.
# The crontab file was already added when we added "project"
ADD docker/crons.conf /root/crons.conf
RUN crontab /root/crons.conf && rm /root/crons.conf

# Add supervisor config
COPY --chown=root:root docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Copy the script across that will create the .env file on startup for the web user to use.
COPY docker/create-env-file.php /root/create-env-file.php

# Add the remap-user script to the container.
COPY docker/remap-user.sh /root/remap-user.sh

# Add the startup script to the container
COPY docker/startup.sh /root/startup.sh

# Change the workdir to /var/www/site so that when devs enter the container, they are at the site
WORKDIR /var/www/site

# Execute the containers startup script which will start many processes/services
# The startup file was already added when we added "project"
CMD ["/bin/bash", "/root/startup.sh"]
