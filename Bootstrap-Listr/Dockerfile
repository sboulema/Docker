FROM php:7.0-apache

RUN a2enmod rewrite && \
	docker-php-ext-install gettext

RUN curl -sSL https://github.com/idleberg/Bootstrap-Listr/archive/2.2.5.tar.gz | tar -xz && \
	mv Bootstrap-Listr-2.2.5/dist/* Bootstrap-Listr-2.2.5/dist/.[a-z]* /var/www/html/ && \
	rm -rf Bootstrap-Listr-2.2.5

VOLUME /var/www/html/_public
	
EXPOSE 80