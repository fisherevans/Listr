# https://www.howtogeek.com/devops/how-to-use-docker-to-containerise-php-and-apache/
# https://stackoverflow.com/questions/37063573/apache-docker-container-invalid-command-rewriteengine

FROM php:7.0-apache

RUN docker-php-ext-install pdo_mysql

RUN a2enmod rewrite

ADD ./src /var/www/html
