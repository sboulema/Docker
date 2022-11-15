FROM php:7.4-apache

RUN apt-get update -y \
  && apt-get install -y \
    libxml2-dev \
  && apt-get clean

COPY . /var/www/html/

EXPOSE 80
