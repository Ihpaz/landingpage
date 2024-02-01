FROM ubuntu:22.04
WORKDIR "/var/www/application"

## for apt to be noninteractive
ENV DEBIAN_FRONTEND noninteractive
ENV DEBCONF_NONINTERACTIVE_SEEN true

RUN apt-get update && \
    apt-get install -y software-properties-common wget

# Configure repository
RUN add-apt-repository ppa:ondrej/php

RUN apt-get update    

RUN apt-get -y --no-install-recommends install \
    nginx curl \
    openssl zip unzip git supervisor cron nano \
    php-pear build-essential libaio1 \
    php8.2-fpm \
    php8.2-dev \
    php8.2-bcmath \ 
    php8.2-curl \
    php8.2-dba \ 
    php8.2-gd \
    php8.2-gmp \ 
    php8.2-imap \ 
    php8.2-imagick \
    php8.2-intl \ 
    php8.2-ldap \ 
    php8.2-mbstring \ 
    php8.2-mcrypt \ 
    php8.2-mongo \
    php8.2-mysql \
    php8.2-pgsql \ 
    php8.2-redis \
    php8.2-raphf \ 
    php8.2-xsl \ 
    php8.2-yaml \
    php8.2-zip

RUN apt-get clean; \
    rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

# Install composer globally
RUN curl -sSL https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer 
    
# Install locales
ENV LC_TIME id_ID.UTF-8

# Expose the port nginx is reachable on
EXPOSE 8000

# Create directory
RUN mkdir -p /home/supervisor
RUN mkdir -p /run/php

# Configure crontab
ADD docker/php-fpm/schedule/crontab /etc/cron.d/cron
RUN chmod 0644 /etc/cron.d/cron
RUN crontab /etc/cron.d/cron

# Run Supervisor Start Nginx & Php
COPY docker/php-fpm/supervisor/worker.conf /etc/supervisor/conf.d/worker.conf
COPY docker/nginx/nginx.conf /etc/nginx/conf.d/default.conf
COPY docker/php-fpm/start.sh /usr/local/bin/start.sh
RUN chmod a+x /usr/local/bin/start.sh
CMD ["/usr/local/bin/start.sh"]