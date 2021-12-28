FROM ubuntu
LABEL maintainer="sboulema@gmail.com"

ENV PHP_VERSION=8.1
ENV DEBIAN_FRONTEND=noninteractive 

RUN useradd ttrss

# add PHP repository
RUN apt-get update && \
  apt-get install -y software-properties-common && \
  add-apt-repository ppa:ondrej/php

# install PHP and extensions
RUN apt-get update && apt-get install -y \
  php${PHP_VERSION} php${PHP_VERSION}-fpm \
  php${PHP_VERSION}-pdo php${PHP_VERSION}-gd php${PHP_VERSION}-pgsql php${PHP_VERSION}-mbstring \
  php${PHP_VERSION}-intl php${PHP_VERSION}-xml php${PHP_VERSION}-curl \
  php${PHP_VERSION}-dom php${PHP_VERSION}-fileinfo php${PHP_VERSION}-iconv \
  php${PHP_VERSION}-posix php${PHP_VERSION}-zip \
  git nginx supervisor sudo \
  && apt-get clean && rm -rf /var/lib/apt/lists/* && mkdir -p /var/run/php

# enable PHP modules
RUN phpenmod -v ${PHP_VERSION} mbstring && \
    phpenmod -v ${PHP_VERSION} xml

# enable PHP ENV
RUN sed -i.bak 's/;clear_env = .*/clear_env = no/i' /etc/php/${PHP_VERSION}/fpm/pool.d/www.conf

# add ttrss as the only nginx site
ADD ttrss.nginx.conf /etc/nginx/sites-available/ttrss
RUN sed -i "s/PHP_VERSION/${PHP_VERSION}/" /etc/nginx/sites-available/ttrss
RUN ln -s /etc/nginx/sites-available/ttrss /etc/nginx/sites-enabled/ttrss
RUN rm /etc/nginx/sites-enabled/default

# expose only nginx HTTP port
EXPOSE 80

# install ttrss
WORKDIR /var/www
RUN rm -rf *
RUN git clone https://git.tt-rss.org/fox/tt-rss.git .

# set default settings
ENV TTRSS_SELF_URL_PATH="http://localhost"
ENV TTRSS_DB_NAME="ttrss"
ENV TTRSS_DB_USER="ttrss"
ENV TTRSS_DB_PASS="ttrss"
ENV TTRSS_DB_TYPE="pgsql"
ENV TTRSS_DB_PORT="5432"
ENV TTRSS_SESSION_COOKIE_LIFETIME="86400000"
ENV TTRSS_CACHE_DIR="cache"
ENV TTRSS_ICONS_DIR="feed-icons"
ENV TTRSS_LOCK_DIRECTORY="lock"
ENV TTRSS_PLUGINS="auth_internal"
ENV TTRSS_CONFIG_VERSION="26"
ENV TTRSS_PHP_EXECUTABLE="/usr/bin/php${PHP_VERSION}"
ENV TTRSS_LOG_DESTINATION="sql"
ENV NGINX_XACCEL_PREFIX="/tt-rss"

# set configuration to look at Docker ENV variables
#ADD config.docker.php config.php

# set permissions on directories
RUN chown www-data:www-data -R /var/www && \ 
    chmod -R 777 ${TTRSS_CACHE_DIR}/images && \
    chmod -R 777 ${TTRSS_CACHE_DIR}/upload && \
    chmod -R 777 ${TTRSS_CACHE_DIR}/export && \
    chmod -R 777 feed-icons && \
    chmod -R 777 lock

# always re-configure database with current ENV when RUNning container, then monitor all services
ADD configure-db.php /configure-db.php
ADD supervisord.conf /etc/supervisor/conf.d/supervisord.conf
RUN sed -i "s/PHP_VERSION/${PHP_VERSION}/" /etc/supervisor/conf.d/supervisord.conf
CMD php /configure-db.php && \
    sudo -E -u ttrss php update.php --update-schema=force-yes && \
    supervisord -c /etc/supervisor/conf.d/supervisord.conf