FROM php:8.3-apache

WORKDIR /var/www/html

RUN apt-get update
RUN apt-get -y install apt-utils
RUN apt-get -y install nano
RUN apt-get -y install zip
RUN apt-get -y install unzip
RUN apt-get -y install git
RUN apt-get -y install curl

RUN docker-php-ext-install mysqli pdo pdo_mysql

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php composer-setup.php
RUN php -r "unlink('composer-setup.php');"
RUN mv composer.phar /usr/local/bin/composer

RUN curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.deb.sh' | bash
RUN apt-get -y install symfony-cli

RUN pecl install -f xdebug \
&& echo "zend_extension=$(find /usr/local/lib/php/extensions/ -name xdebug.so)" > /usr/local/etc/php/conf.d/xdebug.ini
RUN echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/xdebug.ini
RUN echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/xdebug.ini
RUN echo "xdebug.client_port=9003" >> /usr/local/etc/php/conf.d/xdebug.ini
RUN echo "xdebug.start_with_request=trigger" >> /usr/local/etc/php/conf.d/xdebug.ini
RUN echo "xdebug.log=/tmp/xdebug.log" >> /usr/local/etc/php/conf.d/xdebug.ini