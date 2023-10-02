FROM php:apache
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli
RUN docker-php-ext-install pdo_mysql
RUN apt-get update && apt-get upgrade -y
COPY 000-default.conf /etc/apache2/sites-available
RUN a2enmod headers
COPY banshee /var/www/html/
