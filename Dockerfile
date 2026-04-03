FROM php:8.0-apache
RUN apt-get update 
RUN apt-get install -y sudo nano zip unzip
RUN apt-get install -y git
RUN docker-php-ext-install pdo pdo_mysql
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin/ --filename=composer
RUN a2enmod rewrite
CMD ["/bin/bash"]